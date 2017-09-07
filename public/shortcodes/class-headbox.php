<?php
/**
 * Define the Headbox Shortcode class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
 */

namespace wpmoly\Shortcodes;

use wpmoly\Templates\Front as Template;

/**
 * General Shortcode class.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/public/shortcodes
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Headbox extends Shortcode {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 *
	 * @var    string
	 */
	public static $name = 'headbox';

	/**
	 * Headbox Node.
	 *
	 * @var    Node
	 */
	private $node;

	/**
	 * Shortcode attributes sanitizers
	 *
	 * @var    array
	 */
	protected $validates = array(
		'id' => array(
			'default' => '',
			'values'  => null,
			'filter'  => 'intval',
		),
		'title' => array(
			'default' => '',
			'values'  => null,
			'filter'  => 'esc_attr',
		),
		'type' => array(
			'default' => 'movie',
			'values'  => null,
			'filter'  => 'esc_attr',
		),
		'theme' => array(
			'default' => 'default',
			'values'  => null,
			'filter'  => 'esc_attr',
		),
	);

	/**
	 * Shortcode aliases
	 *
	 * @var    array
	 */
	protected static $aliases = array(
		'movie'              => 'movie',
		'movie_headbox'      => 'movie',
		'actor'              => 'actor',
		'actor_headbox'      => 'actor',
		'collection'         => 'collection',
		'collection_headbox' => 'collection',
		'genre'              => 'genre',
		'genre_headbox'      => 'genre',
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
			$this->set( 'type', self::$aliases[ $this->tag ] );
		}

		if ( ! in_array( $this->attributes['type'], array( 'movie', 'actor', 'collection', 'genre' ) ) ) {
			return false;
		}

		$this->get_node();
		if ( ! $this->node ) {
			return false;
		}

		$this->headbox = get_headbox( $this->node );
		if ( ! $this->headbox ) {
			return false;
		}

		$this->headbox->set( $this->attributes );

		// Set Template
		$this->template = get_headbox_template( $this->headbox );
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

		if ( ! $this->node ) {
			$this->template = wpmoly_get_template( 'notice.php' );
			$this->template->set_data( array(
				'type'    => 'info',
				'icon'    => 'wpmolicon icon-info',
				'message' => sprintf( __( 'It seems this Node does not have any metadata available yet; %s?', 'wpmovielibrary' ), sprintf( '<a href="%s">%s</a>', get_edit_post_link(), __( 'care to add some', 'wpmovielibrary' ) ) ),
				'note'    => __( 'This notice is private; only you and other administrators can see it.', 'wpmovielibrary' ),
			) );
			return $this;
		}

		$type = $this->attributes['type'];
		$this->template->set_data( array(
			'headbox' => $this->headbox,
			"$type"   => $this->node,
		) );

		return $this;
	}

	/**
	 * Retriveve the Headbox Movie.
	 *
	 * Try to find the node by its title/name if such an attribute was passed
	 * to the Shortcode.
	 *
	 * @since    3.0
	 *
	 * @return   Node|boolean
	 */
	private function get_node() {

		$function = 'get_' . $this->attributes['type'];
		if ( ! function_exists( $function ) ) {
			return false;
		}

		if ( empty( $this->attributes['title'] ) ) {
			$this->node = $function( $this->attributes['id'] );
			return $this->node;
		}

		if ( in_array( $this->attributes['type'], array( 'movie' ) ) ) {
			$object = get_page_by_title( $this->attributes['title'], OBJECT, $this->attributes['type'] );
			$object_id = $object->ID;
		} elseif ( in_array( $this->attributes['type'], array( 'movie', 'actor', 'collection', 'genre' ) ) ) {
			$object = get_term_by( 'name', $this->attributes['title'], $this->attributes['type'] );
			$object_id = $object->term_id;
		}

		if ( is_null( $object_id ) ) {
			return false;
		}

		$this->node = $function( $object_id );

		return $this->node;
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
