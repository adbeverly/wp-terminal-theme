<?php
/**
 * Terminal Theme — searchform.php
 *
 * Custom search form loaded by get_search_form().
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="terminal-search-field" class="screen-reader-text">
		<?php esc_html_e( 'Search', 'terminal-theme' ); ?>
	</label>
	<input
		type="search"
		id="terminal-search-field"
		class="search-field"
		placeholder="<?php echo esc_attr_x( 'search [term]', 'placeholder', 'terminal-theme' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
	>
	<button type="submit" class="search-submit">
		<?php esc_html_e( 'search', 'terminal-theme' ); ?>
	</button>
</form>
