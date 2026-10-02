<?php
/**
 * Title: Latest content (native Query Loop)
 * Slug: al-base/latest-content
 * Categories: al-base, query
 */
?>
<!-- wp:group {"align":"wide","anchor":"content","layout":{"type":"constrained"}} --><div class="wp-block-group alignwide" id="content">
<!-- wp:heading --><h2 class="wp-block-heading"><?php echo esc_html__( 'Latest content', 'al-base' ); ?></h2><!-- /wp:heading -->
<!-- wp:query {"queryId":0,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"align":"wide","className":"al-native-query"} --><div class="wp-block-query alignwide al-native-query">
<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /-->
<!-- wp:post-title {"isLink":true,"level":3} /-->
<!-- wp:post-excerpt /--><!-- wp:post-date /-->
<!-- /wp:post-template -->
<!-- wp:query-pagination --><!-- wp:query-pagination-previous /--><!-- wp:query-pagination-numbers /--><!-- wp:query-pagination-next /--><!-- /wp:query-pagination -->
<!-- wp:query-no-results --><!-- wp:paragraph --><p><?php echo esc_html__( 'No content found.', 'al-base' ); ?></p><!-- /wp:paragraph --><!-- /wp:query-no-results -->
</div><!-- /wp:query --></div><!-- /wp:group -->
