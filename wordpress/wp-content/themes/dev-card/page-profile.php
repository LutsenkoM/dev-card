<?php
/**
 * Template for the "profile" page (picked by slug: page-{slug}.php).
 *
 * @package Dev_Card
 */

get_header();

// Temporary hardcoded data. Will be moved to the admin panel in step 7.
$dev_card_facts = array(
	'Location'   => 'Krakow, Poland',
	'Experience' => '8+ years',
	'Focus'      => 'React · Next.js · TS',
	'Work'       => 'Remote / hybrid / on-site',
	'Status'     => 'Available immediately',
);

$dev_card_languages = array(
	'English'   => 'B2',
	'Polish'    => 'A2',
	'Ukrainian' => 'Native',
);

$dev_card_experience = array(
	array(
		'period'  => '06/2019 — 04/2026',
		'role'    => 'Frontend Engineer',
		'company' => 'eKreative',
		'text'    => 'Software development company, international clients — remote. Clinical research, healthcare, insurance and e-commerce products.',
	),
	array(
		'period'  => '08/2017 — 06/2019',
		'role'    => 'CMS Developer',
		'company' => 'eKreative',
		'text'    => 'Developed, customised and maintained websites using WordPress and Drupal.',
	),
);

$dev_card_education = array(
	array(
		'period' => '01/2023 — 06/2025',
		'degree' => 'M.Sc., Computer Systems',
		'school' => 'ISMA University of Applied Sciences, Riga, Latvia',
	),
	array(
		'period' => '09/2009 — 01/2014',
		'degree' => 'M.Sc., Energy & Automation',
		'school' => 'National University of Life and Environmental Sciences of Ukraine, Kyiv',
	),
	array(
		'period' => '09/2005 — 06/2009',
		'degree' => 'Junior Specialist, Software Development',
		'school' => 'Cherkasy State Business College',
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

						<h2 class="profile-card__subtitle">Languages</h2>
						<dl class="profile-card__facts">
							<?php foreach ( $dev_card_languages as $language => $level ) : ?>
								<div>
									<dt><?php echo esc_html( $language ); ?></dt>
									<dd><?php echo esc_html( $level ); ?></dd>
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

					<h2 class="section-title mt-4">Education</h2>
					<ol class="timeline">
						<?php foreach ( $dev_card_education as $study ) : ?>
							<li class="timeline__item">
								<p class="timeline__period"><?php echo esc_html( $study['period'] ); ?></p>
								<h3 class="timeline__role"><?php echo esc_html( $study['degree'] ); ?></h3>
								<p class="timeline__text"><?php echo esc_html( $study['school'] ); ?></p>
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
