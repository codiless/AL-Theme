<?php
defined( 'ABSPATH' ) || exit;
/** Explicit public field registry. Never expose arbitrary/private post meta. */
function al_listing_fields() {
	return apply_filters( 'al_listing_fields', array() );
}
function al_dynamic_value( $post_id, $key ) {
	$registry = al_listing_fields();
	if ( ! isset( $registry[ $key ] ) || ! is_array( $registry[ $key ] ) ) {
		return '';
	}
	$field = $registry[ $key ];
	$meta_key = $field['key'] ?? $key;
	if ( isset( $field['callback'] ) && is_callable( $field['callback'] ) ) {
		$value = call_user_func( $field['callback'], $post_id, $field );
	} elseif ( 'acf' === ( $field['source'] ?? 'meta' ) ) {
		$value = function_exists( 'get_field' ) ? get_field( $meta_key, $post_id ) : get_post_meta( $post_id, $meta_key, true );
	} else {
		$value = get_post_meta( $post_id, $meta_key, true );
	}
	return is_scalar( $value ) ? (string) $value : '';
}
add_action( 'init', function () {
	if ( function_exists( 'register_block_bindings_source' ) ) {
		register_block_bindings_source( 'al-base/field', array(
			'label' => __( 'AL public field', 'al-base' ),
			'uses_context' => array( 'postId' ),
			'get_value_callback' => function ( $args, $block ) {
				return al_dynamic_value( absint( $block->context['postId'] ?? get_the_ID() ), sanitize_key( $args['key'] ?? '' ) );
			},
		) );
	}
} );
