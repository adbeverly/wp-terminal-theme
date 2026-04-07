/**
 * Block save function.
 *
 * For a dynamic block, save() returns null. PHP handles all rendering
 * via the render_callback in class-block.php. There is no static HTML
 * to save to the database — the block is re-rendered on every page load.
 *
 * Returning null is not a fallback or an error — it is the correct and
 * intentional output for a dynamic block.
 *
 * @return {null}
 */
export default function save() {
	return null;
}
