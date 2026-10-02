<?php
/** Isolated contracts for the engine; no WordPress database is required. */
define( 'ABSPATH', __DIR__ );
function apply_filters( $hook, $value, ...$args ) { return $value; }
function absint( $value ) { return abs( (int) $value ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( $value ) ); }
function get_post_type_object( $type ) { return in_array( $type, array( 'post', 'page', 'example' ), true ) ? (object) array( 'public' => true ) : null; }
function taxonomy_exists( $taxonomy ) { return 'category' === $taxonomy; }
function get_taxonomy( $taxonomy ) { return (object) array( 'public' => true ); }
function is_object_in_taxonomy( $type, $taxonomy ) { return 'category' === $taxonomy; }
function wp_get_post_parent_id( $id ) { return 0; }
function wp_get_object_terms( $id, $taxonomy, $args ) { return array(); }
function is_wp_error( $value ) { return false; }
function wp_count_terms( $args ) { return 17; }
function get_terms( $args ) { $GLOBALS['term_args'] = $args; return array(); }
function get_post_meta( $id, $key, $single ) { return 'linked' === $key ? array( 2, 4, 6 ) : 'visible'; }
function al_listing_fields() { return array( 'related' => array( 'key' => 'linked', 'relationship' => true ), 'cost' => array( 'key' => 'public_cost', 'sortable' => true, 'numeric' => true ) ); }
class WP_Query {
	public $posts = array();
	public $found_posts = 17;
	public function __construct( $args ) { $GLOBALS['query_args'] = $args; }
}
require dirname( __DIR__ ) . '/inc/query.php';
function verify( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
$result = al_listing_query( array( 'perPage' => 6, 'offset' => 2, 'pagination' => 'pagination' ), 0, 2 );
verify( 8 === $GLOBALS['query_args']['offset'], 'Offset must advance by page size.' );
verify( 3 === $result['pages'], 'Offset must be deducted from pagination total.' );
al_listing_query( array( 'source' => 'manual', 'include' => array( 2, 4, 6 ), 'exclude' => array( 4 ) ) );
verify( array( 2, 6 ) === $GLOBALS['query_args']['post__in'], 'Manual exclusions must survive WP post__in semantics.' );
al_listing_query( array( 'source' => 'manual' ) );
verify( array( 0 ) === $GLOBALS['query_args']['post__in'], 'Empty manual selection must never list all posts.' );
al_listing_query( array( 'source' => 'children', 'postType' => 'page' ), 20 );
verify( 20 === $GLOBALS['query_args']['post_parent'], 'Children use context ID.' );
al_listing_query( array( 'source' => 'siblings', 'postType' => 'page' ), 20 );
verify( 0 === $GLOBALS['query_args']['post_parent'], 'Root siblings must retain parent zero.' );
verify( in_array( 20, $GLOBALS['query_args']['post__not_in'], true ), 'Siblings exclude current content.' );
al_listing_query( array( 'source' => 'children', 'postType' => 'page' ), 0 );
verify( -1 === $GLOBALS['query_args']['post_parent'], 'Missing context must not return root pages.' );
al_listing_query( array( 'source' => 'related' ), 20 );
verify( array( 0 ) === $GLOBALS['query_args']['post__in'], 'No shared terms must return no related posts.' );
al_listing_query( array( 'source' => 'relationship', 'relationField' => 'related' ), 20 );
verify( array( 2, 4, 6 ) === $GLOBALS['query_args']['post__in'], 'Relationship field resolves registered IDs.' );
al_listing_query( array( 'source' => 'relationship', 'relationField' => '_secret' ), 20 );
verify( array( 0 ) === $GLOBALS['query_args']['post__in'], 'Unregistered relationship field must remain private.' );
al_listing_query( array( 'sortField' => 'cost' ) );
verify( 'meta_value_num' === $GLOBALS['query_args']['orderby'], 'Numeric field sorting.' );
al_listing_query( array( 'orderBy' => 'rand', 'pagination' => 'load-more' ) );
verify( 'date' === $GLOBALS['query_args']['orderby'], 'Paginated ordering must be stable.' );
$result = al_listing_query( array( 'source' => 'terms', 'perPage' => 6, 'offset' => 2, 'pagination' => 'pagination' ), 0, 2 );
verify( 8 === $GLOBALS['term_args']['offset'] && 3 === $result['pages'], 'Term pagination follows the same offset contract.' );
verify( array() === al_listing_query( array( 'postType' => 'private' ) )['items'], 'Nonpublic post types are rejected.' );
echo "15 query engine contracts passed.\n";
