<?php
/**
 * Terminal Theme — comments.php
 *
 * Displays the comment list (threaded) and comment form.
 * Separates trackbacks/pingbacks from regular comments.
 * Loaded via comments_template() in single.php and page.php.
 *
 * @package terminal-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * If the current post is password-protected and the visitor has not yet
 * entered the password, return early — do not reveal comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="comments-area">

	<?php /* ── Comment count header ──────────────────────────────────── */ ?>
	<?php if ( have_comments() ) : ?>

		<h2 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			if ( '1' === $comment_count ) {
				printf(
					/* translators: %s: post title */
					esc_html__( '// 1 comment on "%s"', 'terminal-theme' ),
					esc_html( get_the_title() )
				);
			} else {
				printf(
					/* translators: 1: comment count, 2: post title */
					esc_html__( '// %1$s comments on "%2$s"', 'terminal-theme' ),
					esc_html( number_format_i18n( $comment_count ) ),
					esc_html( get_the_title() )
				);
			}
			?>
		</h2>

		<?php /* ── Trackbacks / pingbacks ──────────────────────────── */ ?>
		<?php
		$terminal_pings = wp_list_comments(
			array(
				'type'    => 'pings',
				'echo'    => false,
			)
		);

		if ( $terminal_pings ) :
			?>
			<div class="trackbacks">
				<h3 class="trackbacks-title"><?php esc_html_e( '// trackbacks', 'terminal-theme' ); ?></h3>
				<ol class="trackbacks-list">
					<?php
					wp_list_comments(
						array(
							'type'  => 'pings',
							'style' => 'ol',
						)
					);
					?>
				</ol>
			</div>
		<?php endif; ?>

		<?php /* ── Comment list ─────────────────────────────────────── */ ?>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'type'        => 'comment',
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 0,
					'callback'    => 'terminal_theme_comment',
					'end-callback' => 'terminal_theme_comment_end',
				)
			);
			?>
		</ol>

		<?php the_comments_pagination(); ?>

	<?php endif; // have_comments() ?>

	<?php /* ── Comment form ──────────────────────────────────────────── */ ?>
	<?php if ( comments_open() ) : ?>

		<?php
		comment_form(
			array(
				'title_reply'          => esc_html__( '// leave a comment', 'terminal-theme' ),
				'title_reply_to'       => esc_html__( '// reply to %s', 'terminal-theme' ),
				'cancel_reply_link'    => esc_html__( 'cancel', 'terminal-theme' ),
				'label_submit'         => esc_html__( 'submit', 'terminal-theme' ),
				'class_submit'         => 'search-submit',
				'comment_field'        =>
					'<p class="comment-form-comment">' .
					'<label for="comment">' . esc_html__( 'comment', 'terminal-theme' ) . '</label>' .
					'<textarea id="comment" name="comment" class="search-field" cols="45" rows="6" required></textarea>' .
					'</p>',
			)
		);
		?>

	<?php elseif ( ! is_single() || ! post_type_supports( get_post_type(), 'comments' ) ) : ?>

		<p class="comments-closed terminal-output-line terminal-output-line--comment">
			<?php esc_html_e( '// comments are closed.', 'terminal-theme' ); ?>
		</p>

	<?php endif; ?>

</section><!-- #comments -->

<?php
/**
 * Custom callback for rendering individual comments.
 * Called by wp_list_comments() for each comment in the list.
 *
 * WordPress passes $comment, $args, and $depth automatically.
 *
 * @param WP_Comment $comment Comment object.
 * @param array      $args    wp_list_comments() arguments.
 * @param int        $depth   Nesting depth of this comment.
 */
function terminal_theme_comment( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo esc_html( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'terminal-comment', $comment ); ?>>

		<article class="comment-body">

			<header class="comment-meta">
				<span class="comment-author">
					<?php
					/*
					 * comment_author_link() outputs the commenter's name as a link
					 * if they provided a URL, or plain text otherwise.
					 * bypostauthor class (added by WordPress via comment_class()) marks
					 * comments from the post author for distinct styling.
					 */
					echo get_comment_author_link( $comment );
					?>
				</span>
				<span class="comment-date">
					<a href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
						<time datetime="<?php comment_time( 'c' ); ?>">
							<?php comment_date( 'Y-m-d' ); ?> <?php comment_time(); ?>
						</time>
					</a>
				</span>
				<?php edit_comment_link( esc_html__( 'edit', 'terminal-theme' ), '<span class="comment-edit">', '</span>' ); ?>
			</header>

			<?php if ( '0' === $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation terminal-output-line terminal-output-line--comment">
					<?php esc_html_e( '// your comment is awaiting moderation.', 'terminal-theme' ); ?>
				</p>
			<?php endif; ?>

			<div class="comment-content entry-content">
				<?php comment_text(); ?>
			</div>

			<footer class="comment-reply">
				<?php
				comment_reply_link(
					array_merge(
						$args,
						array(
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before'    => '',
							'after'     => '',
							'reply_text' => esc_html__( '// reply', 'terminal-theme' ),
						)
					)
				);
				?>
			</footer>

		</article>
	<?php
}

/**
 * End callback for wp_list_comments() — closes the comment tag.
 *
 * @param string $args  wp_list_comments() arguments.
 * @param int    $depth Nesting depth of this comment.
 */
function terminal_theme_comment_end( $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	echo '</' . esc_html( $tag ) . '>';
}
