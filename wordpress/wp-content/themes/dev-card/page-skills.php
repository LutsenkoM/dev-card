<?php
/**
 * Template for the "skills" page (picked by slug: page-{slug}.php).
 *
 * @package Dev_Card
 */

get_header();

// Temporary hardcoded data. Will be moved to the admin panel in step 7.
// Format: group => [ skill name => level in percent ].
$dev_card_skill_groups = array(
	'Backend'  => array(
		'PHP'       => 90,
		'WordPress' => 85,
		'MySQL'     => 75,
		'REST API'  => 80,
	),
	'Frontend' => array(
		'HTML & CSS' => 90,
		'JavaScript' => 80,
		'React'      => 65,
		'Bootstrap'  => 85,
	),
	'Tools'    => array(
		'Git'      => 85,
		'Docker'   => 70,
		'Linux'    => 70,
		'PhpStorm' => 90,
	),
);

while ( have_posts() ) :
	the_post();
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php get_template_part( 'template-parts/page-hero' ); ?>

		<div class="container py-5">
			<?php if ( get_the_content() ) : ?>
				<div class="entry-content mb-5">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>

			<div class="row g-4">
				<?php foreach ( $dev_card_skill_groups as $group => $skills ) : ?>
					<div class="col-md-6 col-lg-4">
						<section class="card card-dev h-100">
							<div class="card-body p-4">
								<h2 class="h5 skill-group__title"><?php echo esc_html( $group ); ?></h2>

								<?php foreach ( $skills as $name => $level ) : ?>
									<div class="skill">
										<div class="skill__head">
											<span><?php echo esc_html( $name ); ?></span>
											<span class="skill__level"><?php echo esc_html( $level ); ?>%</span>
										</div>
										<div class="progress skill__bar" role="progressbar" aria-label="<?php echo esc_attr( $name ); ?>" aria-valuenow="<?php echo esc_attr( $level ); ?>" aria-valuemin="0" aria-valuemax="100">
											<div class="progress-bar" style="width: <?php echo esc_attr( $level ); ?>%"></div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</section>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</article>

	<?php
endwhile;

get_footer();
