<?php
defined( 'ABSPATH' ) || exit;
$item = $args['item'];
$a = $args['attributes'];
$term = 'term' === $args['kind'];
$url = $term ? get_term_link( $item ) : get_permalink( $item );
if ( is_wp_error( $url ) ) { return; }
$title = $term ? $item->name : get_the_title( $item );
$title = $title ?: __( 'Untitled', 'al-base' );
$elements = $a['elements'] ?? array( 'image', 'category', 'title', 'excerpt', 'date', 'button' );
$elements = array_unique( array_intersect( (array) $elements, array( 'image', 'category', 'title', 'excerpt', 'author', 'date', 'readingTime', 'fields', 'button', 'badge' ) ) );
$heading = 'h' . max( 2, min( 6, absint( $a['headingLevel'] ?? 2 ) ) );
?>
<article class="al-card al-card--<?php echo esc_attr( $args['variant'] ); ?>">
<?php do_action( 'al_before_card', $item, $a ); ?>
<?php foreach ( $elements as $element ) : ?>
	<?php if ( 'image' === $element && ! $term && has_post_thumbnail( $item ) ) : ?>
		<a class="al-card__image" href="<?php echo esc_url( $url ); ?>" tabindex="-1" aria-hidden="true"><?php echo wp_get_attachment_image( get_post_thumbnail_id( $item ), 'medium_large', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 600px) 100vw, (max-width: 1000px) 50vw, 33vw' ) ); ?></a>
	<?php elseif ( 'title' === $element ) : ?>
		<<?php echo $heading; ?> class="al-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></<?php echo $heading; ?>>
	<?php elseif ( 'excerpt' === $element ) : ?>
		<p class="al-card__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $term ? $item->description : get_the_excerpt( $item ), true ), 24 ) ); ?></p>
	<?php elseif ( 'date' === $element && ! $term ) : ?>
		<time class="al-card__meta" datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $item ) ); ?>"><?php echo esc_html( get_the_date( '', $item ) ); ?></time>
	<?php elseif ( 'author' === $element && ! $term ) : ?>
		<span class="al-card__meta"><?php echo esc_html( get_the_author_meta( 'display_name', $item->post_author ) ); ?></span>
	<?php elseif ( 'category' === $element && ! $term ) : ?>
		<?php $tax = sanitize_key( $a['taxonomy'] ?? 'category' ); $categories = get_the_terms( $item, $tax ); ?>
		<?php if ( $categories && ! is_wp_error( $categories ) ) : ?><span class="al-card__meta"><?php echo esc_html( $categories[0]->name ); ?></span><?php endif; ?>
	<?php elseif ( 'readingTime' === $element && ! $term ) : ?>
		<span class="al-card__meta"><?php $minutes = max( 1, (int) ceil( count( preg_split( '/\s+/u', wp_strip_all_tags( strip_shortcodes( $item->post_content ) ), -1, PREG_SPLIT_NO_EMPTY ) ) / 200 ) ); echo esc_html( sprintf( _n( '%s minute read', '%s minutes read', $minutes, 'al-base' ), number_format_i18n( $minutes ) ) ); ?></span>
	<?php elseif ( 'fields' === $element && ! $term ) : ?>
		<?php $registry = al_listing_fields(); foreach ( (array) ( $a['fields'] ?? array() ) as $key ) : $value = al_dynamic_value( $item->ID, $key ); if ( '' === $value ) { continue; } ?>
		<p class="al-card__meta"><span><?php echo esc_html( $registry[ $key ]['label'] ?? $key ); ?>:</span> <?php echo esc_html( $value ); ?></p>
		<?php endforeach; ?>
	<?php elseif ( 'badge' === $element && ! $term ) : ?>
		<?php $badge = al_dynamic_value( $item->ID, sanitize_key( $a['badgeField'] ?? '' ) ); if ( '' !== $badge ) : ?><span class="al-card__badge"><?php echo esc_html( $badge ); ?></span><?php endif; ?>
	<?php elseif ( 'button' === $element ) : ?>
		<a class="al-card__button" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html__( 'Read more', 'al-base' ); ?><span class="screen-reader-text">: <?php echo esc_html( $title ); ?></span> <span aria-hidden="true">→</span></a>
	<?php endif; ?>
<?php endforeach; ?>
<?php do_action( 'al_card_meta', $item, $a ); do_action( 'al_after_card', $item, $a ); ?>
</article>
