<?php
defined( 'ABSPATH' ) || exit;
add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'al-base', get_template_directory() . '/languages' );
	foreach ( array( 'title-tag', 'post-thumbnails', 'responsive-embeds', 'align-wide', 'wp-block-styles', 'editor-styles', 'automatic-feed-links' ) as $feature ) {
		add_theme_support( $feature );
	}
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 240, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'woocommerce' );
	register_nav_menus( array( 'primary' => __( 'Primary navigation', 'al-base' ), 'footer' => __( 'Footer navigation', 'al-base' ) ) );
	add_editor_style( al_asset_path( 'assets/src/css/editor.css' ) );
} );
add_action( 'widgets_init', function () {
	register_sidebar( array( 'name' => __( 'Sidebar', 'al-base' ), 'id' => 'sidebar', 'before_widget' => '<section id="%1$s" class="widget %2$s">', 'after_widget' => '</section>', 'before_title' => '<h2 class="widget-title">', 'after_title' => '</h2>' ) );
} );
