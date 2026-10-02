<?php defined( 'ABSPATH' ) || exit; if ( is_active_sidebar( 'sidebar' ) ) : ?>
<aside class="site-sidebar" aria-label="<?php echo esc_attr__( 'Sidebar', 'al-base' ); ?>"><?php dynamic_sidebar( 'sidebar' ); ?></aside>
<?php endif; ?>
