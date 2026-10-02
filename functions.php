<?php
/** Theme bootstrap. */
defined( 'ABSPATH' ) || exit;
foreach ( array( 'setup', 'assets', 'navigation', 'fields', 'query', 'cards', 'blocks', 'breadcrumbs', 'gutenberg', 'compatibility' ) as $al_module ) {
	require_once get_template_directory() . '/inc/' . $al_module . '.php';
}
