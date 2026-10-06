<?php
/**
 * Template for the site front page (Home).
 * Takes priority over every other template when showing the front page.
 *
 * @package Dev_Card
 */

get_header();

// Temporary hardcoded data. Will be moved to the admin panel in step 7.
$dev_card_highlights = array(
	array(
		'slug'  => 'profile',
		'title' => 'Profile',
		'text'  => 'Who I am, my background and the experience behind my work.',
	),
	array(
		'slug'  => 'skills',
		'title' => 'Skills',
		'text'  => 'Languages, frameworks and tools I use to build things.',
	),
	array(
		'slug'  => 'contacts',
		'title' => 'Contacts',
		'text'  => 'Have a project or a question? Let\'s get in touch.',
	),
);

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
  stack: [<span class="tok-str">'React'</span>, <span class="tok-str">'TypeScript'</span>, <span class="tok-str">'Next.js'</span>],
  experience: <span class="tok-str">'8+ years'</span>,
  location: <span class="tok-str">'Krakow, PL'</span>,
  coffee: <span class="tok-num">Infinity</span>,
  available: <span class="tok-key">true</span>,
};</code></pre>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="home-links py-5">
		<div class="container">
			<div class="row g-4">
				<?php foreach ( $dev_card_highlights as $index => $item ) : ?>
					<div class="col-md-4">
						<a class="card card-dev h-100 home-link" href="<?php echo esc_url( dev_card_page_url( $item['slug'] ) ); ?>">
							<div class="card-body p-4">
								<p class="card-dev__meta"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></p>
								<h2 class="h4 card-title"><?php echo esc_html( $item['title'] ); ?> <span class="home-link__arrow">&rarr;</span></h2>
								<p class="card-text text-secondary mb-0"><?php echo esc_html( $item['text'] ); ?></p>
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
