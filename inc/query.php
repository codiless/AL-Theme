<?php
defined( 'ABSPATH' ) || exit;
function al_ids( $value ) {
	return array_values( array_filter( array_map( 'absint', is_array( $value ) ? $value : explode( ',', (string) $value ) ) ) );
}
/** One query engine; presentation never builds database queries. */
function al_listing_query( $attributes, $context_id = 0, $page = 1 ) {
	$source = $attributes['source'] ?? 'posts';
	$limit = max( 1, min( 100, absint( $attributes['perPage'] ?? 6 ) ) );
	$offset = max( 0, (int) ( $attributes['offset'] ?? 0 ) );
	$paged = 'none' !== ( $attributes['pagination'] ?? 'none' );
	$page = $paged ? max( 1, min( 10000, absint( $page ) ) ) : 1;
	$taxonomy = sanitize_key( $attributes['taxonomy'] ?? 'category' );
	$terms = al_ids( $attributes['terms'] ?? array() );
	$order = 'ASC' === ( $attributes['order'] ?? '' ) ? 'ASC' : 'DESC';
	$orderby = $attributes['orderBy'] ?? 'date';
	if ( 'terms' === $source ) {
		if ( ! taxonomy_exists( $taxonomy ) || ! get_taxonomy( $taxonomy )->public ) {
			return array( 'items' => array(), 'pages' => 0, 'kind' => 'term' );
		}
		$args = array( 'taxonomy' => $taxonomy, 'hide_empty' => (bool) ( $attributes['hideEmpty'] ?? true ), 'include' => $terms, 'orderby' => in_array( $orderby, array( 'name', 'count', 'slug', 'include' ), true ) ? $orderby : 'name', 'order' => $order );
		$args = apply_filters( 'al_listing_term_query_args', $args, $attributes );
		$total = wp_count_terms( array_merge( $args, array( 'fields' => 'count' ) ) );
		$items = get_terms( array_merge( $args, array( 'number' => $limit, 'offset' => $offset + ( $page - 1 ) * $limit ) ) );
		return array( 'items' => is_wp_error( $items ) ? array() : $items, 'pages' => is_wp_error( $total ) ? 0 : (int) ceil( max( 0, (int) $total - $offset ) / $limit ), 'kind' => 'term' );
	}
	$type = sanitize_key( $attributes['postType'] ?? 'post' );
	$object = get_post_type_object( $type );
	if ( ! $object || ! $object->public ) {
		return array( 'items' => array(), 'pages' => 0, 'kind' => 'post' );
	}
	$args = array( 'post_type' => $type, 'post_status' => 'publish', 'has_password' => false, 'posts_per_page' => $limit, 'offset' => $offset + ( $page - 1 ) * $limit, 'ignore_sticky_posts' => true, 'no_found_rows' => ! $paged, 'order' => $order, 'orderby' => in_array( $orderby, array( 'date', 'title', 'menu_order', 'rand', 'post__in' ), true ) ? $orderby : 'date', 'post__not_in' => al_ids( $attributes['exclude'] ?? array() ) );
	$include = al_ids( $attributes['include'] ?? array() );
	if ( $include ) {
		$args['post__in'] = $include;
	}
	if ( 'manual' === $source ) {
		$args['post__in'] = $include ?: array( 0 );
		$args['orderby'] = 'post__in';
	}
	if ( in_array( $source, array( 'children', 'siblings' ), true ) ) {
		$parent = absint( $attributes['parent'] ?? 0 ) ?: $context_id;
		if ( 'siblings' === $source ) {
			$parent = $context_id ? wp_get_post_parent_id( $context_id ) : -1;
			$args['post__not_in'][] = $context_id;
		}
		$args['post_parent'] = $parent ?: -1;
		if ( 'siblings' === $source && $context_id ) {
			$args['post_parent'] = wp_get_post_parent_id( $context_id );
		}
	}
	if ( 'related' === $source ) {
		$terms = $context_id ? wp_get_object_terms( $context_id, $taxonomy, array( 'fields' => 'ids' ) ) : array();
		$terms = is_wp_error( $terms ) ? array() : $terms;
		$args['post__not_in'][] = $context_id;
		if ( ! $terms ) {
			$args['post__in'] = array( 0 );
		}
	}
	if ( 'relationship' === $source ) {
		$registry = al_listing_fields();
		$key = sanitize_key( $attributes['relationField'] ?? '' );
		$field = $registry[ $key ] ?? array();
		$ids = array();
		if ( $context_id && ! empty( $field['relationship'] ) ) {
			$meta_key = $field['key'] ?? $key;
			$value = 'acf' === ( $field['source'] ?? '' ) && function_exists( 'get_field' ) ? get_field( $meta_key, $context_id, false ) : get_post_meta( $context_id, $meta_key, true );
			$ids = al_ids( is_array( $value ) ? $value : array( $value ) );
		}
		$args['post__in'] = $ids ?: array( 0 );
		$args['orderby'] = 'post__in';
	}
	if ( $terms && taxonomy_exists( $taxonomy ) && is_object_in_taxonomy( $type, $taxonomy ) ) {
		$args['tax_query'] = array( array( 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => al_ids( $terms ) ) );
	} elseif ( $terms ) {
		$args['post__in'] = array( 0 );
	}
	if ( ! empty( $attributes['author'] ) ) {
		$args['author'] = absint( $attributes['author'] );
	}
	$fields = al_listing_fields();
	$sort = sanitize_key( $attributes['sortField'] ?? '' );
	if ( $sort && ! empty( $fields[ $sort ]['sortable'] ) ) {
		$args['meta_key'] = $fields[ $sort ]['key'] ?? $sort;
		$args['orderby'] = ! empty( $fields[ $sort ]['numeric'] ) ? 'meta_value_num' : 'meta_value';
	}
	// WP_Query ignores post__not_in when post__in is supplied.
	if ( isset( $args['post__in'] ) ) {
		$args['post__in'] = array_values( array_diff( $args['post__in'], $args['post__not_in'] ) ) ?: array( 0 );
	}
	// Random ordering across paginated requests duplicates/skips content.
	if ( $paged && 'rand' === $args['orderby'] ) { $args['orderby'] = 'date'; }
	$query = new WP_Query( apply_filters( 'al_listing_query_args', $args, $attributes, $context_id ) );
	return array( 'items' => $query->posts, 'pages' => $paged ? (int) ceil( max( 0, $query->found_posts - $offset ) / $limit ) : 1, 'kind' => 'post' );
}
