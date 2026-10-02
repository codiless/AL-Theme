<?php
defined( 'ABSPATH' ) || exit;
function al_breadcrumb_items() {
	$items = array( array( 'label' => __( 'Home', 'al-base' ), 'url' => home_url( '/' ) ) );
	if ( is_singular() ) {
		$post = get_queried_object();
		$type = get_post_type_object( $post->post_type );
		if ( 'post' === $post->post_type && get_option( 'page_for_posts' ) ) {
			$blog = absint( get_option( 'page_for_posts' ) );
			$items[] = array( 'label' => get_the_title( $blog ), 'url' => get_permalink( $blog ) );
		} elseif ( $type && $type->has_archive ) {
			$items[] = array( 'label' => $type->labels->name, 'url' => get_post_type_archive_link( $post->post_type ) );
		}
		foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor ) {
			$items[] = array( 'label' => get_the_title( $ancestor ), 'url' => get_permalink( $ancestor ) );
		}
		$items[] = array( 'label' => get_the_title( $post ), 'url' => '' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		foreach ( array_reverse( get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' ) ) as $ancestor ) {
			$object = get_term( $ancestor, $term->taxonomy );
			if ( ! $object || is_wp_error( $object ) ) { continue; }
			$url = get_term_link( $object );
			if ( ! is_wp_error( $object ) && ! is_wp_error( $url ) ) { $items[] = array( 'label' => $object->name, 'url' => $url ); }
		}
		$items[] = array( 'label' => single_term_title( '', false ), 'url' => '' );
	} elseif ( is_search() ) {
		$items[] = array( 'label' => sprintf( __( 'Search: %s', 'al-base' ), get_search_query() ), 'url' => '' );
	} elseif ( is_404() ) {
		$items[] = array( 'label' => __( 'Page not found', 'al-base' ), 'url' => '' );
	} elseif ( is_home() ) {
		$items[] = array( 'label' => get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'Latest content', 'al-base' ), 'url' => '' );
	} elseif ( is_archive() ) {
		$items[] = array( 'label' => wp_strip_all_tags( get_the_archive_title() ), 'url' => '' );
	}
	return apply_filters( 'al_breadcrumb_items', $items );
}
function al_breadcrumbs() {
	if ( is_front_page() ) { return; }
	if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<nav class="al-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumbs', 'al-base' ) . '">', '</nav>' ); return; }
	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) { rank_math_the_breadcrumbs(); return; }
	if ( function_exists( 'seopress_display_breadcrumbs' ) ) { seopress_display_breadcrumbs(); return; }
	$items = al_breadcrumb_items();
	get_template_part( 'components/breadcrumbs/default', null, array( 'items' => $items ) );
	// Opt-in only: SEO plugins own schema; core emits no Article/Recipe/etc.
	if ( apply_filters( 'al_breadcrumb_schema_enabled', false ) && ! defined( 'WPSEO_VERSION' ) && ! defined( 'RANK_MATH_VERSION' ) && ! defined( 'SEOPRESS_VERSION' ) ) {
		$entries = array();
		foreach ( $items as $index => $item ) {
			$entry = array( '@type' => 'ListItem', 'position' => $index + 1, 'name' => wp_strip_all_tags( $item['label'] ) );
			if ( ! empty( $item['url'] ) ) { $entry['item'] = esc_url_raw( $item['url'] ); }
			$entries[] = $entry;
		}
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $entries ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . '</script>';
	}
}
