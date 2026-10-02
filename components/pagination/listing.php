<?php
defined( 'ABSPATH' ) || exit;
$page = $args['page'];
$pages = $args['pages'];
$base = remove_query_arg( $args['param'] );
$link = function ( $number ) use ( $args, $base ) { return add_query_arg( $args['param'], $number, $base ) . '#' . rawurlencode( $args['anchor'] ); };
?>
<nav class="al-pagination" aria-label="<?php echo esc_attr__( 'Content pages', 'al-base' ); ?>">
<?php if ( $page > 1 ) : ?><a href="<?php echo esc_url( $link( $page - 1 ) ); ?>" rel="prev"><?php echo esc_html__( 'Previous', 'al-base' ); ?></a><?php endif; ?>
<span><?php echo esc_html( sprintf( __( 'Page %1$s of %2$s', 'al-base' ), number_format_i18n( $page ), number_format_i18n( $pages ) ) ); ?></span>
<?php if ( $page < $pages ) : ?><a href="<?php echo esc_url( $link( $page + 1 ) ); ?>" rel="next" <?php if ( 'load-more' === $args['mode'] ) : ?>data-al-load-more<?php endif; ?>><?php echo esc_html( 'load-more' === $args['mode'] ? __( 'Load more', 'al-base' ) : __( 'Next', 'al-base' ) ); ?></a><?php endif; ?>
</nav>
