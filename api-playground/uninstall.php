<?php
/**
 * Uninstall routine for API Playground Block.
 *
 * WordPress runs this file automatically when the plugin is deleted
 * (not just deactivated — deleted) from the Plugins screen.
 *
 * It removes everything the plugin stored in the database so the site
 * is left clean. This is a WordPress.org requirement for plugin submissions.
 *
 * @package api-playground
 */

// WordPress sets this constant before running uninstall.php.
// If it's not set, someone is accessing this file directly — bail out.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove all plugin options from the wp_options table.
delete_option( 'api_playground_api_key' );
delete_option( 'api_playground_model' );
delete_option( 'api_playground_rate_limit' );

// Remove all rate-limiting transients. Transients are temporary database
// entries with an expiry time — we use them to track requests per IP.
// The wildcard pattern deletes all transients whose key starts with our prefix.
global $wpdb;

$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
		$wpdb->esc_like( '_transient_api_playground_rate_' ) . '%',
		$wpdb->esc_like( '_transient_timeout_api_playground_rate_' ) . '%'
	)
);
