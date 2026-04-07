<?php
/**
 * Block registration for API Playground Block.
 *
 * Registers the Gutenberg block using block.json as the source of truth for
 * the block's name, attributes, supports, and asset handles. The render
 * callback generates the HTML visitors see on the frontend.
 *
 * @package api-playground
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class API_Playground_Block
 */
class API_Playground_Block {

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_block' ) );
	}

	/**
	 * Register the block type with WordPress.
	 *
	 * Passing the path to block.json (rather than the block name) tells
	 * WordPress to read all block config — name, attributes, supports,
	 * script handles — from that file. We only need to supply the
	 * render_callback here because that is PHP-only and can't live in JSON.
	 */
	public static function register_block() {
		register_block_type(
			API_PLAYGROUND_DIR . 'block.json',
			array(
				'render_callback' => array( __CLASS__, 'render' ),
			)
		);
	}

	/**
	 * Render the block HTML on the frontend.
	 *
	 * WordPress calls this function every time a page containing the block
	 * is loaded. $attributes contains all the values the site owner set in
	 * the editor sidebar (system prompt, greeting, starter prompts, etc.).
	 *
	 * The output is a wrapper div with the block config embedded as a
	 * data-config JSON attribute. The frontend JavaScript (chat.js) reads
	 * this attribute and builds the interactive chat UI.
	 *
	 * The API key is NOT in this output — it stays server-side.
	 *
	 * @param array  $attributes Block attributes from the editor.
	 * @param string $content    Inner block content (unused — no inner blocks).
	 * @return string The HTML to output on the frontend.
	 */
	public static function render( $attributes, $content ) {

		// Build the config object that JavaScript will read.
		// All values come from block attributes (set by site owner in editor).
		// Defaults match those declared in block.json.
		$config = array(
			'greeting'       => isset( $attributes['greeting'] )
								? $attributes['greeting']
								: '// glad you\'re here. ask me anything.',
			'starterPrompts' => isset( $attributes['starterPrompts'] ) && is_array( $attributes['starterPrompts'] )
								? array_values( array_filter( $attributes['starterPrompts'], 'is_string' ) )
								: array(),
			'systemPrompt'   => isset( $attributes['systemPrompt'] )
								? $attributes['systemPrompt']
								: '',
			'placeholder'    => isset( $attributes['placeholder'] )
								? $attributes['placeholder']
								: '> type a command...',
			'maxTokens'      => absint( isset( $attributes['maxTokens'] ) ? $attributes['maxTokens'] : 300 ),
			'temperature'    => (float) ( isset( $attributes['temperature'] ) ? $attributes['temperature'] : 0.7 ),
			'showPoweredBy'  => isset( $attributes['showPoweredBy'] )
								? (bool) $attributes['showPoweredBy']
								: true,
			// The REST URL is generated server-side so it works on any installation.
			'restUrl'        => rest_url( 'api-playground/v1/chat' ),
		);

		// get_block_wrapper_attributes() generates the correct class, id, and
		// style attributes for the wrapper element, including any alignment
		// classes (alignwide, alignfull) and custom spacing the site owner set.
		$wrapper_attributes = get_block_wrapper_attributes(
			array( 'class' => 'api-playground-block' )
		);

		// Use output buffering to build the HTML string cleanly with heredoc-style PHP.
		// ob_start() begins capturing output. ob_get_clean() returns it as a string.
		ob_start();
		?>
		<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() returns sanitized HTML. ?>>
			<div
				class="apb-inner"
				data-config="<?php echo esc_attr( wp_json_encode( $config ) ); ?>"
			>
				<?php // Shown only when JavaScript is disabled. ?>
				<noscript>
					<p class="apb-noscript">
						<?php esc_html_e( 'JavaScript is required to use the chat interface.', 'api-playground' ); ?>
					</p>
				</noscript>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
