<?php
/**
 * Template for the "profile" page (picked by slug: page-{slug}.php).
 *
 * @package Dev_Card
 */

get_header();

// Managed in the admin: Pages -> Profile -> "Profile details" box (see inc/meta-boxes.php).
$dev_card_facts     = dev_card_parse_pairs( get_post_meta( get_queried_object_id(), '_dc_facts', true ) );
$dev_card_languages = dev_card_parse_pairs( get_post_meta( get_queried_object_id(), '_dc_languages', true ) );

// Managed in the admin: Experience / Education menus (see inc/post-types.php, inc/meta-boxes.php).
$dev_card_experience = dev_card_get_entries( 'dc_experience' );
$dev_card_education  = dev_card_get_entries( 'dc_education' );

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

						<?php if ( $dev_card_facts ) : ?>
							<dl class="profile-card__facts">
								<?php foreach ( $dev_card_facts as $label => $value ) : ?>
									<div>
										<dt><?php echo esc_html( $label ); ?></dt>
										<dd><?php echo esc_html( $value ); ?></dd>
									</div>
								<?php endforeach; ?>
							</dl>
						<?php endif; ?>

						<?php if ( $dev_card_languages ) : ?>
							<h2 class="profile-card__subtitle">Languages</h2>
							<dl class="profile-card__facts">
								<?php foreach ( $dev_card_languages as $language => $level ) : ?>
									<div>
										<dt><?php echo esc_html( $language ); ?></dt>
										<dd><?php echo esc_html( $level ); ?></dd>
									</div>
								<?php endforeach; ?>
							</dl>
						<?php endif; ?>
					</div>
				</aside>

				<div class="col-lg-8">
					<div class="entry-content">
						<?php the_content(); ?>
					</div>

					<?php if ( $dev_card_experience ) : ?>
						<h2 class="section-title mt-5">Experience</h2>
						<ol class="timeline">
							<?php foreach ( $dev_card_experience as $job ) : ?>
								<li class="timeline__item">
									<p class="timeline__period"><?php echo esc_html( $job['period'] ); ?></p>
									<h3 class="timeline__role">
										<?php echo esc_html( $job['title'] ); ?>
										<?php if ( $job['company'] ) : ?>
											<span class="text-secondary">@ <?php echo esc_html( $job['company'] ); ?></span>
										<?php endif; ?>
									</h3>
									<?php if ( $job['description'] ) : ?>
										<p class="timeline__text"><?php echo esc_html( $job['description'] ); ?></p>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ol>
					<?php endif; ?>

					<?php if ( $dev_card_education ) : ?>
						<h2 class="section-title mt-4">Education</h2>
						<ol class="timeline">
							<?php foreach ( $dev_card_education as $study ) : ?>
								<li class="timeline__item">
									<p class="timeline__period"><?php echo esc_html( $study['period'] ); ?></p>
									<h3 class="timeline__role"><?php echo esc_html( $study['title'] ); ?></h3>
									<p class="timeline__text"><?php echo esc_html( $study['school'] ); ?></p>
								</li>
							<?php endforeach; ?>
						</ol>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</article>

	<?php
endwhile;

get_footer();
