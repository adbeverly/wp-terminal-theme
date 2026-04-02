<?php
/**
 * Terminal Theme — footer.php
 *
 * Closes the terminal content area, renders the footer bar and mode toggle,
 * closes .terminal-window, fires wp_footer(), and closes the document.
 *
 * Tags closed here were opened in header.php:
 *   .terminal-window, <body>, <html>
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$terminal_active_mode = esc_attr( get_theme_mod( 'terminal_theme_color_mode', 'dark' ) );
$terminal_show_toggle = get_theme_mod( 'terminal_theme_show_toggle', true );
?>

	<footer class="terminal-footer">

		<span class="terminal-statusbar">
			<?php echo esc_html( get_bloginfo( 'name' ) ); ?> &mdash; <?php esc_html_e( 'ready', 'terminal-theme' ); ?>
		</span>

		<?php if ( $terminal_show_toggle ) : ?>
		<div class="terminal-mode-toggle" role="group" aria-label="<?php esc_attr_e( 'Color mode', 'terminal-theme' ); ?>">

			<button
				class="terminal-mode-toggle__btn"
				data-mode="dark"
				aria-pressed="<?php echo ( 'dark' === $terminal_active_mode ) ? 'true' : 'false'; ?>"
			><?php esc_html_e( 'dark', 'terminal-theme' ); ?></button>

			<span class="terminal-mode-toggle__separator" aria-hidden="true">&middot;</span>

			<button
				class="terminal-mode-toggle__btn"
				data-mode="light"
				aria-pressed="<?php echo ( 'light' === $terminal_active_mode ) ? 'true' : 'false'; ?>"
			><?php esc_html_e( 'light', 'terminal-theme' ); ?></button>

			<span class="terminal-mode-toggle__separator" aria-hidden="true">&middot;</span>

			<button
				class="terminal-mode-toggle__btn"
				data-mode="groovy"
				aria-pressed="<?php echo ( 'groovy' === $terminal_active_mode ) ? 'true' : 'false'; ?>"
			><?php esc_html_e( 'groovy', 'terminal-theme' ); ?></button>

		</div>
		<?php endif; ?>

	</footer>

</div><!-- .terminal-window -->

<?php wp_footer(); ?>

</body>
</html>
