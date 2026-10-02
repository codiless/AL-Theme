<?php
defined( 'ABSPATH' ) || exit;
/** Optional language integrations; empty output with no multilingual plugin. */
function al_language_switcher() {
	if ( function_exists( 'pll_the_languages' ) ) {
		$languages = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 1 ) );
	} elseif ( has_filter( 'wpml_active_languages' ) ) {
		$languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 1 ) );
	} else { return; }
	if ( ! $languages ) { return; }
	echo '<nav aria-label="' . esc_attr__( 'Languages', 'al-base' ) . '"><ul class="al-languages">';
	foreach ( $languages as $language ) {
		$current = ! empty( $language['current_lang'] ) || ! empty( $language['active'] );
		echo '<li><a href="' . esc_url( $language['url'] ) . '" hreflang="' . esc_attr( $language['slug'] ?? $language['language_code'] ?? '' ) . '"' . ( $current ? ' aria-current="true"' : '' ) . '>' . esc_html( $language['name'] ?? $language['native_name'] ?? '' ) . '</a></li>';
	}
	echo '</ul></nav>';
}
