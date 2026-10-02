<?php
/**
 * Title: Content and sidebar
 * Slug: al-base/content-sidebar
 * Categories: al-base
 */
?>
<!-- wp:columns {"align":"wide"} --><div class="wp-block-columns alignwide">
<!-- wp:column {"width":"66.66%"} --><div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:heading --><h2 class="wp-block-heading"><?php echo esc_html__( 'Main content', 'al-base' ); ?></h2><!-- /wp:heading --><!-- wp:paragraph --><p><?php echo esc_html__( 'Add your content here.', 'al-base' ); ?></p><!-- /wp:paragraph --></div><!-- /wp:column -->
<!-- wp:column {"width":"33.33%"} --><div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:group {"tagName":"aside","className":"is-style-surface","layout":{"type":"constrained"}} --><aside class="wp-block-group is-style-surface"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading"><?php echo esc_html__( 'Useful links', 'al-base' ); ?></h3><!-- /wp:heading --><!-- wp:page-list /--></aside><!-- /wp:group --></div><!-- /wp:column -->
</div><!-- /wp:columns -->
