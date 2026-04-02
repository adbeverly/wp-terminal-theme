<?php
/**
 * Terminal Theme — front-page.php
 *
 * Full terminal experience: boot sequence + interactive command prompt.
 * Used when a static front page is set OR as the default homepage.
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="terminal-content" tabindex="-1">

	<?php get_template_part( 'template-parts/boot-sequence' ); ?>

	<div class="terminal-output" id="terminal-output" aria-live="polite" aria-atomic="false"></div>

	<?php if ( get_theme_mod( 'terminal_theme_show_prompt', true ) ) : ?>
	<div class="terminal-prompt-wrap" id="terminal-prompt">
		<span class="terminal-prompt-symbol" aria-hidden="true">&gt;</span>
		<input
			type="text"
			class="terminal-input"
			id="terminal-input"
			autocomplete="off"
			autocorrect="off"
			autocapitalize="off"
			spellcheck="false"
			placeholder="<?php echo esc_attr_x( 'type a command...', 'placeholder', 'terminal-theme' ); ?>"
			aria-label="<?php esc_attr_e( 'Terminal command input', 'terminal-theme' ); ?>"
		>
	</div>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
