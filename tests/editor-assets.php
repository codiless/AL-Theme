<?php
/** Execute theme asset hooks and guard the wp-admin dependency boundary. */
define( 'ABSPATH', __DIR__ );
$GLOBALS['al_test_hooks'] = array();
$GLOBALS['al_test_styles'] = array();
$GLOBALS['al_test_blocks'] = array();
$GLOBALS['al_test_editor_styles'] = array();
function add_action( $name, $callback ) { $GLOBALS['al_test_hooks'][ $name ][] = $callback; }
function add_filter( $name, $callback ) {}
function get_template_directory() { return dirname( __DIR__ ); }
function get_template_directory_uri() { return 'https://example.test/wp-content/themes/al-base'; }
function wp_register_style( $handle, $src, $deps = array(), $version = false ) { $GLOBALS['al_test_styles'][ $handle ] = array( 'src' => $src, 'deps' => $deps ); }
function wp_enqueue_style( $handle, $src = '', $deps = array(), $version = false ) {
	if ( ! isset( $GLOBALS['al_test_styles'][ $handle ] ) ) { wp_register_style( $handle, $src, $deps, $version ); }
	$GLOBALS['al_test_queue'][] = $handle;
}
function wp_register_script( ...$args ) {}
function wp_set_script_translations( ...$args ) {}
function register_block_type( $name, $args ) { $GLOBALS['al_test_blocks'][ $name ] = $args; }
function __( $text, $domain ) { return $text; }
function load_theme_textdomain( ...$args ) {}
function add_theme_support( ...$args ) {}
function register_nav_menus( ...$args ) {}
function add_editor_style( $path ) { $GLOBALS['al_test_editor_styles'][] = $path; }
function is_home() { return false; }
function is_archive() { return false; }
function is_search() { return false; }
function is_singular() { return false; }
function al_test_run( $name ) { foreach ( $GLOBALS['al_test_hooks'][ $name ] ?? array() as $callback ) { $callback(); } }
function al_test_require( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
function al_test_dependencies( $handle ) {
	$result = array( $handle );
	foreach ( $GLOBALS['al_test_styles'][ $handle ]['deps'] ?? array() as $dependency ) { $result = array_merge( $result, al_test_dependencies( $dependency ) ); }
	return $result;
}
require dirname( __DIR__ ) . '/inc/assets.php';
require dirname( __DIR__ ) . '/inc/setup.php';
require dirname( __DIR__ ) . '/inc/blocks.php';
al_test_run( 'init' );
al_test_run( 'after_setup_theme' );
$listing = $GLOBALS['al_test_blocks'][ dirname( __DIR__ ) . '/blocks/content-listing' ];
$styles = al_test_dependencies( $listing['style'] );
al_test_require( ! in_array( 'al-base', $styles, true ), 'Editor block assets must not import frontend resets.' );
al_test_require( ! str_contains( $GLOBALS['al_test_editor_styles'][0], 'app' ), 'Editor must use a dedicated content stylesheet.' );
al_test_require( str_contains( $GLOBALS['al_test_editor_styles'][0], 'editor' ), 'Editor stylesheet must resolve through manifest.' );
$GLOBALS['al_test_queue'] = array();
al_test_run( 'enqueue_block_assets' );
al_enqueue_listing_assets();
foreach ( $GLOBALS['al_test_queue'] as $handle ) { al_test_require( ! in_array( 'al-base', al_test_dependencies( $handle ), true ), 'Listing render must not indirectly enqueue frontend CSS.' ); }
$GLOBALS['al_test_queue'] = array();
al_test_run( 'wp_enqueue_scripts' );
al_test_require( in_array( 'al-base', $GLOBALS['al_test_queue'], true ), 'Frontend must retain its base stylesheet.' );
echo "Editor/frontend asset isolation contracts passed.\n";
