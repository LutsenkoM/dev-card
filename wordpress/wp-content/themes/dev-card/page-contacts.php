<?php
/**
 * Template for the "contacts" page (picked by slug: page-{slug}.php).
 *
 * @package Dev_Card
 */

get_header();

// Temporary hardcoded data. Will be moved to the admin panel in step 7.
$dev_card_email    = 'hello@example.com';
$dev_card_location = 'Krakow, Poland';
$dev_card_contacts = array(
	array(
		'label' => 'LinkedIn',
		'value' => 'linkedin.com/in/maksym-lutsenko',
		'url'   => 'https://linkedin.com/in/maksym-lutsenko',
	),
	array(
		'label' => 'GitHub',
		'value' => 'github.com/LutsenkoM',
		'url'   => 'https://github.com/LutsenkoM',
	),
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
						<li>
							<span class="contact-list__label">Email</span>
							<?php
							// antispambot() encodes the address into HTML entities to confuse spam bots.
							// Its output is already safe for HTML, and esc_url() would break the entities,
							// so we sanitize the email before encoding instead of escaping after.
							$dev_card_email_encoded = antispambot( sanitize_email( $dev_card_email ) );
							?>
							<a href="mailto:<?php echo $dev_card_email_encoded; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php echo $dev_card_email_encoded; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						</li>
						<?php foreach ( $dev_card_contacts as $contact ) : ?>
							<li>
								<span class="contact-list__label"><?php echo esc_html( $contact['label'] ); ?></span>
								<a href="<?php echo esc_url( $contact['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $contact['value'] ); ?></a>
							</li>
						<?php endforeach; ?>
						<li>
							<span class="contact-list__label">Location</span>
							<span class="contact-list__value"><?php echo esc_html( $dev_card_location ); ?></span>
						</li>
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
