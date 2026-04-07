/**
 * Block editor UI.
 *
 * This React component renders the block inside the Gutenberg editor.
 * It has two parts:
 *   1. InspectorControls — the settings sidebar the site owner fills in.
 *   2. The block canvas — a live preview of the chat interface.
 */

/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	TextareaControl,
	TextControl,
	RangeControl,
	ToggleControl,
} from '@wordpress/components';

/**
 * Edit component.
 *
 * WordPress passes two props automatically:
 *   - attributes    The current values of all block attributes (from block.json).
 *   - setAttributes A function that updates one or more attributes and saves them.
 *
 * @param {Object} props
 * @param {Object} props.attributes    Current attribute values.
 * @param {Function} props.setAttributes Function to update attributes.
 * @return {Element} The block editor UI.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		systemPrompt,
		greeting,
		starterPrompts,
		placeholder,
		maxTokens,
		temperature,
		showPoweredBy,
	} = attributes;

	// useBlockProps() generates the correct class names and data attributes
	// for the block's wrapper element. Spreading it onto the div is required.
	const blockProps = useBlockProps( { className: 'api-playground-block' } );

	// Ensure starterPrompts is always an array (defensive — block.json sets []).
	const prompts = Array.isArray( starterPrompts ) ? starterPrompts : [];

	/**
	 * Update a specific starter prompt by index.
	 * Spreads the existing array, updates the slot, saves.
	 *
	 * @param {number} index  Which prompt slot (0–3).
	 * @param {string} value  The new prompt text.
	 */
	function updateStarterPrompt( index, value ) {
		const updated = [ ...prompts ];
		// Pad the array with empty strings if needed so the index exists.
		while ( updated.length <= index ) {
			updated.push( '' );
		}
		updated[ index ] = value;
		setAttributes( { starterPrompts: updated } );
	}

	// The active (non-empty) prompts for the canvas preview.
	const activePrompts = prompts.filter( Boolean );

	return (
		// The empty tag <> ... </> is a React "fragment" — it lets you return
		// two sibling elements (InspectorControls + the block div) without
		// wrapping them in an extra DOM element.
		<>
			{ /* ---- Sidebar settings ---- */ }
			<InspectorControls>

				{ /* Panel 1: Chat content */ }
				<PanelBody title={ __( 'Chat Settings', 'api-playground' ) }>
					<TextareaControl
						label={ __( 'System Prompt', 'api-playground' ) }
						help={ __( 'The AI persona and instructions. Visible in page source — keep sensitive details in your API key configuration instead.', 'api-playground' ) }
						value={ systemPrompt }
						onChange={ ( value ) =>
							setAttributes( { systemPrompt: value } )
						}
						rows={ 6 }
					/>
					<TextControl
						label={ __( 'Greeting', 'api-playground' ) }
						help={ __( 'The first message shown before the visitor types anything.', 'api-playground' ) }
						value={ greeting }
						onChange={ ( value ) =>
							setAttributes( { greeting: value } )
						}
					/>
					<TextControl
						label={ __( 'Input Placeholder', 'api-playground' ) }
						value={ placeholder }
						onChange={ ( value ) =>
							setAttributes( { placeholder: value } )
						}
					/>
				</PanelBody>

				{ /* Panel 2: Starter prompts — collapsed by default */ }
				<PanelBody
					title={ __( 'Starter Prompts', 'api-playground' ) }
					initialOpen={ false }
				>
					{ [ 0, 1, 2, 3 ].map( ( i ) => (
						<TextControl
							key={ i }
							// translators: %d is the prompt number (1–4).
							label={ __( 'Prompt', 'api-playground' ) + ' ' + ( i + 1 ) }
							value={ prompts[ i ] || '' }
							onChange={ ( value ) =>
								updateStarterPrompt( i, value )
							}
						/>
					) ) }
				</PanelBody>

				{ /* Panel 3: API settings — collapsed by default */ }
				<PanelBody
					title={ __( 'Advanced', 'api-playground' ) }
					initialOpen={ false }
				>
					<RangeControl
						label={ __( 'Max Response Length (tokens)', 'api-playground' ) }
						help={ __( 'Roughly 1 token ≈ 4 characters. 300 is a good default for chat.', 'api-playground' ) }
						value={ maxTokens }
						onChange={ ( value ) =>
							setAttributes( { maxTokens: value } )
						}
						min={ 50 }
						max={ 2048 }
						step={ 50 }
					/>
					<RangeControl
						label={ __( 'Temperature', 'api-playground' ) }
						help={ __( '0 = precise and focused, 1 = creative and varied.', 'api-playground' ) }
						value={ temperature }
						onChange={ ( value ) =>
							setAttributes( { temperature: value } )
						}
						min={ 0 }
						max={ 1 }
						step={ 0.1 }
					/>
					<ToggleControl
						label={ __( 'Show "Powered by Claude"', 'api-playground' ) }
						checked={ showPoweredBy }
						onChange={ ( value ) =>
							setAttributes( { showPoweredBy: value } )
						}
					/>
				</PanelBody>

			</InspectorControls>

			{ /* ---- Block canvas preview ---- */ }
			{ /* This is what the site owner sees while editing the page.    */ }
			{ /* The actual interactive chat only works on the live frontend. */ }
			<div { ...blockProps }>
				<div className="apb-preview">

					{ /* Terminal title bar */ }
					<div className="apb-title-bar">
						<span className="apb-dots" aria-hidden="true">
							<span></span>
							<span></span>
							<span></span>
						</span>
						<span className="apb-title">ask.exe</span>
					</div>

					{ /* Chat body */ }
					<div className="apb-body">
						<p className="apb-greeting">{ greeting }</p>

						{ /* Starter prompt chips — only shown if prompts exist */ }
						{ activePrompts.length > 0 && (
							<div className="apb-chips">
								{ activePrompts.map( ( prompt, i ) => (
									<button
										key={ i }
										className="apb-chip"
										disabled
										type="button"
									>
										{ prompt }
									</button>
								) ) }
							</div>
						) }

						{ /* Static input row — not interactive in the editor */ }
						<div className="apb-input-row">
							<span className="apb-input-placeholder">
								{ placeholder }
							</span>
						</div>

						{ /* Editor-only notice */ }
						<p className="apb-editor-notice">
							{ __( 'Chat is active on the published page.', 'api-playground' ) }
						</p>
					</div>

				</div>
			</div>
		</>
	);
}
