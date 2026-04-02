<?php
/**
 * Theme support declarations and navigation menu registration.
 *
 * Hooked to after_setup_theme so WordPress core is ready when these run.
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme features and navigation menus.
 */
function terminal_theme_setup() {

	/*
	 * Let WordPress manage the document title.
	 * Without this, the theme would need to hardcode <title> in header.php.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Add RSS feed links to <head> automatically.
	 */
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Load default block styles so core blocks look reasonable without extra CSS.
	 */
	add_theme_support( 'wp-block-styles' );

	/*
	 * Enable wide and full-width alignment options in the block editor.
	 */
	add_theme_support( 'align-wide' );

	/*
	 * Register editor styles so the block editor matches the front end.
	 * The file itself is built in Step 20.
	 */
	add_editor_style( 'assets/css/editor.css' );

	/*
	 * Enable featured images (post thumbnails) on posts and pages.
	 */
	add_theme_support( 'post-thumbnails' );

	/*
	 * Output HTML5 markup for the elements listed below instead of older XHTML-style output.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	/*
	 * Register the Primary Menu location.
	 * The site owner assigns a menu here via Appearance → Menus.
	 * header.php renders it with wp_nav_menu().
	 */
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'terminal-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'terminal_theme_setup' );

/**
 * Register the terminal sidebar widget area.
 */
function terminal_theme_register_sidebars() {
	register_sidebar(
		array(
			'name'          => __( 'Terminal Sidebar', 'terminal-theme' ),
			'id'            => 'terminal-sidebar',
			'description'   => __( 'Widgets added here appear in the sidebar. Hidden by default — enable in a child theme or via custom CSS.', 'terminal-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'terminal_theme_register_sidebars' );
