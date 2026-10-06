<?php
/**
 * Template for the "skills" page (picked by slug: page-{slug}.php).
 *
 * @package Dev_Card
 */

get_header();

// Skills are managed in the admin: Skills -> All Skills / Groups (see inc/post-types.php).
// The first group is highlighted as the core stack.
$dev_card_skill_groups = dev_card_get_skills_by_group();

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
					<?php $is_core = ( array_key_first( $dev_card_skill_groups ) === $group ); ?>
					<div class="<?php echo esc_attr( $is_core ? 'col-12' : 'col-md-6 col-lg-4' ); ?>">
						<section class="card card-dev h-100<?php echo $is_core ? ' skill-group--core' : ''; ?>">
							<div class="card-body p-4">
								<h2 class="h5 skill-group__title"><?php echo esc_html( $group ); ?></h2>

								<ul class="skill-tags">
									<?php foreach ( $skills as $skill ) : ?>
										<li class="skill-tag"><?php echo esc_html( $skill ); ?></li>
									<?php endforeach; ?>
								</ul>
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
