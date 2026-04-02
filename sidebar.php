<?php
/**
 * Terminal Theme — sidebar.php
 *
 * The sidebar widget area. Registered but hidden by default.
 * Loaded via get_sidebar() in templates that opt in.
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'terminal-sidebar' ) ) {
	return;
}
?>

<aside class="terminal-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'terminal-theme' ); ?>">
	<?php dynamic_sidebar( 'terminal-sidebar' ); ?>
</aside>
