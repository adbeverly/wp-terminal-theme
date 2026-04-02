<?php
/**
 * Terminal Theme — archive.php
 *
 * Template for category, tag, author, and date archive pages.
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
			if ( is_category() ) {
				/* translators: %s: category name */
				printf( esc_html__( 'ls categories/%s', 'terminal-theme' ), single_cat_title( '', false ) );
			} elseif ( is_tag() ) {
				/* translators: %s: tag name */
				printf( esc_html__( 'ls tags/%s', 'terminal-theme' ), single_tag_title( '', false ) );
			} elseif ( is_author() ) {
				/* translators: %s: author name */
				printf( esc_html__( 'ls author/%s', 'terminal-theme' ), get_the_author() );
			} elseif ( is_year() ) {
				/* translators: %s: year, e.g. 2024 */
				printf( esc_html__( 'ls posts/%s', 'terminal-theme' ), get_the_date( 'Y' ) );
			} elseif ( is_month() ) {
				/* translators: %s: year/month, e.g. 2024/01 */
				printf( esc_html__( 'ls posts/%s', 'terminal-theme' ), get_the_date( 'Y/m' ) );
			} else {
				esc_html_e( 'ls posts/', 'terminal-theme' );
			}
			?>
		</h1>
		<?php the_archive_description( '<p class="archive-description">', '</p>' ); ?>
	</header>

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
				</article>

			<?php endwhile; ?>
		</div>

		<?php the_posts_pagination(); ?>

	<?php else : ?>

		<p class="terminal-output-line terminal-output-line--comment">
			<?php esc_html_e( '// no posts found.', 'terminal-theme' ); ?>
		</p>

	<?php endif; ?>

</main>

<?php get_footer(); ?>
