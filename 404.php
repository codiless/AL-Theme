<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<main id="main-content" class="site-main content-container">
<?php al_breadcrumbs(); ?>
<h1><?php echo esc_html__( 'Page not found', 'al-base' ); ?></h1>
<p><?php echo esc_html__( 'The page may have moved. Search the site to find what you need.', 'al-base' ); ?></p>
<?php get_search_form(); ?>
</main>
<?php get_footer(); ?>
