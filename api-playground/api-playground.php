<?php
/**
 * Plugin Name:  API Playground Block
 * Plugin URI:   https://github.com/adbeverly/wp-terminal-theme
 * Description:  Embed a configurable AI chat interface powered by the Anthropic API.
 * Version:      1.0.0
 * Requires at least: 6.3
 * Requires PHP: 8.0
 * Author:       Ashley Beverly
 * License:      GPL-2.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  api-playground
 * Domain Path:  /languages
 *
 * @package api-playground
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version constant.
 * Used when enqueuing scripts and styles so browsers always load the latest files.
 */
define( 'API_PLAYGROUND_VERSION', '1.0.0' );

/**
 * Plugin directory path.
 * Used to require PHP files. Always ends with a trailing slash.
 */
define( 'API_PLAYGROUND_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 * Used when enqueuing scripts and styles. Always ends with a trailing slash.
 */
define( 'API_PLAYGROUND_URL', plugin_dir_url( __FILE__ ) );

// Load the settings page (admin UI + API key storage).
require_once API_PLAYGROUND_DIR . 'includes/class-settings.php';

// Load the REST API endpoint (Anthropic proxy + rate limiting).
require_once API_PLAYGROUND_DIR . 'includes/class-rest-api.php';

// Load the block registration (registers the Gutenberg block with WordPress).
require_once API_PLAYGROUND_DIR . 'includes/class-block.php';

// Boot each class by calling its init() method, which registers its hooks.
API_Playground_Settings::init();
API_Playground_REST_API::init();
API_Playground_Block::init();
