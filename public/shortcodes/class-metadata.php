<?php
/**
 * Define the Metadata Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 */

namespace wpmoly\shortcodes;

use wpmoly\templates\Front as Template;

/**
 * General Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * 
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Metadata extends Shortcode {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 *
	 * @var    string
	 */
	public static $name = 'movie_meta';

	/**
	 * Shortcode attributes sanitizers
	 *
	 * @var    array
	 */
	protected $validates = array(
		'id' => array(
			'default' => false,
			'values'  => null,
			'filter'  => 'intval',
		),
		'title' => array(
			'default' => null,
			'values'  => null,
			'filter'  => 'esc_attr',
		),
		'label' => array(
			'default' => true,
			'values'  => null,
			'filter'  => 'boolval',
		),
		'key' => array(
			'default' => false,
			'values'  => null,
			'filter'  => 'esc_attr',
		),
		'format' => array(
			'default' => 'display',
			'values'  => null,
			'filter'  => 'esc_attr',
		),
		'count' => array(
			'default' => -1,
			'values'  => null,
			'filter'  => 'intval',
		),
	);

	/**
	 * Shortcode aliases
	 *
	 * @var    array
	 */
	protected static $aliases = array(
		'movie_director'       => 'director',
		'movie_overview'       => 'overview',
		'movie_title'          => 'title',
		'movie_original_title' => 'original_title',
		'movie_genres'         => 'genres',
		'movie_actors'         => 'cast',
		'movie_production'     => 'production_companies',
		'movie_producer'       => 'producer',
		'movie_photography'    => 'photography',
		'movie_composer'       => 'composer',
		'movie_author'         => 'author',
		'movie_writer'         => 'writer',
		'movie_tagline'        => 'tagline',
		'movie_certification'  => 'certification',
		'movie_budget'         => 'budget',
		'movie_revenue'        => 'revenue',
		'movie_imdb_id'        => 'imdb_id',
		'movie_tmdb_id'        => 'tmdb_id',
		'movie_adult'          => 'adult',
		'movie_homepage'       => 'homepage',
	);

	/**
	 * Build the Shortcode.
	 *
	 * Prepare Shortcode parameters.
	 *
	 * @since    3.0
	 */
	protected function make() {

		if ( ! is_null( $this->tag ) && isset( self::$aliases[ $this->tag ] ) ) {
			$this->set( 'key', self::$aliases[ $this->tag ] );
		}

		// Set Template
		if ( false !== $this->attributes['label'] ) {
			$template = 'shortcodes/metadata-label.php';
		} else {
			$template = 'shortcodes/metadata.php';
		}

		$this->template = new Template( $template );
	}

	/**
	 * Get Movie ID from title if needed.
	 *
	 * @since    3.0
	 *
	 * @return   int
	 */
	protected function get_movie_id() {

		global $wpdb;

		if ( is_null( $this->attributes['title'] ) ) {
			return $this->attributes['id'];
		}

		$like = $wpdb->esc_like( $this->attributes['title'] );
		$like = '%' . $like . '%';

		$post_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE %s",
				$like
			)
		);

		$this->attributes['id'] = $post_id;

		return $post_id;
	}

	/**
	 * Get the metadata value.
	 *
	 * @since    3.0
	 *
	 * @return   mixed
	 */
	protected function get_meta_value() {

		$key    = $this->attributes['key'];
		$format = isset( $this->attributes['format'] ) ? $this->attributes['format'] : null;

		// Get Movie ID
		$post_id = $this->get_movie_id();

		// Get value
		$value = get_movie_meta( $post_id, $key, true );
		if ( empty( $value ) ) {
			/**
			 * Filter empty meta value.
			 *
			 * @since    3.0
			 *
			 * @param    string    $value
			 * @param    string    $format
			 */
			$value = apply_filters( "wpmoly/shortcode/format/{$key}/empty/value", $value, $format );
			if ( empty( $value ) ) {
				/** This filter is documented in includes/helpers/formatting.php **/
				$value = apply_filters( 'wpmoly/filter/meta/empty/value', '&mdash;' );
			}

			return $value;
		}

		// Raw value requested
		if ( 'raw' == $format ) {
			/**
			 * Filter raw meta value.
			 *
			 * @since    3.0
			 *
			 * @param    string    $value
			 */
			return apply_filters( "wpmoly/shortcode/format/{$key}/raw/value", $value );
		}

		// Deal with lists
		if ( ! empty( $this->attributes['count'] ) && 0 < $this->attributes['count'] ) {

			$old_value = $value;

			$value = explode( ',', $value );
			$value = array_map( 'trim', $value );
			$value = array_slice( $value, 0, $this->attributes['count'] );

			/**
			 * Filter array-shaped meta value.
			 *
			 * @since    3.0
			 *
			 * @param    array    $value
			 * @param    array    $options
			 */
			return apply_filters( "wpmoly/shortcode/format/{$key}/value", $value, compact( 'format' ) );
		}

		/**
		 * Filter meta value.
		 *
		 * @since    3.0
		 *
		 * @param    string    $value
		 * @param    array     $options
		 */
		return apply_filters( "wpmoly/shortcode/format/{$key}/value", $value, compact( 'format' ) );
	}

	/**
	 * Run the Shortcode.
	 *
	 * Perform all needed Shortcode stuff.
	 *
	 * @since    3.0
	 *
	 * @return   Shortcode
	 */
	public function run() {

		// Get value
		$meta = $this->get_meta_value();
		$key  = $this->attributes['key'];

		// Get label
		$label = wpmoly_o( 'default_meta' );
		$label = isset( $label[ $key ]['title'] ) ? $label[ $key ]['title'] : '';

		// Set template data
		$this->template->set_data( array(
			'meta'  => $meta,
			'label' => $label,
			'key'   => $key,
		) );

		return $this;
	}

	/**
	 * Initialize the Shortcode.
	 *
	 * Run things before doing anything.
	 *
	 * @since    3.0
	 */
	protected function init() {}
}
