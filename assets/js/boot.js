/**
 * boot.js
 *
 * Animated boot sequence for the front page terminal.
 *
 * Reads boot lines from terminalThemeData.bootLines (set via wp_localize_script
 * in inc/commands.php) and outputs them one at a time with a configurable delay.
 * The command prompt is hidden until the sequence completes, then revealed and
 * focused so the visitor can start typing immediately.
 */

( function () {
	'use strict';

	const DELAY_PER_LINE = 120; // ms between each boot line
	const DELAY_PROMPT   = 300; // ms after last line before prompt appears

	/**
	 * Run the boot sequence animation.
	 */
	function runBootSequence() {
		const bootContainer = document.getElementById( 'terminal-boot' );
		const promptWrap    = document.getElementById( 'terminal-prompt' );
		const input         = document.getElementById( 'terminal-input' );

		if ( ! bootContainer ) {
			return;
		}

		// Hide prompt until boot completes.
		if ( promptWrap ) {
			promptWrap.style.display = 'none';
		}

		const lines = ( window.terminalThemeData && window.terminalThemeData.bootLines )
			? window.terminalThemeData.bootLines
			: [ 'terminal ready.' ];

		// Output each line after a staggered delay.
		lines.forEach( function ( text, index ) {
			setTimeout( function () {
				const line = document.createElement( 'span' );
				line.className = 'terminal-boot-line';
				line.textContent = text;
				bootContainer.appendChild( line );
			}, index * DELAY_PER_LINE );
		} );

		// Reveal and focus the prompt after all lines have rendered.
		const totalDelay = lines.length * DELAY_PER_LINE + DELAY_PROMPT;

		setTimeout( function () {
			if ( promptWrap ) {
				promptWrap.style.display = '';
			}
			if ( input ) {
				input.focus();
			}
		}, totalDelay );
	}

	document.addEventListener( 'DOMContentLoaded', runBootSequence );
}() );
