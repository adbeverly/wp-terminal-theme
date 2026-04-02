<?php
/**
 * Terminal Theme — search.php
 *
 * Template for search results pages.
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="terminal-content" tabindex="-1">

	<header class="archive-header">
		<h1 class="archive-title">
			<?php
			printf(
				/* translators: %s: search term */
				esc_html__( 'search %s', 'terminal-theme' ),
				'<span class="search-term">' . esc_html( get_search_query() ) . '</span>'
			);
			?>
		</h1>
	</header>

	<?php get_search_form(); ?>

	<?php if ( have_posts() ) : ?>

		<div class="post-list">
			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-list__item' ); ?>>
					<span class="post-list__date">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							<?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?>
						</time>
					</span>
					<a class="post-list__title" href="<?php the_permalink(); ?>">
						<?php the_title(); ?>
					</a>
					<?php if ( get_the_excerpt() ) : ?>
						<p class="post-list__excerpt"><?php the_excerpt(); ?></p>
					<?php endif; ?>
				</article>

			<?php endwhile; ?>
		</div>

		<?php the_posts_pagination(); ?>

	<?php else : ?>

		<p class="terminal-output-line terminal-output-line--comment">
			<?php
			printf(
				/* translators: %s: search term */
				esc_html__( '// no results for "%s".', 'terminal-theme' ),
				esc_html( get_search_query() )
			);
			?>
		</p>

	<?php endif; ?>

</main>

<?php get_footer(); ?>
