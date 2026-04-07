=== API Playground Block ===
Contributors:      adbeverly
Tags:              block, ai, chat, claude, anthropic
Requires at least: 6.3
Tested up to:      6.7
Requires PHP:      8.0
Stable tag:        1.0.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Embed a configurable AI chat interface powered by the Anthropic API on any page using a Gutenberg block.

== Description ==

API Playground Block adds a Gutenberg block that puts a fully configured AI chat interface on any page or post. The site owner sets the AI persona, opening message, and clickable starter prompts entirely from the block settings panel — no code required.

**Security first.** The Anthropic API key is stored server-side and never sent to the browser. All requests from visitors are proxied through a WordPress REST endpoint that retrieves the key, calls the Anthropic API, and returns only the reply. Rate limiting prevents API abuse.

**Terminal aesthetic out of the box.** Designed to work alongside the Terminal theme — the block inherits the active color mode (dark, light, groovy) automatically via CSS custom properties. Works on any theme with standalone dark terminal defaults.

= Features =

* Configure the AI persona, greeting, and up to four starter prompts per block instance
* API key stored securely in wp_options — never exposed to the browser
* Server-side Anthropic API proxy with per-IP rate limiting via WordPress transients
* Multi-turn conversation with context maintained across the session
* Typewriter animation on responses
* Automatic color mode inheritance from the Terminal theme
* Accessible: keyboard navigation, screen reader labels, focus indicators, noscript fallback
* All strings internationalized with the api-playground text domain

= Security Architecture =

```
Visitor types message
      ↓
JavaScript sends POST to /wp-json/api-playground/v1/chat
      ↓
WordPress verifies nonce (prevents cross-site request forgery)
      ↓
PHP checks rate limit (transient keyed to visitor IP)
      ↓
PHP retrieves API key from wp_options (server-side only)
      ↓
PHP calls Anthropic API
      ↓
Only the reply text is returned to the browser
```

= Third-Party Services =

This plugin sends data to the Anthropic API to generate chat responses. Specifically, it sends the visitor's message, the conversation history for the current session, and the system prompt configured by the site owner.

* Service: Anthropic API — https://www.anthropic.com
* Privacy policy: https://www.anthropic.com/privacy
* Terms of service: https://www.anthropic.com/terms

Data is sent only when a visitor submits a message in the chat interface. No data is sent during plugin installation or on pages that do not contain the block.

An Anthropic API key is required. API usage is billed according to Anthropic's pricing.

== Installation ==

1. Upload the `api-playground` folder to `/wp-content/plugins/`.
2. Activate the plugin through the Plugins screen.
3. Go to **Settings &rsaquo; API Playground** and enter your Anthropic API key.
4. Edit any page or post and add the **API Playground** block from the block inserter.
5. Configure the system prompt, greeting, and starter prompts in the block settings sidebar.
6. Publish the page and test the chat on the frontend.

= Building the JavaScript =

The plugin ships with pre-built JavaScript in the `build/` directory. If you want to modify `src/` and rebuild:

1. Install Node.js (18 or later recommended).
2. In the `api-playground/` directory, run `npm install`.
3. Run `npm run build` to compile `src/` to `build/`.
4. Use `npm run start` during development to watch for changes and rebuild automatically.

== Frequently Asked Questions ==

= Where do I get an Anthropic API key? =

Sign up at console.anthropic.com. API usage is billed per token. Claude Haiku (the default model) is the most cost-efficient option for a chat interface.

= Is the API key safe? =

Yes. The key is stored in `wp_options` using WordPress's standard option storage. It is never output to any page, never included in any REST response, and never logged. It is only used server-side to authenticate requests to the Anthropic API.

= Can I use this block more than once on a page? =

Yes. Each block instance is independent — it has its own system prompt, greeting, starter prompts, and conversation history.

= What happens if the rate limit is reached? =

The chat displays an error message: "Too many requests. Please try again in an hour." The rate limit resets automatically every hour. You can adjust the limit (or set it to 0 to disable it) in Settings &rsaquo; API Playground.

= Does this work without the Terminal theme? =

Yes. The block includes standalone dark terminal styling as fallback values. It will look like a self-contained terminal window on any theme.

= Can I change the AI model? =

Yes. Go to Settings &rsaquo; API Playground and choose between Claude Haiku 4.5 (fast, recommended for chat) and Claude Sonnet 4.6 (more capable, higher cost). The model applies to all block instances on the site.

= What data is stored? =

The plugin stores three options in `wp_options`: the API key, the chosen model, and the rate limit. Rate-limiting counters are stored as transients with a two-hour expiry. No conversation history is stored — it exists only in the visitor's browser memory for the current page session. All stored data is removed when the plugin is deleted.

== Screenshots ==

1. The API Playground block on the frontend — terminal-styled chat interface with starter prompt chips.
2. The block in the Gutenberg editor — live preview with sidebar settings panel.
3. The Settings &rsaquo; API Playground admin page — API key, model, and rate limit.

== Changelog ==

= 1.0.0 =
* Initial release.
* Gutenberg block with per-instance system prompt, greeting, and starter prompts.
* WordPress REST endpoint proxying requests to the Anthropic API server-side.
* Per-IP rate limiting via WordPress transients.
* CSS variable architecture inheriting Terminal theme color modes.
* Accessible keyboard navigation, screen reader labels, focus indicators.
