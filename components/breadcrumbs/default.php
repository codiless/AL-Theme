<?php defined( 'ABSPATH' ) || exit; ?>
<nav class="al-breadcrumbs" aria-label="<?php echo esc_attr__( 'Breadcrumbs', 'al-base' ); ?>"><ol>
<?php foreach ( $args['items'] as $item ) : ?>
<li><?php if ( ! empty( $item['url'] ) ) : ?><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a><?php else : ?><span aria-current="page"><?php echo esc_html( $item['label'] ); ?></span><?php endif; ?></li>
<?php endforeach; ?>
</ol></nav>
