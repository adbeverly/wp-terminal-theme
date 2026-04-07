/**
 * Block entry point.
 *
 * This file is the webpack entry point for the block editor JS.
 * @wordpress/scripts compiles it to build/index.js.
 *
 * CSS imports here tell webpack to compile and extract the stylesheets:
 *   './style.css'  → build/style-index.css  (loaded on frontend + in editor)
 *   './editor.css' → build/index.css         (loaded in editor only)
 */

/**
 * WordPress dependencies.
 *
 * These look like npm packages but @wordpress/scripts knows they are
 * already loaded globally by WordPress. It strips them from the bundle
 * and tells WordPress which global variables to provide instead.
 * This keeps the bundle small and avoids duplicate React instances.
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies.
 */
import Edit from './edit';
import save from './save';
import metadata from '../block.json';

/**
 * Styles.
 *
 * Importing CSS here is how @wordpress/scripts knows to compile them.
 * The file named 'style.css' gets the 'style-' prefix in the output name.
 */
import './style.css';
import './editor.css';

/**
 * Register the block.
 *
 * metadata.name = 'api-playground/chat' — read directly from block.json
 * so we never have the block name in two places.
 */
registerBlockType( metadata.name, {
	edit: Edit,
	save,
} );
