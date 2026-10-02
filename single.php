<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<main id="main-content" class="site-main">
<div class="content-container"><?php al_breadcrumbs(); ?></div>
<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content', 'single' ); the_post_navigation(); if ( comments_open() || get_comments_number() ) { comments_template(); } endwhile; ?>
</main>
<?php get_footer(); ?>
