<?php
/**
 * Terminal Theme — page.php
 *
 * Template for individual pages. Wraps Gutenberg block content
 * in the terminal chrome.
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

		</article>

		<?php comments_template(); ?>

	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
