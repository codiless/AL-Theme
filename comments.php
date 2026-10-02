<?php defined( 'ABSPATH' ) || exit; if ( post_password_required() ) { return; } ?>
<section id="comments" class="comments-area content-container">
<?php if ( have_comments() ) : ?>
<h2><?php echo esc_html__( 'Comments', 'al-base' ); ?></h2>
<ol class="comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 48 ) ); ?></ol>
<?php the_comments_pagination(); endif; comment_form(); ?>
</section>
