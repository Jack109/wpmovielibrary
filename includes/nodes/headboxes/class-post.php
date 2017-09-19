<?php
/**
 * Define the Post Headbox class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly\nodes\headboxes;

use \wpmoly\nodes\Node;
use \wpmoly\nodes\posts;

/**
 * General Post Headbox class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Post extends Headbox {

	/**
	 * Class Constructor.
	 *
	 * @since    3.0
	 *
	 * @param    int|Node|WP_Term    $node Node ID, node instance or post object
	 */
	public function __construct( $node = null ) {

		if ( is_numeric( $node ) ) {
			$this->id   = absint( $node );
			$this->post = get_post( $this->id );
		} elseif ( $node instanceof Node ) {
			if ( $node instanceof posts\Movie ) {
				$this->id    = absint( $node->id );
				$this->movie = $node;
				$this->type  = 'movie';
			} else {
				$this->id   = absint( $node->id );
				$this->post = $node->post;
			}
		} elseif ( isset( $node->term_id ) ) {
			$this->id   = absint( $node->term_id );
			$this->post = $node;
		}

		$this->init();
	}

	/**
	 * Initialize the Headbox.
	 *
	 * @since    3.0
	 */
	public function init() {

		$headbox_types = array(
			'movie' => array(
				'label'  => __( 'Movie', 'wpmovielibrary' ),
				'themes' => array(
					'default'    => __( 'Default', 'wpmovielibrary' ),
					'extended'   => __( 'Extended', 'wpmovielibrary' ),
					'vintage'    => __( 'Vintage', 'wpmovielibrary' ),
					'allocine'   => __( 'Allocine', 'wpmovielibrary' ),
					'allocine-2' => __( 'Allocine v2', 'wpmovielibrary' ),
					'imdb-2'     => __( 'IMDb v2', 'wpmovielibrary' ),
				),
			),
		);

		/**
		 * Filter the supported Headbox types.
		 *
		 * @since    3.0
		 *
		 * @param    array    $headbox_types
		 */
		$this->supported_types = apply_filters( 'wpmoly/filter/postheadbox/supported/types', $headbox_types );

		foreach ( $this->supported_types as $type_id => $type ) {

			/**
			 * Filter the supported Headbox themes.
			 *
			 * @since    3.0
			 *
			 * @param    array    $default_modes
			 */
			$this->supported_themes[ $type_id ] = apply_filters( 'wpmoly/filter/postheadbox/supported/' . $type_id . '/themes', $type['themes'] );
		}

		$this->build();
	}

	/**
	 * Build the Headbox.
	 *
	 * Load items depending on presets or custom settings.
	 *
	 * @since    3.0
	 */
	public function build() {

		if ( is_null( $this->get( 'type' ) ) ) {
			return false;
		}

		$function = 'get_' . $this->get( 'type' );
		if ( function_exists( $function ) ) {
			$this->node = $function( $this->id );
		}
	}

	/**
	 * Retrieve current postheadbox type.
	 *
	 * @since    3.0
	 *
	 * @return   string
	 */
	public function get_type() {

		/**
		 * Filter postheadbox default type.
		 *
		 * @since    3.0
		 *
		 * @param    string    $default_type
		 */
		$default_type = apply_filters( 'wpmoly/filter/postheadbox/default/type', '' );

		if ( is_null( $this->type ) ) {
			$this->type = $default_type;
		}

		return $this->type;
	}

	/**
	 * Set postheadbox type.
	 *
	 * @since    3.0
	 *
	 * @param    string    $type
	 *
	 * @return   string
	 */
	public function set_type( $type ) {

		if ( ! isset( $this->supported_types[ $type ] ) ) {
			$type = '';
		}

		$this->type = $type;

		return $this->type;
	}

	/**
	 * Retrieve current postheadbox theme.
	 *
	 * @since    3.0
	 *
	 * @return   string
	 */
	public function get_theme() {

		if ( is_null( $this->theme ) ) {
			$this->theme = 'default';
		}

		return $this->theme;
	}

	/**
	 * Set postheadbox theme.
	 *
	 * @since    3.0
	 *
	 * @param    string    $theme
	 *
	 * @return   string
	 */
	public function set_theme( $theme ) {

		if ( ! isset( $this->supported_themes[ $this->type ][ $theme ] ) ) {
			$theme = 'default';
		}

		$this->theme = $theme;

		return $this->theme;
	}

}
