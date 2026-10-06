<?php
/**
 * Template for the "profile" page (picked by slug: page-{slug}.php).
 *
 * @package Dev_Card
 */

get_header();

// Temporary hardcoded data. Will be moved to the admin panel in step 7.
$dev_card_facts = array(
	'Location'   => 'Ukraine',
	'Experience' => '5+ years',
	'Languages'  => 'Ukrainian, English',
	'Status'     => 'Open to offers',
);

$dev_card_experience = array(
	array(
		'period'  => '2022 — now',
		'role'    => 'Senior Developer',
		'company' => 'Company Name',
		'text'    => 'Short description of responsibilities and achievements.',
	),
	array(
		'period'  => '2019 — 2022',
		'role'    => 'Web Developer',
		'company' => 'Another Company',
		'text'    => 'Short description of responsibilities and achievements.',
	),
);

while ( have_posts() ) :
	the_post();
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php get_template_part( 'template-parts/page-hero' ); ?>

		<div class="container py-5">
			<div class="row g-5">
				<aside class="col-lg-4">
					<div class="profile-card">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'medium_large', array( 'class' => 'profile-card__photo' ) ); ?>
						<?php else : ?>
							<div class="profile-card__photo profile-card__photo--placeholder" aria-hidden="true">
								<?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?>
							</div>
						<?php endif; ?>

						<dl class="profile-card__facts">
							<?php foreach ( $dev_card_facts as $label => $value ) : ?>
								<div>
									<dt><?php echo esc_html( $label ); ?></dt>
									<dd><?php echo esc_html( $value ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					</div>
				</aside>

				<div class="col-lg-8">
					<div class="entry-content">
						<?php the_content(); ?>
					</div>

					<h2 class="section-title mt-5">Experience</h2>
					<ol class="timeline">
						<?php foreach ( $dev_card_experience as $job ) : ?>
							<li class="timeline__item">
								<p class="timeline__period"><?php echo esc_html( $job['period'] ); ?></p>
								<h3 class="timeline__role">
									<?php echo esc_html( $job['role'] ); ?>
									<span class="text-secondary">@ <?php echo esc_html( $job['company'] ); ?></span>
								</h3>
								<p class="timeline__text"><?php echo esc_html( $job['text'] ); ?></p>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</div>
		</div>
	</article>

	<?php
endwhile;

get_footer();
