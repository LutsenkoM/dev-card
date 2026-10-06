<?php
/**
 * Outgoing email via SMTP.
 *
 * Free and cheap hosts often disable PHP mail(), or mail sent with it lands in spam.
 * Sending through a real SMTP server (Gmail, Brevo, your domain's mailbox...) fixes both.
 *
 * Credentials never live in the theme (it's in a public Git repo).
 * Define them in wp-config.php on each server:
 *
 *     define( 'DEV_CARD_SMTP_HOST', 'smtp.example.com' );
 *     define( 'DEV_CARD_SMTP_PORT', 587 );
 *     define( 'DEV_CARD_SMTP_SECURE', 'tls' );       // 'tls', 'ssl' or '' (none).
 *     define( 'DEV_CARD_SMTP_USER', 'user@example.com' );
 *     define( 'DEV_CARD_SMTP_PASS', 'app-password' );
 *     define( 'DEV_CARD_SMTP_FROM', 'user@example.com' );
 *
 * Without DEV_CARD_SMTP_HOST, WordPress falls back to PHP mail().
 *
 * @package Dev_Card
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Configure PHPMailer (the library behind wp_mail()) to use SMTP.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer Mailer instance, passed by reference.
 */
function dev_card_configure_smtp( $phpmailer ) {
	if ( ! defined( 'DEV_CARD_SMTP_HOST' ) || ! DEV_CARD_SMTP_HOST ) {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host       = DEV_CARD_SMTP_HOST;
	$phpmailer->Port       = defined( 'DEV_CARD_SMTP_PORT' ) ? (int) DEV_CARD_SMTP_PORT : 587;
	$phpmailer->SMTPSecure = defined( 'DEV_CARD_SMTP_SECURE' ) ? DEV_CARD_SMTP_SECURE : 'tls';
	$phpmailer->SMTPAutoTLS = '' !== $phpmailer->SMTPSecure;

	if ( defined( 'DEV_CARD_SMTP_USER' ) && DEV_CARD_SMTP_USER ) {
		$phpmailer->SMTPAuth = true;
		$phpmailer->Username = DEV_CARD_SMTP_USER;
		$phpmailer->Password = defined( 'DEV_CARD_SMTP_PASS' ) ? DEV_CARD_SMTP_PASS : '';
	}
}
add_action( 'phpmailer_init', 'dev_card_configure_smtp' );

/**
 * Set the sender address.
 *
 * wp_mail() sets From to "wordpress@{site domain}" *before* `phpmailer_init`,
 * and fails on invalid domains like "localhost". The `wp_mail_from` filter runs earlier.
 * Also, most SMTP providers reject mail whose From differs from the authenticated account.
 *
 * @param string $from Default sender address.
 * @return string
 */
function dev_card_mail_from( $from ) {
	return defined( 'DEV_CARD_SMTP_FROM' ) && DEV_CARD_SMTP_FROM ? DEV_CARD_SMTP_FROM : $from;
}
add_filter( 'wp_mail_from', 'dev_card_mail_from' );

/**
 * Use the site name as the sender name instead of "WordPress".
 *
 * @return string
 */
function dev_card_mail_from_name() {
	return wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
}
add_filter( 'wp_mail_from_name', 'dev_card_mail_from_name' );

/**
 * Log email failures to wp-content/debug.log, so problems on the server are visible.
 *
 * @param WP_Error $error Error from wp_mail().
 */
function dev_card_log_mail_error( $error ) {
	if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		error_log( 'Dev Card mail error: ' . $error->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}
add_action( 'wp_mail_failed', 'dev_card_log_mail_error' );
