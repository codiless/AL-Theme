<?php
/**
 * Title: Taxonomy grid
 * Slug: al-base/category-grid
 * Categories: al-base, query
 */
?>
<!-- wp:group {"align":"wide","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide">
<!-- wp:heading --><h2 class="wp-block-heading"><?php echo esc_html__( 'Explore topics', 'al-base' ); ?></h2><!-- /wp:heading -->
<!-- wp:al-base/content-listing {"source":"terms","taxonomy":"category","orderBy":"name","order":"ASC","headingLevel":3,"elements":["title","excerpt","button"],"align":"wide"} /-->
</div><!-- /wp:group -->
