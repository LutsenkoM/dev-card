<?php
/**
 * Template for the site front page (Home).
 * Takes priority over every other template when showing the front page.
 *
 * @package Dev_Card
 */

get_header();

// All data below comes from the admin: pages, skills, Profile details, Customizer.
$dev_card_pages        = array_filter(
	array(
		'profile'  => get_page_by_path( 'profile' ),
		'skills'   => get_page_by_path( 'skills' ),
		'contacts' => get_page_by_path( 'contacts' ),
	)
);
$dev_card_skill_groups = dev_card_get_skills_by_group();
$dev_card_core_skills  = (array) reset( $dev_card_skill_groups );
$dev_card_stack        = array_slice( $dev_card_core_skills, 0, 3 );
$dev_card_skills_total = array_sum( array_map( 'count', $dev_card_skill_groups ) );
$dev_card_facts        = dev_card_get_profile_facts();
$dev_card_experience   = $dev_card_facts['Experience'] ?? '';
$dev_card_status       = $dev_card_facts['Status'] ?? '';
$dev_card_location     = dev_card_get_contact( 'location' );

// Marquee: skills from the first two groups.
$dev_card_marquee = array_merge( array(), ...array_slice( array_values( $dev_card_skill_groups ), 0, 2 ) );

// Split the name into words to animate them one by one.
$dev_card_name_words = preg_split( '/\s+/', trim( get_bloginfo( 'name' ) ) );

// Text running around the rotating badge.
$dev_card_badge_text = strtoupper( $dev_card_status ? $dev_card_status : get_bloginfo( 'description' ) );

while ( have_posts() ) :
	the_post();
	?>

	<section class="hero">
		<div class="container">
			<div class="hero__grid">
				<div class="hero__text" data-stagger>
					<?php if ( $dev_card_status ) : ?>
						<p class="pill reveal"><span class="pill__dot"></span><?php echo esc_html( $dev_card_status ); ?></p>
					<?php endif; ?>

					<p class="hero__hello reveal">// hello, world — I'm</p>

					<h1 class="hero__title">
						<?php foreach ( $dev_card_name_words as $index => $word ) : ?>
							<span class="hero__word reveal<?php echo count( $dev_card_name_words ) - 1 === $index ? ' text-gradient' : ''; ?>"><?php echo esc_html( $word ); ?></span>
						<?php endforeach; ?>
					</h1>

					<?php if ( get_bloginfo( 'description' ) ) : ?>
						<p class="hero__role reveal">
							<span class="hero__role-line" aria-hidden="true"></span>
							<?php bloginfo( 'description' ); ?>
						</p>
					<?php endif; ?>

					<div class="hero__intro reveal">
						<?php the_content(); ?>
					</div>

					<div class="hero__actions reveal">
						<?php if ( isset( $dev_card_pages['profile'] ) ) : ?>
							<a class="btn btn-primary btn-lg" href="<?php echo esc_url( get_permalink( $dev_card_pages['profile'] ) ); ?>" data-magnetic>
								View profile <span aria-hidden="true">&rarr;</span>
							</a>
						<?php endif; ?>
						<?php if ( isset( $dev_card_pages['contacts'] ) ) : ?>
							<a class="btn btn-ghost btn-lg" href="<?php echo esc_url( get_permalink( $dev_card_pages['contacts'] ) ); ?>" data-magnetic>Get in touch</a>
						<?php endif; ?>
					</div>
				</div>

				<div class="hero__visual" aria-hidden="true">
					<div class="hero__visual-inner" data-parallax="-0.08">
						<div class="code-window" data-tilt="10">
							<div class="code-window__bar">
								<span></span><span></span><span></span>
								<em>developer.ts</em>
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
};<span class="code-window__caret"></span></code></pre>
						</div>

						<?php foreach ( $dev_card_stack as $index => $skill ) : ?>
							<span class="float-chip float-chip--<?php echo esc_attr( $index + 1 ); ?>" data-parallax="<?php echo esc_attr( 0.04 * ( $index + 1 ) ); ?>">
								<?php echo esc_html( $skill ); ?>
							</span>
						<?php endforeach; ?>

						<?php if ( $dev_card_badge_text ) : ?>
							<svg class="spin-badge" viewBox="0 0 200 200">
								<defs>
									<path id="badge-circle" d="M100,100 m-78,0 a78,78 0 1,1 156,0 a78,78 0 1,1 -156,0"></path>
								</defs>
								<text>
									<textPath href="#badge-circle" textLength="485"><?php echo esc_html( $dev_card_badge_text . ' ✦ ' . $dev_card_badge_text . ' ✦ ' ); ?></textPath>
								</text>
								<text class="spin-badge__center" x="100" y="112" text-anchor="middle">&lt;/&gt;</text>
							</svg>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<a class="scroll-hint" href="#overview" aria-label="Scroll to overview">
				<span>Scroll</span><i></i>
			</a>
		</div>
	</section>

	<?php if ( $dev_card_marquee ) : ?>
		<section class="marquee" aria-label="Tech stack">
			<div class="marquee__track">
				<?php for ( $copy = 0; $copy < 2; $copy++ ) : // Two identical copies make the loop seamless. ?>
					<ul class="marquee__list"<?php echo $copy ? ' aria-hidden="true"' : ''; ?>>
						<?php foreach ( $dev_card_marquee as $index => $skill ) : ?>
							<li class="<?php echo esc_attr( $index % 2 ? 'is-outline' : '' ); ?>"><?php echo esc_html( $skill ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endfor; ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="bento-section" id="overview">
		<div class="container">
			<header class="section-head reveal">
				<p class="section-head__eyebrow">// 01 — overview</p>
				<h2 class="section-head__title">A quick look<span class="text-accent">.</span></h2>
			</header>

			<div class="bento" data-stagger>
				<?php if ( isset( $dev_card_pages['profile'] ) ) : ?>
					<a class="bento__item bento__item--profile card-glass spotlight reveal" href="<?php echo esc_url( get_permalink( $dev_card_pages['profile'] ) ); ?>">
						<span class="bento__index">01</span>
						<div class="bento__body">
							<h3 class="bento__title"><?php echo esc_html( get_the_title( $dev_card_pages['profile'] ) ); ?></h3>
							<?php if ( has_excerpt( $dev_card_pages['profile'] ) ) : ?>
								<p class="bento__text"><?php echo esc_html( get_the_excerpt( $dev_card_pages['profile'] ) ); ?></p>
							<?php endif; ?>
						</div>
						<?php if ( has_post_thumbnail( $dev_card_pages['profile'] ) ) : ?>
							<?php echo get_the_post_thumbnail( $dev_card_pages['profile'], 'medium_large', array( 'class' => 'bento__photo' ) ); ?>
						<?php else : ?>
							<span class="bento__monogram" aria-hidden="true"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span>
						<?php endif; ?>
						<span class="bento__arrow" aria-hidden="true">&nearr;</span>
					</a>
				<?php endif; ?>

				<?php if ( $dev_card_experience ) : ?>
					<div class="bento__item bento__item--stat card-glass spotlight reveal">
						<span class="bento__label">Experience</span>
						<span class="bento__stat"><?php echo esc_html( $dev_card_experience ); ?></span>
					</div>
				<?php endif; ?>

				<?php if ( $dev_card_location ) : ?>
					<div class="bento__item bento__item--stat bento__item--location card-glass spotlight reveal">
						<span class="bento__label">Based in</span>
						<span class="bento__stat bento__stat--sm"><?php echo esc_html( $dev_card_location ); ?></span>
						<span class="bento__pulse" aria-hidden="true"></span>
					</div>
				<?php endif; ?>

				<?php if ( isset( $dev_card_pages['skills'] ) ) : ?>
					<a class="bento__item bento__item--skills card-glass spotlight reveal" href="<?php echo esc_url( get_permalink( $dev_card_pages['skills'] ) ); ?>">
						<span class="bento__index">02</span>
						<div class="bento__body">
							<h3 class="bento__title"><?php echo esc_html( get_the_title( $dev_card_pages['skills'] ) ); ?></h3>
							<?php if ( has_excerpt( $dev_card_pages['skills'] ) ) : ?>
								<p class="bento__text"><?php echo esc_html( get_the_excerpt( $dev_card_pages['skills'] ) ); ?></p>
							<?php endif; ?>
						</div>
						<?php if ( $dev_card_skills_total ) : ?>
							<span class="bento__big-number"><?php echo esc_html( $dev_card_skills_total ); ?><small>technologies</small></span>
						<?php endif; ?>
						<span class="bento__arrow" aria-hidden="true">&nearr;</span>
					</a>
				<?php endif; ?>

				<?php if ( isset( $dev_card_pages['contacts'] ) ) : ?>
					<a class="bento__item bento__item--contacts spotlight reveal" href="<?php echo esc_url( get_permalink( $dev_card_pages['contacts'] ) ); ?>">
						<span class="bento__index">03</span>
						<div class="bento__body">
							<h3 class="bento__title bento__title--xl">Let's build something great together</h3>
							<?php if ( has_excerpt( $dev_card_pages['contacts'] ) ) : ?>
								<p class="bento__text"><?php echo esc_html( get_the_excerpt( $dev_card_pages['contacts'] ) ); ?></p>
							<?php endif; ?>
						</div>
						<span class="bento__arrow bento__arrow--xl" aria-hidden="true">&nearr;</span>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();
