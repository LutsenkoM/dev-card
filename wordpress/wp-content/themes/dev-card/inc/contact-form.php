<?php
/**
 * Contact form: stores every message in the database (Messages menu)
 * and sends an email notification.
 *
 * Designed for cheap/free hosting:
 * - works without JavaScript (classic POST -> redirect -> GET);
 * - the message is saved even if the host can't send email;
 * - spam protection without third-party services (honeypot, time trap, rate limit).
 *
 * @package Dev_Card
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Minimum seconds between page load and submit. Bots submit instantly.
 */
const DEV_CARD_FORM_MIN_SECONDS = 3;

/**
 * Max submissions per visitor (IP) within DEV_CARD_FORM_RATE_WINDOW.
 */
const DEV_CARD_FORM_RATE_LIMIT  = 3;
const DEV_CARD_FORM_RATE_WINDOW = 10 * MINUTE_IN_SECONDS;

/**
 * Register the "Message" post type: read-only inbox in the admin.
 */
function dev_card_register_message_post_type() {
	register_post_type(
		'dc_message',
		array(
			'labels'          => array_merge(
				dev_card_post_type_labels( 'Message', 'Messages' ),
				array( 'edit_item' => 'View Message' )
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => false, // Classic edit screen is enough for a read-only view.
			'menu_position'   => 24,
			'menu_icon'       => 'dashicons-email-alt',
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ), // Messages come only from the form.
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'dev_card_register_message_post_type' );

/**
 * Show the message itself on its admin screen.
 */
function dev_card_message_meta_box() {
	add_meta_box(
		'dev_card_message',
		'Message',
		static function ( $post ) {
			$name  = get_post_meta( $post->ID, '_dc_name', true );
			$email = get_post_meta( $post->ID, '_dc_email', true );

			printf(
				'<p><strong>From:</strong> %1$s &lt;<a href="mailto:%2$s">%2$s</a>&gt;</p><p><strong>Date:</strong> %3$s</p><hr><div style="white-space:pre-wrap">%4$s</div>',
				esc_html( $name ),
				esc_attr( $email ),
				esc_html( get_the_date( 'Y-m-d H:i', $post ) ),
				esc_html( $post->post_content )
			);
		},
		'dc_message',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_dc_message', 'dev_card_message_meta_box' );

/**
 * Add "From" column to the messages list.
 *
 * @param array<string, string> $columns List table columns.
 * @return array<string, string>
 */
function dev_card_message_columns( $columns ) {
	return array(
		'cb'       => $columns['cb'],
		'title'    => 'Subject',
		'dc_email' => 'From',
		'date'     => $columns['date'],
	);
}
add_filter( 'manage_dc_message_posts_columns', 'dev_card_message_columns' );

/**
 * Print the custom column value.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function dev_card_message_column_value( $column, $post_id ) {
	if ( 'dc_email' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_dc_name', true ) . ' <' . get_post_meta( $post_id, '_dc_email', true ) . '>' );
	}
}
add_action( 'manage_dc_message_posts_custom_column', 'dev_card_message_column_value', 10, 2 );

/**
 * Handle the form submission.
 *
 * The form posts to /wp-admin/admin-post.php with action=dev_card_contact.
 * WordPress then fires `admin_post_nopriv_{action}` for guests and
 * `admin_post_{action}` for logged-in users.
 */
function dev_card_handle_contact_form() {
	$redirect = wp_get_referer() ? wp_get_referer() : dev_card_page_url( 'contacts' );
	$redirect = remove_query_arg( array( 'contact', 'token' ), $redirect );

	// 1. CSRF protection.
	if ( ! isset( $_POST['dev_card_contact_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['dev_card_contact_nonce'] ), 'dev_card_contact' ) ) {
		dev_card_contact_redirect( $redirect, array( 'Your session has expired. Please reload the page and try again.' ) );
	}

	// 2. Honeypot: a hidden field humans never fill in. Pretend success to the bot.
	if ( ! empty( $_POST['dc_hp_check'] ) ) {
		dev_card_log_spam( 'honeypot filled' );
		dev_card_contact_redirect( $redirect );
	}

	// 3. Time trap: the timestamp is signed, so a bot can't fake it.
	$started = isset( $_POST['dc_started'] ) ? sanitize_text_field( wp_unslash( $_POST['dc_started'] ) ) : '';
	list( $time, $signature ) = array_pad( explode( '.', $started, 2 ), 2, '' );
	if ( ! hash_equals( wp_hash( $time ), $signature ) ) {
		dev_card_log_spam( 'invalid time signature' );
		dev_card_contact_redirect( $redirect );
	}
	if ( time() - (int) $time < DEV_CARD_FORM_MIN_SECONDS ) {
		dev_card_log_spam( sprintf( 'submitted too fast (%d s)', time() - (int) $time ) );
		dev_card_contact_redirect( $redirect );
	}

	// 4. Rate limit per IP. The IP is hashed and kept only in a short-lived transient.
	$rate_key = 'dc_contact_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	$attempts = (int) get_transient( $rate_key );
	if ( $attempts >= DEV_CARD_FORM_RATE_LIMIT ) {
		dev_card_contact_redirect( $redirect, array( 'Too many messages. Please try again in a few minutes.' ) );
	}

	// 5. Sanitize and validate.
	$input = array(
		'name'    => isset( $_POST['dc_name'] ) ? sanitize_text_field( wp_unslash( $_POST['dc_name'] ) ) : '',
		'email'   => isset( $_POST['dc_email'] ) ? sanitize_email( wp_unslash( $_POST['dc_email'] ) ) : '',
		'message' => isset( $_POST['dc_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['dc_message'] ) ) : '',
	);

	$errors = array();
	if ( '' === $input['name'] || mb_strlen( $input['name'] ) > 100 ) {
		$errors[] = 'Please enter your name (up to 100 characters).';
	}
	if ( ! is_email( $input['email'] ) ) {
		$errors[] = 'Please enter a valid email address.';
	}
	if ( mb_strlen( $input['message'] ) < 10 || mb_strlen( $input['message'] ) > 5000 ) {
		$errors[] = 'The message should be between 10 and 5000 characters.';
	}

	if ( $errors ) {
		dev_card_contact_redirect( $redirect, $errors, $input );
	}

	set_transient( $rate_key, $attempts + 1, DEV_CARD_FORM_RATE_WINDOW );

	// 6. Save first: the message is never lost, even if email fails.
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'dc_message',
			'post_status'  => 'private',
			'post_title'   => sprintf( 'Message from %s', $input['name'] ),
			'post_content' => $input['message'],
			'meta_input'   => array(
				'_dc_name'  => $input['name'],
				'_dc_email' => $input['email'],
			),
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		dev_card_contact_redirect( $redirect, array( 'Something went wrong. Please try again later.' ), $input );
	}

	// 7. Notify. A failure here is logged, but the visitor still sees success.
	dev_card_send_contact_notification( $input );

	dev_card_contact_redirect( $redirect );
}
add_action( 'admin_post_nopriv_dev_card_contact', 'dev_card_handle_contact_form' );
add_action( 'admin_post_dev_card_contact', 'dev_card_handle_contact_form' );

/**
 * Log why a submission was silently rejected as spam (only when WP_DEBUG_LOG is on).
 *
 * Bots get a fake "success", so without this log a false positive is invisible.
 *
 * @param string $reason Short reason, e.g. 'honeypot filled'.
 */
function dev_card_log_spam( $reason ) {
	if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		error_log( 'Dev Card contact form: rejected as spam — ' . $reason ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}

/**
 * Send the notification email to the site owner.
 *
 * @param array{name: string, email: string, message: string} $input Validated form data.
 * @return bool Whether the email was handed over to the mail server.
 */
function dev_card_send_contact_notification( $input ) {
	$to = dev_card_get_contact( 'email' );
	if ( ! $to ) {
		$to = get_option( 'admin_email' );
	}

	$subject = sprintf( '[%s] New message from %s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $input['name'] );
	$body    = sprintf(
		"Name: %s\nEmail: %s\n\n%s\n\n—\nAll messages: %s",
		$input['name'],
		$input['email'],
		$input['message'],
		admin_url( 'edit.php?post_type=dc_message' )
	);

	// Reply-To lets you answer the visitor directly from your mail client.
	$headers = array( sprintf( 'Reply-To: %s <%s>', $input['name'], $input['email'] ) );

	return wp_mail( $to, $subject, $body, $headers );
}

/**
 * Redirect back to the form (Post/Redirect/Get) and stop.
 *
 * On error the messages and the entered values are kept for 5 minutes
 * in a transient, identified by a random token in the URL.
 *
 * @param string   $url    Page to return to.
 * @param string[] $errors Error messages; empty means success.
 * @param array    $input  Entered values to refill the form.
 * @return never
 */
function dev_card_contact_redirect( $url, $errors = array(), $input = array() ) {
	if ( $errors ) {
		$token = wp_generate_password( 12, false );
		set_transient( 'dc_contact_state_' . $token, compact( 'errors', 'input' ), 5 * MINUTE_IN_SECONDS );
		$url = add_query_arg(
			array(
				'contact' => 'error',
				'token'   => $token,
			),
			$url
		);
	} else {
		$url = add_query_arg( 'contact', 'sent', $url );
	}

	// "#contact-form" scrolls the page back to the form.
	wp_safe_redirect( $url . '#contact-form' );
	exit;
}

/**
 * Get the form state for the current request (after redirect).
 *
 * @return array{status: string, errors: string[], input: array<string, string>}
 */
function dev_card_get_contact_form_state() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only display state.
	$status = isset( $_GET['contact'] ) ? sanitize_key( $_GET['contact'] ) : '';
	$token  = isset( $_GET['token'] ) ? sanitize_key( $_GET['token'] ) : '';
	// phpcs:enable

	$state = $token ? get_transient( 'dc_contact_state_' . $token ) : false;

	return array(
		'status' => $status,
		'errors' => $state['errors'] ?? array(),
		'input'  => wp_parse_args(
			$state['input'] ?? array(),
			array(
				'name'    => '',
				'email'   => '',
				'message' => '',
			)
		),
	);
}

/**
 * Value for the time-trap field: "timestamp.signature".
 *
 * @return string
 */
function dev_card_contact_started_value() {
	$time = (string) time();

	return $time . '.' . wp_hash( $time );
}
