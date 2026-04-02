<?php
/**
 * Terminal Theme — 404.php
 *
 * Displayed when no content matches the requested URL.
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="terminal-content" tabindex="-1">

	<div class="terminal-boot">
		<span class="terminal-boot-line terminal-boot-line--accent">
			<?php esc_html_e( 'error: 404', 'terminal-theme' ); ?>
		</span>
		<span class="terminal-boot-line">
			<?php esc_html_e( '// the file or directory you requested does not exist.', 'terminal-theme' ); ?>
		</span>
		<span class="terminal-boot-line">
			<?php esc_html_e( '// check the path and try again.', 'terminal-theme' ); ?>
		</span>
	</div>

	<nav class="error-actions" aria-label="<?php esc_attr_e( '404 navigation', 'terminal-theme' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( '> cd ~', 'terminal-theme' ); ?>
		</a>
	</nav>

	<div class="error-search">
		<p class="terminal-output-line terminal-output-line--comment">
			<?php esc_html_e( '// or try a search:', 'terminal-theme' ); ?>
		</p>
		<?php get_search_form(); ?>
	</div>

</main>

<?php get_footer(); ?>
