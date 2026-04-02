/**
 * commands.js
 *
 * Terminal command parser and output renderer.
 *
 * Reads terminalThemeData (set via wp_localize_script in inc/commands.php)
 * for live WordPress content: pages, posts, categories, tags.
 *
 * Supported commands:
 *   help                — list all commands
 *   ls pages/           — list published pages
 *   ls posts/           — list recent posts
 *   ls categories/      — list post categories
 *   ls tags/            — list post tags
 *   cat [slug]          — fetch and display a page by slug
 *   search [term]       — search posts and pages
 *   open [slug]         — navigate to a page by slug
 *   whoami              — site name, description, URL
 *   pwd                 — current page URL
 *   history             — show command history (session only)
 *   clear               — clear terminal output
 *   set theme [mode]    — switch color mode
 */

( function () {
	'use strict';

	const data    = window.terminalThemeData || {};
	const i18n    = data.i18n || {};
	const history = [];

	let historyIndex = -1;

	// ── DOM references ─────────────────────────────────────────────────────

	const input    = document.getElementById( 'terminal-input' );
	const output   = document.getElementById( 'terminal-output' );

	if ( ! input || ! output ) {
		return;
	}

	// ── Output helpers ─────────────────────────────────────────────────────

	/**
	 * Append a line to the terminal output area.
	 *
	 * @param {string} text    Line content.
	 * @param {string} variant CSS modifier: 'result', 'error', 'comment', or 'command'.
	 */
	function writeLine( text, variant ) {
		const line = document.createElement( 'span' );
		line.className = 'terminal-output-line terminal-output-line--' + ( variant || 'result' );
		line.textContent = text;
		output.appendChild( line );
		line.scrollIntoView( { block: 'nearest' } );
	}

	/**
	 * Append a line containing a clickable link.
	 *
	 * @param {string} label Link text.
	 * @param {string} url   Destination URL.
	 * @param {string} meta  Optional metadata shown after the link (e.g. date).
	 */
	function writeLink( label, url, meta ) {
		const line = document.createElement( 'span' );
		line.className = 'terminal-output-line terminal-output-line--result';

		const a = document.createElement( 'a' );
		a.href        = url;
		a.textContent = label;

		line.appendChild( a );

		if ( meta ) {
			const metaSpan = document.createElement( 'span' );
			metaSpan.className   = 'terminal-output-meta';
			metaSpan.textContent = '  ' + meta;
			line.appendChild( metaSpan );
		}

		output.appendChild( line );
		line.scrollIntoView( { block: 'nearest' } );
	}

	/**
	 * Echo the entered command back to the output area.
	 *
	 * @param {string} raw The raw command string.
	 */
	function echoCommand( raw ) {
		writeLine( raw, 'command' );
	}

	// ── Command handlers ───────────────────────────────────────────────────

	const commands = {

		help: function () {
			writeLine( i18n.help || 'available commands:', 'comment' );
			[
				'help', 'ls pages/', 'ls posts/', 'ls categories/', 'ls tags/',
				'cat [slug]', 'search [term]', 'open [slug]',
				'whoami', 'pwd', 'history', 'clear', 'set theme [dark|light|groovy]',
			].forEach( function ( cmd ) {
				writeLine( '  ' + cmd, 'result' );
			} );
		},

		'ls pages/': function () {
			const pages = data.pages || [];
			if ( ! pages.length ) {
				writeLine( i18n.noPages || '// no pages found.', 'comment' );
				return;
			}
			pages.forEach( function ( page ) {
				writeLink( page.slug + '/', page.url );
			} );
		},

		'ls posts/': function () {
			const posts = data.posts || [];
			if ( ! posts.length ) {
				writeLine( i18n.noPosts || '// no posts found.', 'comment' );
				return;
			}
			posts.forEach( function ( post ) {
				writeLink( post.slug, post.url, post.date || '' );
			} );
		},

		'ls categories/': function () {
			const cats = data.categories || [];
			if ( ! cats.length ) {
				writeLine( '// no categories found.', 'comment' );
				return;
			}
			cats.forEach( function ( cat ) {
				writeLink( cat.slug + '/', cat.url, '(' + cat.count + ')' );
			} );
		},

		'ls tags/': function () {
			const tags = data.tags || [];
			if ( ! tags.length ) {
				writeLine( '// no tags found.', 'comment' );
				return;
			}
			tags.forEach( function ( tag ) {
				writeLink( tag.slug, tag.url, '(' + tag.count + ')' );
			} );
		},

		whoami: function () {
			const w = data.whoami || {};
			writeLine( w.name || '', 'result' );
			if ( w.description ) {
				writeLine( w.description, 'comment' );
			}
			writeLine( w.url || '', 'result' );
		},

		pwd: function () {
			writeLine( window.location.href, 'result' );
		},

		history: function () {
			if ( ! history.length ) {
				writeLine( '// no history yet.', 'comment' );
				return;
			}
			history.forEach( function ( cmd, i ) {
				writeLine( ( i + 1 ) + '  ' + cmd, 'result' );
			} );
		},

		clear: function () {
			output.innerHTML = '';
			writeLine( i18n.cleared || '// terminal cleared.', 'comment' );
		},
	};

	// ── Dynamic command handlers (prefix-matched) ──────────────────────────

	/**
	 * Handle commands that take arguments: cat, search, open, set theme.
	 *
	 * @param {string} raw Full command string entered by the visitor.
	 */
	function handleDynamic( raw ) {
		const parts = raw.trim().split( /\s+/ );
		const verb  = parts[ 0 ];

		// cat [slug]
		if ( 'cat' === verb && parts[ 1 ] ) {
			const slug = parts[ 1 ];
			const page = ( data.pages || [] ).find( function ( p ) { return p.slug === slug; } );
			if ( page ) {
				writeLine( '// opening ' + slug + '...', 'comment' );
				window.location.href = page.url;
			} else {
				writeLine( '// page "' + slug + '" not found.', 'error' );
			}
			return true;
		}

		// open [slug]
		if ( 'open' === verb && parts[ 1 ] ) {
			const slug = parts[ 1 ];
			const page = ( data.pages || [] ).find( function ( p ) { return p.slug === slug; } );
			if ( page ) {
				writeLine( i18n.opening || '// opening...', 'comment' );
				window.location.href = page.url;
			} else {
				writeLine( '// "' + slug + '" not found.', 'error' );
			}
			return true;
		}

		// search [term]
		if ( 'search' === verb && parts[ 1 ] ) {
			const term    = parts.slice( 1 ).join( ' ' );
			const homeUrl = data.homeUrl || '/';
			writeLine( i18n.searching || '// searching...', 'comment' );
			window.location.href = homeUrl + '?s=' + encodeURIComponent( term );
			return true;
		}

		// set theme [mode]
		if ( 'set' === verb && 'theme' === parts[ 1 ] && parts[ 2 ] ) {
			const mode  = parts[ 2 ];
			const valid = [ 'dark', 'light', 'groovy' ];
			if ( valid.includes( mode ) ) {
				document.body.setAttribute( 'data-theme', mode );
				try { localStorage.setItem( 'terminalThemeMode', mode ); } catch ( e ) {}
				document.querySelectorAll( '.terminal-mode-toggle__btn' ).forEach( function ( btn ) {
					btn.setAttribute( 'aria-pressed', btn.dataset.mode === mode ? 'true' : 'false' );
				} );
				writeLine( i18n.themeSet || '// theme updated.', 'comment' );
			} else {
				writeLine( '// unknown theme "' + mode + '". try: dark, light, groovy.', 'error' );
			}
			return true;
		}

		return false;
	}

	// ── Input handling ─────────────────────────────────────────────────────

	input.addEventListener( 'keydown', function ( e ) {

		// Arrow up/down: navigate history.
		if ( 'ArrowUp' === e.key ) {
			e.preventDefault();
			if ( historyIndex < history.length - 1 ) {
				historyIndex++;
				input.value = history[ history.length - 1 - historyIndex ];
			}
			return;
		}

		if ( 'ArrowDown' === e.key ) {
			e.preventDefault();
			if ( historyIndex > 0 ) {
				historyIndex--;
				input.value = history[ history.length - 1 - historyIndex ];
			} else {
				historyIndex = -1;
				input.value  = '';
			}
			return;
		}

		// Enter: run command.
		if ( 'Enter' !== e.key ) {
			return;
		}

		const raw = input.value.trim();
		input.value  = '';
		historyIndex = -1;

		if ( ! raw ) {
			return;
		}

		history.push( raw );
		echoCommand( raw );

		const normalized = raw.toLowerCase();

		if ( commands[ normalized ] ) {
			commands[ normalized ]();
		} else if ( ! handleDynamic( raw ) ) {
			writeLine( i18n.notFound || '// command not found. type help for available commands.', 'error' );
		}
	} );
}() );
