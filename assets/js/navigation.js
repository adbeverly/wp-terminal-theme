/**
 * navigation.js
 *
 * Manages aria-expanded on parent menu items for screen readers.
 * Dropdowns open and close via CSS :hover and :focus-within.
 * The ▾ indicator is a CSS ::after pseudo-element — no injected buttons.
 */

( function () {
	'use strict';

	const menubar = document.querySelector( '.terminal-menubar' );

	if ( ! menubar ) {
		return;
	}

	// Find all <li> elements that have a direct child <ul>.
	const allItems = menubar.querySelectorAll( 'li' );
	const parents  = Array.prototype.filter.call( allItems, function ( li ) {
		return li.querySelector( ':scope > ul' ) !== null;
	} );

	parents.forEach( function ( li ) {
		const link = li.querySelector( ':scope > a' );
		if ( ! link ) {
			return;
		}

		link.setAttribute( 'aria-haspopup', 'true' );
		link.setAttribute( 'aria-expanded', 'false' );

		// Update aria-expanded when the dropdown opens via hover or focus.
		li.addEventListener( 'mouseenter', function () {
			link.setAttribute( 'aria-expanded', 'true' );
		} );
		li.addEventListener( 'mouseleave', function () {
			link.setAttribute( 'aria-expanded', 'false' );
		} );
		li.addEventListener( 'focusin', function () {
			link.setAttribute( 'aria-expanded', 'true' );
		} );
		li.addEventListener( 'focusout', function ( e ) {
			if ( ! li.contains( e.relatedTarget ) ) {
				link.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	} );

}() );
