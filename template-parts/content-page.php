<?php defined( 'ABSPATH' ) || exit; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php if ( ! is_page_template( 'templates/canvas.php' ) ) : ?><header class="entry-header content-container"><h1><?php echo esc_html( get_the_title() ); ?></h1></header><?php endif; ?>
<div class="entry-content is-layout-flow"><?php the_content(); wp_link_pages( array( 'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page sections', 'al-base' ) . '">', 'after' => '</nav>' ) ); ?></div>
</article>
