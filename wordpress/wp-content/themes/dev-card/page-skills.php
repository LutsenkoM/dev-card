<?php
/**
 * Template for the "skills" page (picked by slug: page-{slug}.php).
 *
 * @package Dev_Card
 */

get_header();

// Temporary hardcoded data. Will be moved to the admin panel in step 7.
// Format: group => list of skills. The first group is highlighted as the core stack.
$dev_card_skill_groups = array(
	'Core'                 => array( 'React', 'TypeScript', 'JavaScript (ES6+)', 'Next.js (App Router)', 'HTML5', 'CSS3' ),
	'Data & State'         => array( 'GraphQL (Apollo Client)', 'REST', 'WebSocket', 'SignalR', 'Redux Toolkit', 'React Query', 'Zustand', 'React Hook Form', 'Caching strategies' ),
	'UI & Accessibility'   => array( 'Tailwind CSS', 'Material UI', 'SASS', 'CSS Grid & Flexbox', 'Design systems', 'Responsive layouts', 'Semantic HTML', 'ARIA', 'WCAG' ),
	'Testing & Tooling'    => array( 'Jest', 'React Testing Library', 'Cypress', 'Vite', 'webpack', 'Turbo', 'pnpm monorepo', 'Storybook', 'ESLint', 'Prettier' ),
	'AI Tools'             => array( 'Claude Code', 'GitHub Copilot', 'Codex' ),
	'Cloud & Workflow'     => array( 'GCP', 'Firebase', 'Git (GitLab / Bitbucket / GitHub)', 'Azure DevOps', 'Jira', 'Figma', 'Agile / Scrum', 'Code review' ),
	'Also'                 => array( 'Angular / AngularJS', 'Node.js', 'WordPress', 'Drupal' ),
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
