<?php
/**
 * Template for the site front page (Home).
 * Takes priority over every other template when showing the front page.
 *
 * @package Dev_Card
 */

get_header();

// Cards link to these pages; titles and texts (page excerpts) are edited in Pages.
$dev_card_highlights = array_filter( array_map( 'get_page_by_path', array( 'profile', 'skills', 'contacts' ) ) );

// "Code window" values are collected from data managed elsewhere in the admin.
$dev_card_skill_groups = dev_card_get_skills_by_group();
$dev_card_stack        = array_slice( (array) reset( $dev_card_skill_groups ), 0, 3 ); // First 3 core skills.
$dev_card_experience   = dev_card_get_profile_facts()['Experience'] ?? '';
$dev_card_location     = dev_card_get_contact( 'location' );

while ( have_posts() ) :
	the_post();
	?>

	<section class="home-hero">
		<div class="container">
			<div class="row align-items-center g-5">
				<div class="col-lg-7">
					<p class="home-hero__hello">// hello, world</p>

					<h1 class="home-hero__title">
						Hi, I'm <span class="text-accent"><?php bloginfo( 'name' ); ?></span>
					</h1>

					<?php if ( get_bloginfo( 'description' ) ) : ?>
						<p class="home-hero__role"><?php bloginfo( 'description' ); ?></p>
					<?php endif; ?>

					<div class="home-hero__intro">
						<?php the_content(); ?>
					</div>

					<div class="d-flex flex-wrap gap-3 mt-4">
						<a class="btn btn-primary btn-lg" href="<?php echo esc_url( dev_card_page_url( 'profile' ) ); ?>">View profile</a>
						<a class="btn btn-outline-primary btn-lg" href="<?php echo esc_url( dev_card_page_url( 'contacts' ) ); ?>">Get in touch</a>
					</div>
				</div>

				<div class="col-lg-5 d-none d-lg-block">
					<div class="code-window" aria-hidden="true">
						<div class="code-window__bar">
							<span></span><span></span><span></span>
							<em>developer.js</em>
						</div>
<pre class="code-window__body"><code><span class="tok-key">const</span> developer = {
  name: <span class="tok-str">'<?php echo esc_html( get_bloginfo( 'name' ) ); ?>'</span>,
<?php if ( $dev_card_stack ) : ?>
  stack: [<?php echo implode( ', ', array_map( static fn( $skill ) => '<span class="tok-str">\'' . esc_html( $skill ) . '\'</span>', $dev_card_stack ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>],
<?php endif; ?>
<?php if ( $dev_card_experience ) : ?>
  experience: <span class="tok-str">'<?php echo esc_html( $dev_card_experience ); ?>'</span>,
<?php endif; ?>
<?php if ( $dev_card_location ) : ?>
  location: <span class="tok-str">'<?php echo esc_html( $dev_card_location ); ?>'</span>,
<?php endif; ?>
  coffee: <span class="tok-num">Infinity</span>,
};</code></pre>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="home-links py-5">
		<div class="container">
			<div class="row g-4">
				<?php foreach ( array_values( $dev_card_highlights ) as $index => $highlight ) : ?>
					<div class="col-md-4">
						<a class="card card-dev h-100 home-link" href="<?php echo esc_url( get_permalink( $highlight ) ); ?>">
							<div class="card-body p-4">
								<p class="card-dev__meta"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></p>
								<h2 class="h4 card-title"><?php echo esc_html( get_the_title( $highlight ) ); ?> <span class="home-link__arrow">&rarr;</span></h2>
								<?php if ( has_excerpt( $highlight ) ) : ?>
									<p class="card-text text-secondary mb-0"><?php echo esc_html( get_the_excerpt( $highlight ) ); ?></p>
								<?php endif; ?>
							</div>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();
