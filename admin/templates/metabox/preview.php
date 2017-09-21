<?php
/**
 * Metabox preview panel Template
 *
 * @since 3.0.0
 */
?>

		<div id="wpmoly-movie-preview" class="wpmoly-movie-preview<?php echo $empty ? ' hidden' : ''; ?>">

			<div class="wpmoly-movie-preview-bg-container">
				<div class="wpmoly-movie-preview-background" style="background-image:url(<?php $background->render(); ?>)"></div>
			</div>

			<div class="wpmoly-movie-preview-content clearfix">
				<div class="wpmoly-movie-preview-poster">
					<?php $poster->render( 'medium', 'html' ); ?>
					<button type="button" data-action="open-editor" class="button button-primary hide-if-no-js"><?php _e( 'Open Editor', 'wpmovielibrary' ); ?></button>
					<button type="button" data-action="close-editor" class="button button-secondary hide-if-no-js hidden"><?php _e( 'Close Editor', 'wpmovielibrary' ); ?></button>
				</div>

				<div class="wpmoly-movie-preview-meta">
					<div class="wpmoly-movie-preview-hgroup">
						<h2 class="wpmoly-movie-preview-title"><?php echo $movie->the( 'title' ); ?><span class="wpmoly-movie-preview-original-title">(<?php $movie->the( 'original_title' ); ?>)</span></h2>
						<h5 class="wpmoly-movie-preview-tagline"><?php $movie->the( 'tagline' ); ?></h5>
					</div>
					<div class="wpmoly-movie-preview-intro">
						<span><?php echo substr( $movie->get( 'release_date' ), 0, 4 ); ?></span>&nbsp;|&nbsp;
						<span><?php $movie->the( 'runtime' ); ?> min</span>&nbsp;|&nbsp;
						<span><?php $movie->the( 'genres' ); ?></span>&nbsp;|&nbsp;
						<span><?php $movie->the( 'certification' ); ?></span>
					</div>
					<div class="wpmoly-movie-preview-overview"><?php $movie->the( 'overview' ); ?></div>
				</div>
			</div>

		</div>
