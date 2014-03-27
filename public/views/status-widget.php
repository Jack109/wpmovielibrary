<?php
$title = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
$description = $instance['description'];
$type = $instance['type'];
$list = ( 1 == $instance['list'] ? true : false );
$css = ( 1 == $instance['css'] ? true : false );
$thumbnails = ( 1 == $instance['thumbnails'] ? true : false );
$status_only = ( 1 == $instance['status_only'] ? true : false );

?>
		<?php echo $title; ?>
<?php

if ( $status_only ) :

	$status = apply_filters( 'wpml_get_available_movie_status', null );

	if ( ! empty( $status ) ) :

		$items = array();

		foreach ( $status as $slug => $status_title )
			$items[] = array(
				'ID'          => $slug,
				'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', 'wpml' ), $status_title ),
				'link'        => home_url( "/movies/{$slug}/" ),
				'title'       => esc_attr( $status_title ),
			);

		$html = apply_filters( 'wpml_format_widget_lists', $items, $list, $css, __( 'Select a Movie', 'wpml' ) );

		echo $html;
	else :
		printf( '<em>%s</em>', __( 'Nothing to display.', 'wpml' ) );
	endif;

else :
	$movies = apply_filters( 'wpml_get_movies_from_status', $type );

	if ( ! empty( $movies ) ) :

		$items = array();

		foreach ( $movies as $movie )
			$items[] = array(
				'ID'          => $movie->ID,
				'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', 'wpml' ), $movie->post_title ),
				'link'        => get_permalink( $movie->ID ),
				'title'       => esc_attr( $movie->post_title ),
			);

		if ( $thumbnails )
			$html = apply_filters( 'wpml_format_widget_lists_thumbnails', $items );
		else
			$html = apply_filters( 'wpml_format_widget_lists', $items, $list, $css, __( 'Select a Movie', 'wpml' ) );

		echo $html;
	else :
		printf( '<em>%s</em>', __( 'Nothing to display.', 'wpml' ) );
	endif;
endif;
?>