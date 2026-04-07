<?php
/**
 * Admin settings page for API Playground Block.
 *
 * Adds a settings page under Settings > API Playground where the site owner
 * can enter their Anthropic API key, choose a default model, and set a rate limit.
 *
 * The API key is stored in wp_options and is NEVER output to the browser.
 *
 * @package api-playground
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class API_Playground_Settings
 *
 * Handles the admin settings page. All methods are static, so we call them
 * as API_Playground_Settings::method_name() rather than creating an object.
 */
class API_Playground_Settings {

	/**
	 * Register all hooks for this class.
	 *
	 * Called once from api-playground.php. This is the only place hooks are
	 * registered — keeps everything organised and easy to find.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Add the settings page to the WordPress admin menu.
	 *
	 * add_options_page() adds a sub-item under the Settings menu.
	 * It takes: page title, menu title, required capability, menu slug, callback.
	 */
	public static function add_settings_page() {
		add_options_page(
			__( 'API Playground Settings', 'api-playground' ),
			__( 'API Playground', 'api-playground' ),
			'manage_options',
			'api-playground',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Register the settings, sections, and fields with WordPress.
	 *
	 * WordPress Settings API works in three layers:
	 * 1. register_setting()  — declares an option name and how to sanitize it
	 * 2. add_settings_section() — a visual group on the page
	 * 3. add_settings_field()  — one row: label + input
	 */
	public static function register_settings() {

		// -- API Key ----------------------------------------------------------

		register_setting(
			'api_playground_settings',       // Option group (ties fields to the save action).
			'api_playground_api_key',        // Option name in wp_options.
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		// -- Model ------------------------------------------------------------

		register_setting(
			'api_playground_settings',
			'api_playground_model',
			array(
				'sanitize_callback' => array( __CLASS__, 'sanitize_model' ),
				'default'           => 'claude-haiku-4-5-20251001',
			)
		);

		// -- Rate limit -------------------------------------------------------

		register_setting(
			'api_playground_settings',
			'api_playground_rate_limit',
			array(
				'sanitize_callback' => 'absint',
				'default'           => 20,
			)
		);

		// -- Section ----------------------------------------------------------

		add_settings_section(
			'api_playground_main',
			__( 'API Configuration', 'api-playground' ),
			'__return_false',   // No description paragraph needed.
			'api-playground'
		);

		// -- Fields -----------------------------------------------------------

		add_settings_field(
			'api_playground_api_key',
			__( 'Anthropic API Key', 'api-playground' ),
			array( __CLASS__, 'render_api_key_field' ),
			'api-playground',
			'api_playground_main'
		);

		add_settings_field(
			'api_playground_model',
			__( 'Default Model', 'api-playground' ),
			array( __CLASS__, 'render_model_field' ),
			'api-playground',
			'api_playground_main'
		);

		add_settings_field(
			'api_playground_rate_limit',
			__( 'Rate Limit', 'api-playground' ),
			array( __CLASS__, 'render_rate_limit_field' ),
			'api-playground',
			'api_playground_main'
		);
	}

	/**
	 * Render the full settings page HTML.
	 *
	 * settings_fields() outputs the hidden nonce and option group fields
	 * that WordPress needs to verify and save the form submission.
	 * do_settings_sections() outputs all registered sections and fields.
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<?php if ( isset( $_GET['settings-updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Settings saved.', 'api-playground' ); ?></p>
				</div>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'api_playground_settings' );
				do_settings_sections( 'api-playground' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the API key input field.
	 *
	 * The stored key is shown as placeholder dots (password field) so it
	 * is not visible in the admin — but we never echo the actual value
	 * into a visible text field. The value attribute uses esc_attr() which
	 * escapes special characters like quotes that could break the HTML.
	 */
	public static function render_api_key_field() {
		$value = get_option( 'api_playground_api_key', '' );
		?>
		<input
			type="password"
			id="api_playground_api_key"
			name="api_playground_api_key"
			value="<?php echo esc_attr( $value ); ?>"
			class="regular-text"
			autocomplete="off"
		/>
		<p class="description">
			<?php esc_html_e( 'Your Anthropic API key. Never exposed to site visitors.', 'api-playground' ); ?>
		</p>
		<?php
	}

	/**
	 * Render the model select field.
	 */
	public static function render_model_field() {
		$value = get_option( 'api_playground_model', 'claude-haiku-4-5-20251001' );
		$models = array(
			'claude-haiku-4-5-20251001' => __( 'Claude Haiku 4.5 (fast, recommended)', 'api-playground' ),
			'claude-sonnet-4-6'         => __( 'Claude Sonnet 4.6 (more capable)', 'api-playground' ),
		);
		?>
		<select id="api_playground_model" name="api_playground_model">
			<?php foreach ( $models as $model_id => $label ) : ?>
				<option value="<?php echo esc_attr( $model_id ); ?>" <?php selected( $value, $model_id ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Render the rate limit input field.
	 */
	public static function render_rate_limit_field() {
		$value = absint( get_option( 'api_playground_rate_limit', 20 ) );
		?>
		<input
			type="number"
			id="api_playground_rate_limit"
			name="api_playground_rate_limit"
			value="<?php echo esc_attr( $value ); ?>"
			min="1"
			max="1000"
			class="small-text"
		/>
		<p class="description">
			<?php esc_html_e( 'Maximum requests per visitor IP address per hour.', 'api-playground' ); ?>
		</p>
		<?php
	}

	/**
	 * Sanitize the model setting.
	 *
	 * Only accept values from the known-good list. If someone submits
	 * an unexpected value (e.g. via a crafted request), fall back to
	 * the default. This pattern — validate against a whitelist — is the
	 * same one used in the theme's sanitize_color_mode() function.
	 *
	 * @param string $value The submitted value.
	 * @return string A safe, whitelisted model ID.
	 */
	public static function sanitize_model( $value ) {
		$allowed = array( 'claude-haiku-4-5-20251001', 'claude-sonnet-4-6' );
		if ( in_array( $value, $allowed, true ) ) {
			return $value;
		}
		return 'claude-haiku-4-5-20251001';
	}
}
