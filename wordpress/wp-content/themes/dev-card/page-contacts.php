<?php
/**
 * Template for the "contacts" page (picked by slug: page-{slug}.php).
 *
 * @package Dev_Card
 */

get_header();

// Contact data comes from Appearance -> Customize -> Contacts (see inc/customizer.php).
$dev_card_email    = dev_card_get_contact( 'email' );
$dev_card_phone    = dev_card_get_contact( 'phone' );
$dev_card_location = dev_card_get_contact( 'location' );

// Link-type contacts: label => URL. Empty values are skipped below.
$dev_card_links = array(
	'LinkedIn' => dev_card_get_contact( 'linkedin' ),
	'Telegram' => dev_card_get_contact( 'telegram' ),
);

while ( have_posts() ) :
	the_post();
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php get_template_part( 'template-parts/page-hero' ); ?>

		<div class="container py-5">
			<div class="row g-5">
				<div class="col-lg-5">
					<div class="entry-content mb-4">
						<?php the_content(); ?>
					</div>

					<ul class="contact-list">
						<?php if ( $dev_card_email ) : ?>
							<li>
								<span class="contact-list__label">Email</span>
								<?php
								// antispambot() encodes the address into HTML entities to confuse spam bots.
								// Its output is already safe for HTML, and esc_url() would break the entities.
								// The value was sanitized with sanitize_email() when saved in the Customizer.
								$dev_card_email_encoded = antispambot( $dev_card_email );
								?>
								<a href="mailto:<?php echo $dev_card_email_encoded; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php echo $dev_card_email_encoded; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
							</li>
						<?php endif; ?>

						<?php if ( $dev_card_phone ) : ?>
							<li>
								<span class="contact-list__label">Phone</span>
								<a href="<?php echo esc_url( 'tel:' . dev_card_phone_href( $dev_card_phone ) ); ?>"><?php echo esc_html( $dev_card_phone ); ?></a>
							</li>
						<?php endif; ?>

						<?php foreach ( array_filter( $dev_card_links ) as $label => $url ) : ?>
							<li>
								<span class="contact-list__label"><?php echo esc_html( $label ); ?></span>
								<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( dev_card_pretty_url( $url ) ); ?></a>
							</li>
						<?php endforeach; ?>

						<?php if ( $dev_card_location ) : ?>
							<li>
								<span class="contact-list__label">Location</span>
								<span class="contact-list__value"><?php echo esc_html( $dev_card_location ); ?></span>
							</li>
						<?php endif; ?>
					</ul>
				</div>

				<div class="col-lg-7">
					<?php $dev_card_form = dev_card_get_contact_form_state(); ?>
					<div class="card card-dev" id="contact-form">
						<div class="card-body p-4 p-lg-5">
							<h2 class="h4 mb-4">Send a message</h2>

							<?php if ( 'sent' === $dev_card_form['status'] ) : ?>
								<div class="alert alert-success" role="status">
									Thank you! Your message has been sent. I will get back to you soon.
								</div>
							<?php elseif ( $dev_card_form['errors'] ) : ?>
								<div class="alert alert-danger" role="alert">
									<ul class="mb-0 ps-3">
										<?php foreach ( $dev_card_form['errors'] as $error ) : ?>
											<li><?php echo esc_html( $error ); ?></li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>

							<form class="contact-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
								<input type="hidden" name="action" value="dev_card_contact">
								<input type="hidden" name="dc_started" value="<?php echo esc_attr( dev_card_contact_started_value() ); ?>">
								<?php wp_nonce_field( 'dev_card_contact', 'dev_card_contact_nonce' ); ?>

								<?php
								// Honeypot: hidden from people, but bots fill in every field.
								// The name must not look like a real field (website, url, company...),
								// otherwise browser autofill and password managers fill it for real users.
								?>
								<div class="contact-form__hp" aria-hidden="true">
									<label for="dc-hp">Leave this field empty</label>
									<input type="text" id="dc-hp" name="dc_hp_check" value="" tabindex="-1" autocomplete="new-password" data-lpignore="true" data-1p-ignore data-bwignore>
								</div>

								<div class="row g-3">
									<div class="col-md-6">
										<label class="form-label" for="dc-name">Name</label>
										<input class="form-control" type="text" id="dc-name" name="dc_name" required maxlength="100" autocomplete="name" value="<?php echo esc_attr( $dev_card_form['input']['name'] ); ?>">
									</div>
									<div class="col-md-6">
										<label class="form-label" for="dc-email">Email</label>
										<input class="form-control" type="email" id="dc-email" name="dc_email" required autocomplete="email" value="<?php echo esc_attr( $dev_card_form['input']['email'] ); ?>">
									</div>
									<div class="col-12">
										<label class="form-label" for="dc-message">Message</label>
										<textarea class="form-control" id="dc-message" name="dc_message" rows="6" required minlength="10" maxlength="5000"><?php echo esc_textarea( $dev_card_form['input']['message'] ); ?></textarea>
									</div>
									<div class="col-12">
										<button class="btn btn-primary btn-lg" type="submit">Send message</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</article>

	<?php
endwhile;

get_footer();
