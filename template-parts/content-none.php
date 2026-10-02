<?php defined( 'ABSPATH' ) || exit; ?>
<p><?php echo esc_html__( 'No content found. Try another search.', 'al-base' ); ?></p>
<?php if ( ! is_search() ) { get_search_form(); } ?>
