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
					<div class="card card-dev">
						<div class="card-body p-4 p-lg-5">
							<h2 class="h4 mb-2">Send a message</h2>
							<p class="text-secondary mb-0">The contact form will be added in step 8.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</article>

	<?php
endwhile;

get_footer();
