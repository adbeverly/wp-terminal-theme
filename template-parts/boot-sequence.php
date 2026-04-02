<?php
/**
 * Terminal Theme — template-parts/boot-sequence.php
 *
 * Outputs the boot sequence lines container.
 * boot.js reads these from the localized terminalThemeData object
 * and renders them with configurable delays.
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="terminal-boot" id="terminal-boot" aria-live="polite" aria-atomic="false">
	<noscript>
		<span class="terminal-boot-line terminal-boot-line--accent">
			<?php esc_html_e( 'terminal ready.', 'terminal-theme' ); ?>
		</span>
	</noscript>
</div>
