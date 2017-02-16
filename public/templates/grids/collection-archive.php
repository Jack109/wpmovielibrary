<?php
/**
 * Collections Grid 'archive' mode template.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $items
 */
?>

	<div id="wpmoly-grid-<?php echo $grid->id; ?>" class="wpmoly grid-<?php echo $grid->id; ?> shortcode collections grid archive theme-<?php echo $grid->get_theme(); ?>" data-grid="<?php echo $grid->id; ?>">
		<div class="grid-json"><?php echo $grid->toJSON(); ?></div>
		<div class="grid-menu settings-menu"></div>
		<div class="grid-settings"></div>
		<div class="grid-customs"></div>
		<div class="grid-content archive clearfix">

<?php
if ( $items->has_items() ) :
	while ( $items->has_items() ) :
		$collection = $items->the_item();
?>
			<div class="node term-node collection">
<?php
					$headbox = get_collection_headbox_template( $collection );
					echo $headbox->render();
?>
			</div>
<?php
	endwhile;
endif;
?>
		</div>
<?php if ( $grid->show_pagination() ) : ?>
		<div class="grid-menu pagination-menu clearfix">
<?php if ( ! $grid->is_first_page() ) : ?>
			<a href="<?php echo esc_url( $grid->get_previous_page_url() ); ?>" data-action="grid-paginate" data-value="prev" class="button left"><span class="wpmolicon icon-arrow-left"></span></a>
<?php else : ?>
			<a class="button left disabled"><span class="wpmolicon icon-arrow-left"></span></a>
<?php endif; ?>
			<div class="pagination-menu">Page <span class="current-page"><input type="text" size="1" data-action="grid-paginate" value="<?php echo esc_attr( $grid->get_current_page() ); ?>" /></span> of <span class="total-pages"><?php echo esc_attr( $grid->get_total_pages() ); ?></span></div>
<?php if ( ! $grid->is_last_page() ) : ?>
			<a href="<?php echo esc_url( $grid->get_next_page_url() ); ?>" data-action="grid-paginate" data-value="next" class="button right"><span class="wpmolicon icon-arrow-right"></span></a>
<?php else : ?>
			<a class="button right disabled"><span class="wpmolicon icon-arrow-right"></span></a>
<?php endif; ?>
		</div>
<?php endif; ?>
	</div>
