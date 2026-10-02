<?php
defined( 'ABSPATH' ) || exit;
$al_panel_id = wp_unique_id( 'al-menu-panel-' );
wp_enqueue_script_module( 'al-navigation', al_asset_url( 'assets/src/js/navigation.js' ), array(), '1.0.3' );
?>
<header class="site-header" data-al-navigation>
<div class="site-header__inner alignwide">
<div class="site-branding">
<?php if ( has_custom_logo() ) { the_custom_logo(); } else { ?><a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a><?php } ?>
</div>
<button class="site-menu-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $al_panel_id ); ?>">
<svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 6h16M4 12h16M4 18h16" fill="none" stroke="currentColor" stroke-width="2"/></svg>
<span><?php echo esc_html__( 'Menu', 'al-base' ); ?></span>
</button>
<div class="site-navigation" id="<?php echo esc_attr( $al_panel_id ); ?>">
<nav aria-label="<?php echo esc_attr__( 'Primary navigation', 'al-base' ); ?>">
<?php wp_nav_menu( apply_filters( 'al_primary_menu_args', array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'primary-menu', 'fallback_cb' => 'al_primary_menu_fallback', 'walker' => new AL_Nav_Walker() ) ) ); ?>
</nav>
<div class="site-header__tools">
<?php if ( apply_filters( 'al_header_search_enabled', true ) ) { get_search_form(); } al_language_switcher(); do_action( 'al_header_cta' ); ?>
</div>
</div>
</div>
</header>
