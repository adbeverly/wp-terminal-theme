<?php
/**
 * REST API endpoint for API Playground Block.
 *
 * Registers POST /wp-json/api-playground/v1/chat.
 *
 * Flow:
 *   1. Check rate limit — prevents API abuse.
 *   2. Retrieve API key from wp_options (never from the request).
 *   3. Send request to Anthropic API server-side.
 *   4. Return the reply as JSON.
 *
 * The Anthropic API key is NEVER included in any response to the browser.
 *
 * @package api-playground
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class API_Playground_REST_API
 */
class API_Playground_REST_API {

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Register the chat REST route.
	 *
	 * register_rest_route() takes a namespace, a path, and an array of options.
	 * 'args' declares the expected request parameters and how to sanitize them —
	 * WordPress validates and sanitizes before the callback runs.
	 */
	public static function register_routes() {
		register_rest_route(
			'api-playground/v1',
			'/chat',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'handle_chat' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'message'       => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'history'       => array(
						'default' => array(),
					),
					'system_prompt' => array(
						'default'           => '',
						'sanitize_callback' => 'sanitize_textarea_field',
					),
					'max_tokens'    => array(
						'default'           => 300,
						'sanitize_callback' => 'absint',
					),
					'temperature'   => array(
						'default' => 0.7,
					),
				),
			)
		);
	}

	/**
	 * Handle an incoming chat request.
	 *
	 * @param WP_REST_Request $request The incoming request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function handle_chat( WP_REST_Request $request ) {

		// -- 1. Check rate limit ---------------------------------------------
		$rate_check = self::check_rate_limit();
		if ( is_wp_error( $rate_check ) ) {
			return $rate_check;
		}

		// -- 2. Get API key --------------------------------------------------
		// Retrieved server-side only. Never sent to the browser.
		$api_key = get_option( 'api_playground_api_key', '' );
		if ( empty( $api_key ) ) {
			return new WP_Error(
				'no_api_key',
				__( 'API key not configured. Visit Settings &rsaquo; API Playground.', 'api-playground' ),
				array( 'status' => 500 )
			);
		}

		// -- 3. Build the Anthropic request ----------------------------------
		$message       = $request->get_param( 'message' );
		$history       = $request->get_param( 'history' );
		$system_prompt = $request->get_param( 'system_prompt' );
		$max_tokens    = $request->get_param( 'max_tokens' );
		$temperature   = (float) $request->get_param( 'temperature' );
		$model         = get_option( 'api_playground_model', 'claude-haiku-4-5-20251001' );

		// Build the messages array from conversation history + current message.
		$messages = array();

		if ( is_array( $history ) ) {
			foreach ( $history as $turn ) {
				if (
					isset( $turn['role'], $turn['content'] ) &&
					in_array( $turn['role'], array( 'user', 'assistant' ), true )
				) {
					$messages[] = array(
						'role'    => sanitize_text_field( $turn['role'] ),
						'content' => sanitize_textarea_field( $turn['content'] ),
					);
				}
			}
		}

		$messages[] = array(
			'role'    => 'user',
			'content' => $message,
		);

		$max_tokens  = max( 1, min( 2048, $max_tokens ) );
		$temperature = max( 0.0, min( 1.0, $temperature ) );

		$body = array(
			'model'       => $model,
			'max_tokens'  => $max_tokens,
			'temperature' => $temperature,
			'messages'    => $messages,
		);

		if ( ! empty( $system_prompt ) ) {
			$body['system'] = $system_prompt;
		}

		$response = wp_remote_post(
			'https://api.anthropic.com/v1/messages',
			array(
				'timeout' => 30,
				'headers' => array(
					'x-api-key'         => $api_key,
					'anthropic-version' => '2023-06-01',
					'content-type'      => 'application/json',
				),
				'body'    => wp_json_encode( $body ),
			)
		);

		// -- 4. Handle the Anthropic response --------------------------------
		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'api_connection_error',
				__( 'Could not reach the API. Please try again.', 'api-playground' ),
				array( 'status' => 502 )
			);
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body_raw    = wp_remote_retrieve_body( $response );
		$data        = json_decode( $body_raw, true );

		if ( 200 !== (int) $status_code || ! isset( $data['content'][0]['text'] ) ) {
			return new WP_Error(
				'api_error',
				__( 'Unexpected API response. Please try again.', 'api-playground' ),
				array( 'status' => 502 )
			);
		}

		return rest_ensure_response(
			array(
				'reply' => $data['content'][0]['text'],
			)
		);
	}

	/**
	 * Check the rate limit for the current visitor's IP address.
	 *
	 * @return true|WP_Error True if under limit, WP_Error if exceeded.
	 */
	private static function check_rate_limit() {
		$limit = absint( get_option( 'api_playground_rate_limit', 20 ) );
		if ( 0 === $limit ) {
			return true;
		}

		$ip     = self::get_client_ip();
		$bucket = (int) floor( time() / HOUR_IN_SECONDS );
		$key    = 'api_playground_rate_' . md5( $ip . $bucket );
		$count  = (int) get_transient( $key );

		if ( $count >= $limit ) {
			return new WP_Error(
				'rate_limit_exceeded',
				__( 'Too many requests. Please try again in an hour.', 'api-playground' ),
				array( 'status' => 429 )
			);
		}

		set_transient( $key, $count + 1, 2 * HOUR_IN_SECONDS );

		return true;
	}

	/**
	 * Get the real client IP address.
	 *
	 * @return string The client IP address.
	 */
	private static function get_client_ip() {
		$headers = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_X_FORWARDED_FOR',
			'REMOTE_ADDR',
		);

		foreach ( $headers as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				$ip = sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) );
				if ( str_contains( $ip, ',' ) ) {
					$ip = trim( explode( ',', $ip )[0] );
				}
				return $ip;
			}
		}

		return '0.0.0.0';
	}
}
