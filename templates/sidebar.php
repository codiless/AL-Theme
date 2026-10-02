<?php
/** Template Name: Content with sidebar */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main-content" class="site-main alignwide">
<?php al_breadcrumbs(); ?>
<div class="content-sidebar"><div><?php while ( have_posts() ) { the_post(); get_template_part( 'template-parts/content', 'page' ); } ?></div><?php get_sidebar(); ?></div>
</main>
<?php get_footer(); ?>
