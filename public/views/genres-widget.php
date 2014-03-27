<?php
$title = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
$list  = ( 1 == $instance['list'] ? true : false );
$css = ( 1 == $instance['css'] ? true : false );
$count = ( 1 == $instance['count'] ? true : false );

$genres = get_terms( array( 'genre' ) );
?>
		<?php echo $title; ?>
<?php
if ( $genres && ! is_wp_error( $genres ) ) :

	$items = array();

	foreach ( $genres as $genre )
		$items[] = array(
			'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', 'wpml' ), $genre->name ),
			'link'        => get_term_link( sanitize_term( $genre, 'genre' ), 'genre' ),
			'title'       => esc_attr( $genre->name . ( 1 == $count ? sprintf( '&nbsp;(%d)', $genre->count ) : '' ) )
		);

	$html = apply_filters( 'wpml_format_widget_lists', $items, $list, $css, __( 'Select a Genre', 'wpml' ) );

	echo $html;
else :
	printf( '<em>%s</em>', __( 'Nothing to display for "Genre" taxonomy.', 'wpml' ) );
endif; ?>