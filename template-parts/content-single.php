<?php defined( 'ABSPATH' ) || exit; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<header class="entry-header content-container"><h1><?php echo esc_html( get_the_title() ); ?></h1><p><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time> · <?php echo esc_html( get_the_author() ); ?></p></header>
<?php if ( has_post_thumbnail() ) : ?><figure class="entry-image alignwide"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?></figure><?php endif; ?>
<div class="entry-content is-layout-flow"><?php the_content(); wp_link_pages( array( 'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Article pages', 'al-base' ) . '">', 'after' => '</nav>' ) ); ?></div>
</article>
