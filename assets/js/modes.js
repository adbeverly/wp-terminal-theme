/**
 * modes.js
 *
 * Color mode toggle and localStorage persistence.
 *
 * Reads the active mode from localStorage on page load and overrides
 * the server-set data-theme attribute on <body> if a visitor preference
 * is stored. Handles button clicks and updates aria-pressed state.
 */

( function () {
	'use strict';

	const STORAGE_KEY = 'terminalThemeMode';
	const VALID_MODES = [ 'dark', 'light', 'groovy' ];

	/**
	 * Apply a color mode to <body> and update toggle button state.
	 *
	 * @param {string} mode One of: dark, light, groovy.
	 */
	function applyMode( mode ) {
		if ( ! VALID_MODES.includes( mode ) ) {
			return;
		}

		document.body.setAttribute( 'data-theme', mode );

		document.querySelectorAll( '.terminal-mode-toggle__btn' ).forEach( function ( btn ) {
			btn.setAttribute( 'aria-pressed', btn.dataset.mode === mode ? 'true' : 'false' );
		} );
	}

	/**
	 * Save the visitor's mode preference to localStorage.
	 *
	 * @param {string} mode One of: dark, light, groovy.
	 */
	function saveMode( mode ) {
		try {
			localStorage.setItem( STORAGE_KEY, mode );
		} catch ( e ) {
			// localStorage unavailable (private browsing, storage full). Silently ignore.
		}
	}

	/**
	 * Read the stored mode preference from localStorage.
	 *
	 * @return {string|null} Stored mode or null if none.
	 */
	function getSavedMode() {
		try {
			return localStorage.getItem( STORAGE_KEY );
		} catch ( e ) {
			return null;
		}
	}

	// On load: apply stored preference if it exists and is valid.
	const savedMode = getSavedMode();
	if ( savedMode && VALID_MODES.includes( savedMode ) ) {
		applyMode( savedMode );
	}

	// Wire up toggle buttons.
	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.terminal-mode-toggle__btn' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				const mode = btn.dataset.mode;
				applyMode( mode );
				saveMode( mode );
			} );
		} );
	} );
}() );
