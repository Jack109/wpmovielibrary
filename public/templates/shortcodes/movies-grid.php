<?php
/**
 * Movies Shortcode view Template
 * 
 * Showing a grid of movies.
 * 
 * @since    3.0
 * 
 * @uses    $grid
 * @uses    $movies
 */
?>

	<div id="wpmoly-grid-<?php echo $grid->id; ?>" class="wpmoly shortcode movies grid theme-<?php echo $grid->theme; ?> <?php echo $grid->columns; ?>-columns" data-columns="<?php echo $grid->columns; ?>" data-rows="<?php echo $grid->rows; ?>" data-column-width="<?php echo $grid->column_width; ?>" data-row-height="<?php echo $grid->row_height; ?>">
		<div class="grid-menu clearfix">
			<button type="button" data-action="grid-menu" class="button left"><span class="wpmolicon icon-order"></span></button>
			<button type="button" data-action="grid-settings" class="button right"><span class="wpmolicon icon-settings"></span></button>
		</div>
		<div class="grid-content grid clearfix">

<?php
if ( $movies->has_items() ) :
	while ( $movies->has_items() ) :
		$movie = $movies->the_item();
?>
			<div class="movie" data-width="<?php echo $grid->column_width; ?>" data-height="<?php echo $grid->row_height; ?>">
				<div class="movie-poster" style="background-image:url(<?php $movie->get_poster()->render( 'medium' ); ?>)">
					<a href="<?php echo get_the_permalink( $movie->id ); ?>"></a>
				</div>
				<div class="movie-title"><a href="<?php echo get_the_permalink( $movie->id ); ?>"><?php $movie->the( 'title' ); ?></a></div>
				<div class="movie-genres"><?php echo apply_filters( 'wpmoly/shortcode/format/genres/value', $movie->genres ); ?></div>
				<div class="movie-runtime"><?php echo apply_filters( 'wpmoly/shortcode/format/runtime/value', $movie->runtime ); ?></div>
			</div>
<?php
	endwhile;
endif;
?>
		</div>
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
	</div>
