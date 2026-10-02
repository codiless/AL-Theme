<?php
defined( 'ABSPATH' ) || exit;
/** Native menu walker with independent links and disclosure buttons. */
class AL_Nav_Walker extends Walker_Nav_Menu {
	private array $submenu_ids = array();
	private bool $mega_root = false;
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		if ( 0 === $depth ) { $this->mega_root = in_array( 'al-mega-menu', (array) $data_object->classes, true ); }
		$children = in_array( 'menu-item-has-children', (array) $data_object->classes, true );
		if ( $children ) { $this->submenu_ids[ $depth ] = wp_unique_id( 'al-submenu-' . absint( $data_object->ID ) . '-' ); }
		parent::start_el( $output, $data_object, $depth, $args, $current_object_id );
		if ( $children && ! ( $this->mega_root && $depth > 0 ) ) {
			$output .= '<button class="al-submenu-toggle" type="button" aria-expanded="false" aria-controls="' . esc_attr( $this->submenu_ids[ $depth ] ) . '" aria-label="' . esc_attr( sprintf( __( 'Toggle submenu: %s', 'al-base' ), wp_strip_all_tags( $data_object->title ) ) ) . '"><svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m6 9 6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"/></svg></button>';
		}
	}
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$classes = apply_filters( 'nav_menu_submenu_css_class', array( 'sub-menu' ), $args, $depth );
		$attributes = apply_filters( 'nav_menu_submenu_attributes', array( 'class' => implode( ' ', $classes ), 'id' => $this->submenu_ids[ $depth ] ?? wp_unique_id( 'al-submenu-' ) ), $args, $depth );
		// Keep the disclosure button and its list connected after plugin filters.
		$attributes['id'] = $this->submenu_ids[ $depth ] ?? $attributes['id'];
		$output .= '<ul';
		foreach ( $attributes as $name => $value ) {
			if ( is_scalar( $value ) && '' !== (string) $value && preg_match( '/^[a-zA-Z][a-zA-Z0-9_:.-]*$/', $name ) ) {
				$output .= ' ' . $name . '="' . esc_attr( $value ) . '"';
			}
		}
		$output .= '>';
	}
}
/** Useful navigation before a project assigns its own primary menu. */
function al_primary_menu_fallback() {
	echo '<ul class="primary-menu"><li><a href="' . esc_url( home_url( '/' ) ) . '"' . ( is_front_page() ? ' aria-current="page"' : '' ) . '>' . esc_html__( 'Home', 'al-base' ) . '</a></li>';
	wp_list_pages( array( 'title_li' => '', 'depth' => 1 ) );
	echo '</ul>';
}
