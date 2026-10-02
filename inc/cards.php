<?php
defined( 'ABSPATH' ) || exit;
function al_card_variants() {
	return apply_filters( 'al_card_variants', array( 'default' => __( 'Default', 'al-base' ), 'minimal' => __( 'Minimal', 'al-base' ), 'horizontal' => __( 'Horizontal', 'al-base' ), 'compact' => __( 'Compact', 'al-base' ), 'featured' => __( 'Featured', 'al-base' ) ) );
}
function al_render_card( $item, $attributes = array(), $kind = 'post' ) {
	$variant = sanitize_key( $attributes['cardVariant'] ?? 'default' );
	if ( ! isset( al_card_variants()[ $variant ] ) ) {
		$variant = 'default';
	}
	get_template_part( 'components/cards/' . $variant, null, array( 'item' => $item, 'attributes' => $attributes, 'kind' => $kind, 'variant' => $variant ) );
}
