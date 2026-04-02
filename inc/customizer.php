<?php
/**
 * Customizer settings registration.
 *
 * Registers sections, settings, and controls for the Terminal Theme options.
 * Settings are stored in wp_options via the Customizer API and read
 * with get_theme_mod() throughout the theme.
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Customizer sections, settings, and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function terminal_theme_customize_register( $wp_customize ) {

	// ── Section ───────────────────────────────────────────────────────────

	$wp_customize->add_section(
		'terminal_theme_options',
		array(
			'title'    => __( 'Terminal Settings', 'terminal-theme' ),
			'priority' => 30,
		)
	);

	// ── Default color mode ────────────────────────────────────────────────

	$wp_customize->add_setting(
		'terminal_theme_color_mode',
		array(
			'default'           => 'dark',
			'sanitize_callback' => 'terminal_theme_sanitize_color_mode',
		)
	);

	$wp_customize->add_control(
		'terminal_theme_color_mode',
		array(
			'label'   => __( 'Default color mode', 'terminal-theme' ),
			'section' => 'terminal_theme_options',
			'type'    => 'select',
			'choices' => array(
				'dark'   => __( 'Dark', 'terminal-theme' ),
				'light'  => __( 'Light', 'terminal-theme' ),
				'groovy' => __( 'Groovy', 'terminal-theme' ),
			),
		)
	);

	// ── Show visitor mode toggle ──────────────────────────────────────────

	$wp_customize->add_setting(
		'terminal_theme_show_toggle',
		array(
			'default'           => true,
			'sanitize_callback' => 'terminal_theme_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'terminal_theme_show_toggle',
		array(
			'label'   => __( 'Show color mode toggle to visitors', 'terminal-theme' ),
			'section' => 'terminal_theme_options',
			'type'    => 'checkbox',
		)
	);

	// ── Show command prompt ───────────────────────────────────────────────

	$wp_customize->add_setting(
		'terminal_theme_show_prompt',
		array(
			'default'           => true,
			'sanitize_callback' => 'terminal_theme_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'terminal_theme_show_prompt',
		array(
			'label'   => __( 'Show interactive command prompt', 'terminal-theme' ),
			'section' => 'terminal_theme_options',
			'type'    => 'checkbox',
		)
	);

	// ── Boot sequence lines ───────────────────────────────────────────────

	$wp_customize->add_setting(
		'terminal_theme_boot_lines',
		array(
			'default'           => "initializing...\nsystem ready.\n// navigate via the menu above, or type a command. try: help",
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);

	$wp_customize->add_control(
		'terminal_theme_boot_lines',
		array(
			'label'       => __( 'Boot sequence lines', 'terminal-theme' ),
			'description' => __( 'One line per entry. Displayed in sequence on page load.', 'terminal-theme' ),
			'section'     => 'terminal_theme_options',
			'type'        => 'textarea',
		)
	);

	// ── Title bar text ────────────────────────────────────────────────────

	$wp_customize->add_setting(
		'terminal_theme_title_bar_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'terminal_theme_title_bar_text',
		array(
			'label'       => __( 'Title bar text', 'terminal-theme' ),
			'description' => __( 'Overrides the default "terminal — Site Name" label.', 'terminal-theme' ),
			'section'     => 'terminal_theme_options',
			'type'        => 'text',
		)
	);

	// ── Show post dates in ls ─────────────────────────────────────────────

	$wp_customize->add_setting(
		'terminal_theme_ls_show_dates',
		array(
			'default'           => true,
			'sanitize_callback' => 'terminal_theme_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'terminal_theme_ls_show_dates',
		array(
			'label'   => __( 'Show post dates in ls posts/', 'terminal-theme' ),
			'section' => 'terminal_theme_options',
			'type'    => 'checkbox',
		)
	);

	// ── Posts per page in terminal ────────────────────────────────────────

	$wp_customize->add_setting(
		'terminal_theme_posts_per_page',
		array(
			'default'           => 10,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'terminal_theme_posts_per_page',
		array(
			'label'   => __( 'Posts per page in ls posts/', 'terminal-theme' ),
			'section' => 'terminal_theme_options',
			'type'    => 'number',
		)
	);
}
add_action( 'customize_register', 'terminal_theme_customize_register' );

// ── Sanitization callbacks ─────────────────────────────────────────────────

/**
 * Sanitize the color mode select field.
 * Only allows the three valid mode values.
 *
 * @param string $value Raw input value.
 * @return string Sanitized value, defaults to 'dark'.
 */
function terminal_theme_sanitize_color_mode( $value ) {
	$valid = array( 'dark', 'light', 'groovy' );
	return in_array( $value, $valid, true ) ? $value : 'dark';
}

/**
 * Sanitize checkbox fields.
 * Returns true for any truthy value, false otherwise.
 *
 * @param mixed $value Raw input value.
 * @return bool
 */
function terminal_theme_sanitize_checkbox( $value ) {
	return (bool) $value;
}
