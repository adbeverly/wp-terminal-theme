<?php
/**
 * Terminal Theme — functions.php
 *
 * Bootstraps the theme by loading feature files from inc/.
 * WordPress automatically loads this file when the theme is active.
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/theme-support.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/commands.php';
