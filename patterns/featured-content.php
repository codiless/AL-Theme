<?php
/**
 * Title: Featured content
 * Slug: al-base/featured-content
 * Categories: al-base, query
 */
?>
<!-- wp:group {"align":"wide","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide">
<!-- wp:heading --><h2 class="wp-block-heading"><?php echo esc_html__( 'Featured content', 'al-base' ); ?></h2><!-- /wp:heading -->
<!-- wp:query {"queryId":1,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"align":"wide"} --><div class="wp-block-query alignwide">
<!-- wp:post-template --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /--></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:post-title {"isLink":true,"level":3} /--><!-- wp:post-excerpt /--></div><!-- /wp:column --></div><!-- /wp:columns --><!-- /wp:post-template -->
</div><!-- /wp:query --></div><!-- /wp:group -->
