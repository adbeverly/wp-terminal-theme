<?php
/**
 * Terminal Theme — single.php
 *
 * Template for individual blog posts.
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="terminal-content" tabindex="-1">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<div class="entry-meta">
					<span class="entry-date">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							<?php echo esc_html( get_the_date() ); ?>
						</time>
					</span>
					<span class="entry-author">
						<?php
						printf(
							/* translators: %s: author display name */
							esc_html__( '// %s', 'terminal-theme' ),
							esc_html( get_the_author() )
						);
						?>
					</span>
				</div>
			</header>

			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<nav class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'terminal-theme' ) . '</span>',
						'after'  => '</nav>',
					)
				);
				?>
			</div>

			<footer class="entry-footer">
				<?php
				$categories = get_the_category_list( ', ' );
				if ( $categories ) {
					printf(
						'<span class="entry-categories">// cat: %s</span>',
						wp_kses_post( $categories )
					);
				}

				$tags = get_the_tag_list( '', ', ' );
				if ( $tags ) {
					printf(
						'<span class="entry-tags">// tags: %s</span>',
						wp_kses_post( $tags )
					);
				}
				?>
			</footer>

		</article>

		<nav class="post-navigation" aria-label="<?php esc_attr_e( 'Post navigation', 'terminal-theme' ); ?>">
			<?php
			the_post_navigation(
				array(
					'prev_text' => '&larr; %title',
					'next_text' => '%title &rarr;',
				)
			);
			?>
		</nav>

		<?php comments_template(); ?>

	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
