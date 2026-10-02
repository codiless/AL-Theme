<?php defined( 'ABSPATH' ) || exit; ?>
<footer class="site-footer"><div class="alignwide">
<?php do_action( 'al_footer_cta' ); ?>
<?php if ( has_nav_menu( 'footer' ) ) : ?><nav aria-label="<?php echo esc_attr__( 'Footer navigation', 'al-base' ); ?>"><?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 1, 'fallback_cb' => false ) ); ?></nav><?php endif; ?>
<p><?php echo esc_html( sprintf( __( '© %1$s %2$s', 'al-base' ), wp_date( 'Y' ), get_bloginfo( 'name' ) ) ); ?></p>
</div></footer>
