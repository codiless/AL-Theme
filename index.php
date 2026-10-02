<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<main id="main-content" class="site-main alignwide">
<?php al_breadcrumbs(); ?>
<header class="page-header">
<?php if ( is_search() ) : ?><h1><?php echo esc_html( sprintf( __( 'Search results for: %s', 'al-base' ), get_search_query() ) ); ?></h1><?php get_search_form(); ?>
<?php elseif ( is_archive() ) : ?><h1><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1><?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
<?php else : ?><h1><?php echo esc_html( get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'Latest content', 'al-base' ) ); ?></h1><?php endif; ?>
</header>
<?php if ( have_posts() ) : ?>
<div class="al-listing"><div class="al-listing__items al-layout--grid">
<?php while ( have_posts() ) { the_post(); al_render_card( get_post() ); } ?>
</div></div>
<?php the_posts_pagination( array( 'prev_text' => __( 'Previous', 'al-base' ), 'next_text' => __( 'Next', 'al-base' ) ) ); ?>
<?php else : ?>
<?php get_template_part( 'template-parts/content', 'none' ); ?>
<?php endif; ?>
</main>
<?php get_footer(); ?>
