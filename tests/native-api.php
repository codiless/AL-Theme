<?php
/** Verify serialized patterns and MO catalogs against installed WP libraries only. */
$root = rtrim( getenv( 'AL_WP_ROOT' ), '/\\' );
define( 'ABSPATH', $root . '/' );
define( 'WPINC', 'wp-includes' );
require $root . '/wp-includes/compat.php';
require $root . '/wp-includes/class-wp-block-parser-block.php';
require $root . '/wp-includes/class-wp-block-parser-frame.php';
require $root . '/wp-includes/class-wp-block-parser.php';
require $root . '/wp-includes/pomo/mo.php';
function esc_html__( $text, $domain ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function verify_native( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
$mo = new MO();
verify_native( $mo->import_from_file( dirname( __DIR__ ) . '/languages/es_PE.mo' ), 'MO catalog must parse.' );
verify_native( 'Cargar más' === $mo->translate( 'Load more' ), 'Native gettext translation.' );
verify_native( '2 minutos de lectura' === sprintf( $mo->translate_plural( '%s minute read', '%s minutes read', 2 ), 2 ), 'Native gettext plural.' );
$count = 0;
foreach ( glob( dirname( __DIR__ ) . '/patterns/*.php' ) as $file ) {
	ob_start(); include $file; $markup = ob_get_clean();
	$parser = new WP_Block_Parser();
	$blocks = $parser->parse( $markup );
	verify_native( ! empty( $blocks ), 'Pattern must contain blocks: ' . $file );
	$walk = function ( $items ) use ( &$walk, $file ) {
		foreach ( $items as $block ) {
			if ( null === $block['blockName'] ) {
				verify_native( '' === trim( $block['innerHTML'] ), 'Unexpected freeform HTML: ' . $file );
			} else {
				verify_native( str_starts_with( $block['blockName'], 'core/' ) || str_starts_with( $block['blockName'], 'al-base/' ), 'Unknown block namespace.' );
				$walk( $block['innerBlocks'] );
			}
		}
	};
	$walk( $blocks );
	++$count;
}
echo "$count patterns parsed by WordPress; native MO singular and plural translations passed.\n";
