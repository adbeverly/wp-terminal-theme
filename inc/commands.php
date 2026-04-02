<?php
/**
 * Terminal command registry.
 *
 * Builds the command data object and passes it to JavaScript via
 * wp_localize_script(). All content is live from WordPress — no hardcoded
 * portfolio data. Commands map to standard WordPress query functions.
 *
 * Plugins and child themes can extend commands via the filter:
 *   add_filter( 'terminal_theme_commands', function( $commands ) { ... } );
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build and localize the terminal command registry.
 * Hooked late on wp_enqueue_scripts so the script handle exists.
 */
function terminal_theme_localize_commands() {

	if ( ! is_front_page() ) {
		return;
	}

	$show_dates   = get_theme_mod( 'terminal_theme_ls_show_dates', true );
	$posts_limit  = absint( get_theme_mod( 'terminal_theme_posts_per_page', 10 ) );
	$boot_lines   = sanitize_textarea_field( get_theme_mod( 'terminal_theme_boot_lines', "initializing...\nsystem ready." ) );

	// ── ls pages/ ─────────────────────────────────────────────────────────

	$pages     = get_pages( array( 'sort_column' => 'menu_order' ) );
	$pages_out = array();

	foreach ( $pages as $page ) {
		$pages_out[] = array(
			'title' => esc_html( $page->post_title ),
			'slug'  => esc_html( $page->post_name ),
			'url'   => esc_url( get_permalink( $page->ID ) ),
		);
	}

	// ── ls posts/ ─────────────────────────────────────────────────────────

	$posts_query = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $posts_limit,
			'no_found_rows'  => true,
		)
	);

	$posts_out = array();

	if ( $posts_query->have_posts() ) {
		while ( $posts_query->have_posts() ) {
			$posts_query->the_post();
			$entry = array(
				'title' => esc_html( get_the_title() ),
				'slug'  => esc_html( get_post_field( 'post_name' ) ),
				'url'   => esc_url( get_permalink() ),
			);
			if ( $show_dates ) {
				$entry['date'] = esc_html( get_the_date( 'Y-m-d' ) );
			}
			$posts_out[] = $entry;
		}
		wp_reset_postdata();
	}

	// ── ls categories/ ───────────────────────────────────────────────────

	$categories  = get_categories( array( 'hide_empty' => false ) );
	$cats_out    = array();

	foreach ( $categories as $cat ) {
		$cats_out[] = array(
			'name'  => esc_html( $cat->name ),
			'slug'  => esc_html( $cat->slug ),
			'url'   => esc_url( get_category_link( $cat->term_id ) ),
			'count' => absint( $cat->count ),
		);
	}

	// ── ls tags/ ─────────────────────────────────────────────────────────

	$tags     = get_tags( array( 'hide_empty' => false ) );
	$tags_out = array();

	foreach ( $tags as $tag ) {
		$tags_out[] = array(
			'name'  => esc_html( $tag->name ),
			'slug'  => esc_html( $tag->slug ),
			'url'   => esc_url( get_tag_link( $tag->term_id ) ),
			'count' => absint( $tag->count ),
		);
	}

	// ── whoami ───────────────────────────────────────────────────────────

	$whoami = array(
		'name'        => esc_html( get_bloginfo( 'name' ) ),
		'description' => esc_html( get_bloginfo( 'description' ) ),
		'url'         => esc_url( home_url( '/' ) ),
	);

	// ── Build data object ─────────────────────────────────────────────────

	$data = array(
		'pages'      => $pages_out,
		'posts'      => $posts_out,
		'categories' => $cats_out,
		'tags'       => $tags_out,
		'whoami'     => $whoami,
		'homeUrl'    => esc_url( home_url( '/' ) ),
		'bootLines'  => array_filter( array_map( 'sanitize_text_field', explode( "\n", $boot_lines ) ) ),
		'showDates'  => (bool) $show_dates,
		'i18n'       => array(
			'help'        => esc_html__( 'available commands:', 'terminal-theme' ),
			'notFound'    => esc_html__( '// command not found. type help for available commands.', 'terminal-theme' ),
			'cleared'     => esc_html__( '// terminal cleared.', 'terminal-theme' ),
			'noPages'     => esc_html__( '// no pages found.', 'terminal-theme' ),
			'noPosts'     => esc_html__( '// no posts found.', 'terminal-theme' ),
			'noResults'   => esc_html__( '// no results found.', 'terminal-theme' ),
			'searching'   => esc_html__( '// searching...', 'terminal-theme' ),
			'themeSet'    => esc_html__( '// theme updated.', 'terminal-theme' ),
			'opening'     => esc_html__( '// opening...', 'terminal-theme' ),
		),
	);

	/**
	 * Filter the terminal command data before it is passed to JavaScript.
	 *
	 * @param array $data The full command data object.
	 */
	$data = apply_filters( 'terminal_theme_commands', $data );

	wp_localize_script( 'terminal-theme-commands', 'terminalThemeData', $data );
}
add_action( 'wp_enqueue_scripts', 'terminal_theme_localize_commands', 20 );
