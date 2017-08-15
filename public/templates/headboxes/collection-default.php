<?php
/**
 * Collection Headbox default template.
 * 
 * @since    3.0
 * 
 * @uses    $headbox
 * @uses    $collection
 * @uses    $is_json
 */

if ( ! $is_json ) :
?>

	<div id="collection-headbox-<?php echo $headbox->id; ?>" class="wpmoly term-headbox collection-headbox theme-<?php echo $headbox->get_theme(); ?>">
		<div class="headbox-header">
			<div class="headbox-thumbnail">
				<img src="<?php echo $collection->get_thumbnail(); ?>" alt="" />
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-titles">
				<div class="headbox-title">
					<div class="term-title collection-title"><a href="<?php echo get_term_link( $collection->term, 'collection' ); ?>"><?php $collection->the_name(); ?></a></div>
				</div>
				<div class="headbox-subtitle">
					<div class="term-count collection-count"><?php printf( _n( '%d Movie', '%d Movies', $collection->term->count, 'wpmovielibrary' ), $collection->term->count ); ?></div>
				</div>
			</div>
			<div class="headbox-metadata">
				<div class="headbox-description">
					<div class="term-description collection-description"><?php $collection->the_description(); ?></div>
				</div>
			</div>
		</div>
	</div>
<?php else : ?>

	<div id="collection-headbox-{{ data.node.get( 'id' ) }}" class="wpmoly term-headbox collection-headbox theme-{{ data.settings.get( 'theme' ) }}">
		<div class="headbox-header">
			<div class="headbox-thumbnail">
				<img src="{{ data.node.get( 'thumbnail' ) }}" alt="" />
			</div>
		</div>
		<div class="headbox-content clearfix">
			<div class="headbox-titles">
				<div class="headbox-title">
					<div class="term-title collection-title"><a href="{{ data.node.get( 'link' ) }}">{{ data.node.get( 'name' ) }}</a></div>
				</div>
				<div class="headbox-subtitle">
					<div class="term-count collection-count">{{ wpmoly.l10n._n( wpmolyL10n.nMoviesFound, data.node.get( 'count' ) ) }}</div>
				</div>
			</div>
			<div class="headbox-metadata">
				<div class="headbox-description">
					<div class="term-description collection-description">{{ data.node.get( 'description' ) }}</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>