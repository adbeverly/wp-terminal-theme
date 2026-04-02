=== Terminal ===

Contributors: adbeverly
Tags: dark, one-column, custom-colors, custom-menu, accessibility-ready, block-editor-style
Requires at least: 6.3
Tested up to: 6.7
Requires PHP: 8.0
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A terminal-styled WordPress theme with dark, light, and groovy color modes.

== Description ==

Terminal is a hybrid WordPress theme with a dark, monospace, terminal aesthetic. Classic PHP
templates control the overall layout and structure. The block editor (Gutenberg) is supported
for page and post content areas. The terminal shell — header, footer, navigation, color modes
— is owned entirely by the theme.

Three color modes are included: dark (default), light, and groovy. The active mode is set by
the site owner in the Customizer. Visitors can switch modes themselves using the mode toggle,
and their preference is saved across sessions.

An interactive command prompt on the front page lets visitors navigate the site by typing
commands such as `ls pages/`, `ls posts/`, `cat [slug]`, and `search [term]`. All command
output is live from WordPress — no hardcoded content.

The theme works out of the box with the WordPress Theme Unit Test Data.

== Installation ==

1. In your WordPress admin, go to Appearance → Themes → Add New.
2. Click Upload Theme and select the terminal-theme.zip file.
3. Click Install Now, then Activate.
4. Go to Appearance → Menus to create and assign a menu to the Primary Menu location.
5. Go to Appearance → Customize to set your preferred default color mode and boot sequence.

== Frequently Asked Questions ==

= How do I change the color mode? =

Go to Appearance → Customize → Color Mode to set the default. Visitors can toggle modes
using the dark · light · groovy links in the footer, and their choice persists across visits.

= How do I customize the boot sequence? =

Go to Appearance → Customize → Terminal Settings. Enter one line of text per boot sequence
line in the Boot Sequence Lines field.

= Can I add my own terminal commands? =

Yes. Use the `terminal_theme_commands` filter in your child theme or plugin:

    add_filter( 'terminal_theme_commands', function( $commands ) {
        $commands['open resume'] = [
            'output' => '// opening resume...',
            'action' => 'navigate',
            'url'    => 'https://example.com/resume',
        ];
        return $commands;
    } );

= Does this theme support the block editor? =

Yes. Interior page and post templates wrap Gutenberg block content inside the terminal chrome.
The theme includes editor styles so the block editor matches the front-end appearance.

== Changelog ==

= 1.0.0 =
* Initial release.

== Copyright ==

Terminal WordPress Theme, Copyright 2025 Ashley Beverly
Terminal is distributed under the terms of the GNU GPL v2 or later.

This theme bundles the following third-party resources:

JetBrains Mono, Copyright 2020 The JetBrains Mono Project Authors
License: SIL Open Font License, Version 1.1
Source: https://www.jetbrains.com/lp/mono/
Location: assets/fonts/webfonts/