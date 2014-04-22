<?php
$title = $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
$list  = ( 1 == $instance['list'] ? true : false );
$css = ( 1 == $instance['css'] ? true : false );
$count = ( 1 == $instance['count'] ? true : false );

$collections = get_terms( array( 'collection' ) );
?>
		<?php echo $title; ?>
<?php
if ( $collections && ! is_wp_error( $collections ) ) :

	$items = array();

	foreach ( $collections as $collection )
		$items[] = array(
			'attr_title'  => sprintf( __( 'Permalink for &laquo; %s &raquo;', WPML_SLUG ), $collection->name ),
			'link'        => get_term_link( sanitize_term( $collection, 'collection' ), 'collection' ),
			'title'       => esc_attr( $collection->name . ( $count ? sprintf( '&nbsp;(%d)', $collection->count ) : '' ) )
		);

	$html = apply_filters( 'wpml_format_widget_lists', $items, $list, $css, __( 'Select a Collection', WPML_SLUG ) );

	echo $html;
else :
	printf( '<em>%s</em>', __( 'Nothing to display for "Collection" taxonomy.', WPML_SLUG ) );
endif; ?>