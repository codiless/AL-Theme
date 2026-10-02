<?php
defined( 'ABSPATH' ) || exit;
$a = $args['attributes'];
$result = $args['result'];
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'al-listing', 'style' => $args['style'], 'data-al-listing' => $args['id'], 'id' => $a['anchor'] ?? $args['id'] ) ); ?>>
<div class="al-listing__items al-layout--<?php echo esc_attr( $args['layout'] ); ?>">
<?php foreach ( $result['items'] as $item ) { al_render_card( $item, $a, $result['kind'] ); } ?>
</div>
<?php if ( ! $result['items'] ) : ?><p><?php echo esc_html__( 'No content found.', 'al-base' ); ?></p><?php endif; ?>
<?php if ( 'none' !== $args['mode'] && $result['pages'] > 1 ) { get_template_part( 'components/pagination/listing', null, array( 'page' => $args['page'], 'pages' => $result['pages'], 'param' => $args['param'], 'mode' => $args['mode'], 'anchor' => $a['anchor'] ?? $args['id'] ) ); } ?>
<p class="screen-reader-text al-listing__status" role="status" aria-live="polite" data-loaded="<?php echo esc_attr__( 'More content loaded.', 'al-base' ); ?>" data-error="<?php echo esc_attr__( 'Unable to load content. Follow the next page link to continue.', 'al-base' ); ?>"></p>
</div>
