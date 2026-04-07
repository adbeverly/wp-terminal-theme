/**
 * API Playground Block — Frontend Chat Interface
 *
 * This script runs on the public-facing frontend when the block is on the page.
 * It is NOT loaded in the Gutenberg editor — that is handled by edit.js.
 *
 * Flow:
 *   1. On page load, find all block instances (.apb-inner[data-config]).
 *   2. Parse the JSON config from each block's data-config attribute.
 *   3. Build the interactive chat UI in place.
 *   4. On submit, POST to the WordPress REST endpoint with the message + nonce.
 *   5. Show the response with a typewriter animation.
 *
 * This file uses plain vanilla JavaScript — no React, no jQuery, no frameworks.
 * It needs to work in any browser a visitor might use.
 *
 * The IIFE (Immediately Invoked Function Expression) wrapper prevents any
 * variables declared here from leaking into the global scope and conflicting
 * with other scripts on the page.
 */
( function () {
	'use strict';

	// -------------------------------------------------------------------------
	// Initialisation
	// -------------------------------------------------------------------------

	/**
	 * Find and initialise every block instance on the current page.
	 * Called once when the DOM is ready.
	 */
	function initAll() {
		const instances = document.querySelectorAll( '.apb-inner[data-config]' );
		instances.forEach( initBlock );
	}

	/**
	 * Initialise a single block instance.
	 *
	 * @param {HTMLElement} container The .apb-inner element output by PHP.
	 */
	function initBlock( container ) {
		// Parse the JSON config the PHP render callback embedded.
		let config;
		try {
			config = JSON.parse( container.dataset.config );
		} catch ( e ) {
			// Malformed JSON — skip this block and leave the noscript fallback.
			return;
		}

		// conversation history — array of { role: 'user'|'assistant', content: string }.
		// Kept in memory for the page session. Each block gets its own array.
		const history = [];

		// Replace the container's contents with the interactive chat UI.
		container.innerHTML = '';
		const ui = buildUI( config, history );
		container.appendChild( ui );
	}

	// -------------------------------------------------------------------------
	// UI builder
	// -------------------------------------------------------------------------

	/**
	 * Build the complete chat interface for one block instance.
	 *
	 * @param {Object} config  Parsed block config from data-config.
	 * @param {Array}  history Conversation history array (shared by reference).
	 * @return {HTMLElement} The fully built chat container.
	 */
	function buildUI( config, history ) {
		const wrap = el( 'div', 'apb-chat' );

		// -- Title bar --------------------------------------------------------
		const bar = el( 'div', 'apb-title-bar' );
		const dots = el( 'span', 'apb-dots' );
		dots.setAttribute( 'aria-hidden', 'true' );
		dots.innerHTML = '<span></span><span></span><span></span>';
		const barTitle = el( 'span', 'apb-title' );
		barTitle.textContent = 'ask.exe';
		bar.append( dots, barTitle );

		// -- Messages area ----------------------------------------------------
		// aria-live="polite" tells screen readers to announce new messages
		// after the current speech is finished.
		const messages = el( 'div', 'apb-messages' );
		messages.setAttribute( 'aria-live', 'polite' );
		messages.setAttribute( 'aria-atomic', 'false' );
		messages.setAttribute( 'role', 'log' );

		// Render the greeting.
		appendMessage( messages, config.greeting || '// ready.', 'assistant' );

		// -- Starter prompt chips ---------------------------------------------
		const activePrompts = Array.isArray( config.starterPrompts )
			? config.starterPrompts.filter( Boolean )
			: [];

		const chips = activePrompts.length > 0 ? el( 'div', 'apb-chips' ) : null;

		if ( chips ) {
			activePrompts.forEach( function ( prompt ) {
				const chip = el( 'button', 'apb-chip' );
				chip.type = 'button';
				chip.textContent = prompt;
				// Clicking a chip is the same as typing and submitting that text.
				chip.addEventListener( 'click', function () {
					handleSubmit( prompt, input, messages, chips, config, history );
				} );
				chips.appendChild( chip );
			} );
		}

		// -- Input row --------------------------------------------------------
		const inputRow = el( 'div', 'apb-input-row' );

		// Every input needs a label for accessibility. We visually hide this
		// one because the placeholder provides the visual context.
		const labelEl = el( 'label', 'apb-sr-only' );
		const inputId = 'apb-input-' + Math.random().toString( 36 ).slice( 2 );
		labelEl.setAttribute( 'for', inputId );
		labelEl.textContent = 'Enter a message';

		const input = el( 'input', 'apb-input' );
		input.type = 'text';
		input.id = inputId;
		input.placeholder = config.placeholder || '> type a command...';
		input.setAttribute( 'autocomplete', 'off' );
		input.setAttribute( 'spellcheck', 'false' );

		const sendBtn = el( 'button', 'apb-send' );
		sendBtn.type = 'button';
		sendBtn.textContent = 'send';
		sendBtn.setAttribute( 'aria-label', 'Send message' );

		// Submit on Enter key or button click.
		input.addEventListener( 'keydown', function ( e ) {
			if ( 'Enter' === e.key ) {
				e.preventDefault();
				if ( input.value.trim() ) {
					handleSubmit( input.value.trim(), input, messages, chips, config, history );
				}
			}
		} );
		sendBtn.addEventListener( 'click', function () {
			if ( input.value.trim() ) {
				handleSubmit( input.value.trim(), input, messages, chips, config, history );
			}
		} );

		inputRow.append( labelEl, input, sendBtn );

		// -- Powered by attribution -------------------------------------------
		let poweredBy = null;
		if ( config.showPoweredBy ) {
			poweredBy = el( 'p', 'apb-powered-by' );
			poweredBy.textContent = 'Powered by Claude';
		}

		// -- Assemble ---------------------------------------------------------
		wrap.appendChild( bar );
		wrap.appendChild( messages );
		if ( chips ) wrap.appendChild( chips );
		wrap.appendChild( inputRow );
		if ( poweredBy ) wrap.appendChild( poweredBy );

		return wrap;
	}

	// -------------------------------------------------------------------------
	// Message handling
	// -------------------------------------------------------------------------

	/**
	 * Handle a message submission — from either the input or a chip click.
	 *
	 * @param {string}      message  The message text to send.
	 * @param {HTMLElement} input    The text input element.
	 * @param {HTMLElement} messages The messages container.
	 * @param {HTMLElement|null} chips The chips container (hidden after first use).
	 * @param {Object}      config   Block config.
	 * @param {Array}       history  Conversation history (mutated in place).
	 */
	function handleSubmit( message, input, messages, chips, config, history ) {
		// Clear input and disable controls while the request is in flight.
		input.value = '';
		setEnabled( input, false );

		// Hide starter chips after the first real interaction.
		if ( chips ) {
			chips.hidden = true;
		}

		// Show the user's message immediately.
		appendMessage( messages, message, 'user' );

		// Show a placeholder while waiting for the response.
		const loadingEl = appendMessage( messages, '...', 'assistant assistant--loading' );

		// Build the history to send — all prior turns before this message.
		// The REST endpoint receives history (prior turns) + message (current turn)
		// separately and appends the current message itself.
		const priorHistory = history.slice();

		// Add this turn to our local history now.
		history.push( { role: 'user', content: message } );

		fetch( config.restUrl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify( {
				message: message,
				history: priorHistory,
				system_prompt: config.systemPrompt || '',
				max_tokens: config.maxTokens || 300,
				temperature: config.temperature || 0.7,
			} ),
		} )
			.then( function ( response ) {
				// If the server returned a non-2xx status, parse the error body.
				if ( ! response.ok ) {
					return response.json().then( function ( errData ) {
						throw new Error(
							errData.message || 'request failed (' + response.status + ')'
						);
					} );
				}
				return response.json();
			} )
			.then( function ( data ) {
				// Remove the loading dots.
				loadingEl.remove();

				// Typewrite the reply into a new message bubble.
				const replyEl = appendMessage( messages, '', 'assistant' );
				typewrite( replyEl, data.reply, function () {
					// Re-enable input after the animation completes.
					setEnabled( input, true );
					input.focus();
				} );

				// Save the assistant reply to history for multi-turn context.
				history.push( { role: 'assistant', content: data.reply } );
			} )
			.catch( function ( err ) {
				loadingEl.remove();
				appendMessage(
					messages,
					'// error: ' + err.message,
					'assistant assistant--error'
				);
				setEnabled( input, true );
				input.focus();
			} );
	}

	// -------------------------------------------------------------------------
	// DOM helpers
	// -------------------------------------------------------------------------

	/**
	 * Append a message paragraph to the messages container and scroll to it.
	 *
	 * @param {HTMLElement} container The messages area.
	 * @param {string}      text      Message text.
	 * @param {string}      cssClass  CSS class string.
	 * @return {HTMLElement} The created paragraph.
	 */
	function appendMessage( container, text, cssClass ) {
		const p = el( 'p', 'apb-message ' + cssClass );
		p.textContent = text;
		container.appendChild( p );
		scrollToBottom( container );
		return p;
	}

	/**
	 * Typewriter effect — reveal text one character at a time.
	 *
	 * Uses setTimeout to schedule each character so the browser stays
	 * responsive. Calls onDone when the full text has been revealed.
	 *
	 * @param {HTMLElement} target  The element to write into.
	 * @param {string}      text    The complete text to reveal.
	 * @param {Function}    onDone  Called when typing is complete.
	 * @param {number}      [speed] Milliseconds per character. Default 16.
	 */
	function typewrite( target, text, onDone, speed ) {
		const delay = speed || 16;
		let i = 0;

		function step() {
			if ( i < text.length ) {
				target.textContent += text[ i ];
				i++;
				scrollToBottom( target.parentElement );
				setTimeout( step, delay );
			} else if ( 'function' === typeof onDone ) {
				onDone();
			}
		}

		step();
	}

	/**
	 * Enable or disable the input field and its send button.
	 *
	 * @param {HTMLElement} input   The input element.
	 * @param {boolean}     enabled True to enable, false to disable.
	 */
	function setEnabled( input, enabled ) {
		input.disabled = ! enabled;
		// The send button is a sibling inside .apb-input-row.
		if ( input.parentElement ) {
			const btn = input.parentElement.querySelector( '.apb-send' );
			if ( btn ) btn.disabled = ! enabled;
		}
	}

	/**
	 * Scroll a container to its bottom.
	 *
	 * @param {HTMLElement|null} container
	 */
	function scrollToBottom( container ) {
		if ( container ) {
			container.scrollTop = container.scrollHeight;
		}
	}

	/**
	 * Shorthand for createElement + className.
	 *
	 * @param {string} tag       HTML tag name.
	 * @param {string} className CSS class string.
	 * @return {HTMLElement}
	 */
	function el( tag, className ) {
		const node = document.createElement( tag );
		if ( className ) node.className = className;
		return node;
	}

	// -------------------------------------------------------------------------
	// Boot
	// -------------------------------------------------------------------------

	// Run initAll once the DOM is fully parsed and available.
	// If the script loads after DOMContentLoaded has already fired (e.g. defer),
	// the readyState check means we still initialise correctly.
	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}
} )();
