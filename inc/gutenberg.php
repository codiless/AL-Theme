<?php
defined( 'ABSPATH' ) || exit;
add_action( 'init', function () {
	register_block_pattern_category( 'al-base', array( 'label' => __( 'AL Base', 'al-base' ) ) );
	register_block_style( 'core/group', array( 'name' => 'surface', 'label' => __( 'Surface', 'al-base' ) ) );
	register_block_style( 'core/post-template', array( 'name' => 'cards', 'label' => __( 'Cards', 'al-base' ) ) );
} );
