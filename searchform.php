<?php defined( 'ABSPATH' ) || exit; $al_search_id = wp_unique_id( 'al-search-' ); ?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
<label for="<?php echo esc_attr( $al_search_id ); ?>" class="screen-reader-text"><?php echo esc_html__( 'Search the site', 'al-base' ); ?></label>
<input type="search" id="<?php echo esc_attr( $al_search_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php echo esc_attr__( 'Search…', 'al-base' ); ?>" required>
<button type="submit"><?php echo esc_html__( 'Search', 'al-base' ); ?></button>
</form>
