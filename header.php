<?php
/**
 * Terminal Theme — header.php
 *
 * Opens the HTML document, runs required WordPress hooks, and renders
 * the terminal chrome: title bar and navigation menu bar.
 *
 * Tags opened here are closed in footer.php:
 *   .terminal-window, <body>, <html>
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> data-theme="<?php echo esc_attr( get_theme_mod( 'terminal_theme_color_mode', 'dark' ) ); ?>">
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main">
	<?php esc_html_e( 'Skip to content', 'terminal-theme' ); ?>
</a>

<div class="terminal-window">

	<?php /* ── Title bar ──────────────────────────────────────────────── */ ?>
	<div class="terminal-titlebar" aria-hidden="true">
		<div class="terminal-dots">
			<span class="terminal-dot terminal-dot--close"></span>
			<span class="terminal-dot terminal-dot--min"></span>
			<span class="terminal-dot terminal-dot--expand"></span>
		</div>
		<span class="terminal-title">
			<?php
			$terminal_title_default = 'terminal &mdash; ' . esc_html( get_bloginfo( 'name' ) );
			echo wp_kses( get_theme_mod( 'terminal_theme_title_bar_text', $terminal_title_default ), array() );
			?>
		</span>
	</div>

	<?php /* ── Navigation menu bar ────────────────────────────────────── */ ?>
	<nav class="terminal-menubar" aria-label="<?php esc_attr_e( 'Primary menu', 'terminal-theme' ); ?>">
		<ul class="terminal-menubar__home">
			<li>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Home', 'terminal-theme' ); ?>"><?php esc_html_e( '~', 'terminal-theme' ); ?></a>
			</li>
		</ul>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_class'     => 'terminal-menubar__list',
				'container'      => false,
				'fallback_cb'    => 'terminal_theme_nav_fallback',
				'depth'          => 3,
			)
		);
		?>
	</nav>

	<?php /* .terminal-window closes in footer.php */ ?>
