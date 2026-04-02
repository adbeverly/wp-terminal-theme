<?php
/**
 * Script and stylesheet enqueuing.
 *
 * Uses wp_enqueue_style() and wp_enqueue_script() — never hardcoded
 * <link> or <script> tags. WordPress manages load order and deduplication.
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue theme styles and scripts.
 */
function terminal_theme_enqueue_assets() {

	$version = wp_get_theme()->get( 'Version' );
	$uri     = get_template_directory_uri();

	// Color mode variables — loaded first so terminal.css can inherit them.
	wp_enqueue_style(
		'terminal-theme-modes',
		$uri . '/assets/css/modes.css',
		array(),
		$version
	);

	// Core terminal styles.
	wp_enqueue_style(
		'terminal-theme-style',
		$uri . '/assets/css/terminal.css',
		array( 'terminal-theme-modes' ),
		$version
	);

	// Boot sequence — only on the front page.
	if ( is_front_page() ) {
		wp_enqueue_script(
			'terminal-theme-boot',
			$uri . '/assets/js/boot.js',
			array(),
			$version,
			array( 'strategy' => 'defer' )
		);
	}

	// Command parser — only on the front page.
	if ( is_front_page() ) {
		wp_enqueue_script(
			'terminal-theme-commands',
			$uri . '/assets/js/commands.js',
			array( 'terminal-theme-boot' ),
			$version,
			array( 'strategy' => 'defer' )
		);
	}

	// Color mode toggle — everywhere.
	wp_enqueue_script(
		'terminal-theme-modes',
		$uri . '/assets/js/modes.js',
		array(),
		$version,
		array( 'strategy' => 'defer' )
	);
}
add_action( 'wp_enqueue_scripts', 'terminal_theme_enqueue_assets' );
