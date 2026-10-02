<?php
defined( 'ABSPATH' ) || exit;
/** Resolve parent assets; child themes override components and tokens independently. */
function al_asset_path( $entry ) {
	static $manifest;
	if ( null === $manifest ) {
		$file = get_template_directory() . '/assets/dist/manifest.json';
		$manifest = is_readable( $file ) ? json_decode( file_get_contents( $file ), true ) : array();
	}
	return ! empty( $manifest[ $entry ]['file'] ) ? 'assets/dist/' . $manifest[ $entry ]['file'] : $entry;
}
function al_asset_url( $entry ) {
	return get_template_directory_uri() . '/' . al_asset_path( $entry );
}
add_action( 'init', function () {
	wp_register_style( 'al-base', al_asset_url( 'assets/src/css/app.css' ), array(), '1.0.3' );
} );
// Classic themes otherwise enqueue every registered block stylesheet globally.
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
function al_enqueue_listing_assets( $load_more = false ) {
	// Shared block CSS must never pull frontend resets into wp-admin.
	wp_enqueue_style( 'al-listing', al_asset_url( 'assets/src/css/listing.css' ), array(), '1.0.3' );
	if ( $load_more ) {
		wp_enqueue_script_module( 'al-load-more', al_asset_url( 'assets/src/js/load-more.js' ), array(), '1.0.0' );
	}
}
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'al-base', al_asset_url( 'assets/src/css/app.css' ), array(), '1.0.3' );
	if ( is_home() || is_archive() || is_search() || ( is_singular() && has_block( 'al-base/content-listing', get_queried_object_id() ) ) ) {
		al_enqueue_listing_assets();
	}
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
} );
