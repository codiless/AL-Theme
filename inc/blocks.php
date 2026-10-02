<?php
defined( 'ABSPATH' ) || exit;
add_action( 'init', function () {
	wp_register_script( 'al-block-editor', al_asset_url( 'assets/src/js/editor.js' ), array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-core-data', 'wp-i18n', 'wp-server-side-render' ), '1.0.0', true );
	wp_set_script_translations( 'al-block-editor', 'al-base', get_template_directory() . '/languages' );
	wp_register_style( 'al-listing', al_asset_url( 'assets/src/css/listing.css' ), array(), '1.0.3' );
	register_block_type( get_template_directory() . '/blocks/content-listing', array( 'title' => __( 'Content Listing', 'al-base' ), 'description' => __( 'Reusable advanced queries with independent layouts and cards.', 'al-base' ), 'editor_script' => 'al-block-editor', 'style' => 'al-listing', 'render_callback' => 'al_render_listing' ) );
	register_block_type( 'al-base/breadcrumbs', array( 'api_version' => 3, 'title' => __( 'Breadcrumbs', 'al-base' ), 'category' => 'theme', 'icon' => 'arrow-right-alt2', 'editor_script' => 'al-block-editor', 'render_callback' => function () { ob_start(); al_breadcrumbs(); return ob_get_clean(); } ) );
} );
add_action( 'enqueue_block_editor_assets', function () {
	$fields = array();
	foreach ( al_listing_fields() as $key => $field ) {
		$fields[] = array( 'value' => $key, 'label' => $field['label'] ?? $key, 'relationship' => ! empty( $field['relationship'] ), 'sortable' => ! empty( $field['sortable'] ) );
	}
	wp_add_inline_script( 'al-block-editor', 'window.alBaseEditor = ' . wp_json_encode( array( 'fields' => $fields, 'variants' => al_card_variants() ) ) . ';', 'before' );
} );
function al_render_listing( $attributes, $content, $block ) {
	static $instance = 0;
	++$instance;
	$id = sanitize_html_class( $attributes['listingId'] ?? '' );
	$id = $id ?: 'listing-' . $instance;
	$param = 'al-page-' . $id;
	// Public page number only; no endpoint, nonce or user/session dependence.
	$page = isset( $_GET[ $param ] ) && is_scalar( $_GET[ $param ] ) ? max( 1, min( 10000, absint( wp_unslash( $_GET[ $param ] ) ) ) ) : 1;
	$context_id = absint( $block->context['postId'] ?? ( is_singular() ? get_queried_object_id() : 0 ) );
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST && current_user_can( 'edit_posts' ) && isset( $_GET['post_id'] ) ) {
		$context_id = absint( $_GET['post_id'] );
	}
	$result = al_listing_query( $attributes, $context_id, $page );
	$mode = $attributes['pagination'] ?? 'none';
	al_enqueue_listing_assets( 'load-more' === $mode );
	$layout = in_array( $attributes['layout'] ?? '', array( 'grid', 'list', 'horizontal', 'featured', 'compact' ), true ) ? $attributes['layout'] : 'grid';
	$gap = in_array( $attributes['gap'] ?? '', array( 'xs', 's', 'm', 'l', 'xl', '2xl' ), true ) ? $attributes['gap'] : 'm';
	$gap = '2xl' === $gap ? '2-xl' : $gap;
	$style = sprintf( '--al-columns:%d;--al-tablet-columns:%d;--al-mobile-columns:%d;--al-gap:var(--wp--preset--spacing--%s)', max( 1, min( 6, absint( $attributes['columns'] ?? 3 ) ) ), max( 1, min( 4, absint( $attributes['tabletColumns'] ?? 2 ) ) ), max( 1, min( 2, absint( $attributes['mobileColumns'] ?? 1 ) ) ), $gap );
	ob_start();
	get_template_part( 'components/listings/default', null, array( 'attributes' => $attributes, 'result' => $result, 'layout' => $layout, 'style' => $style, 'id' => $id, 'page' => $page, 'param' => $param, 'mode' => $mode ) );
	return ob_get_clean();
}
