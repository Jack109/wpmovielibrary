<?php
/**
 * Define the Taxonomy class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly\nodes\taxonomies;

use wpmoly\nodes\Node;

/**
 * Define a generic Taxonomy class.
 *
 * @since 3.0.0
 * @package WPMovieLibrary
 * @author Charlie Merland <charlie@caercam.org>
 */
abstract class Taxonomy extends Node {

	/**
	 * Term object.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var WP_Term
	 */
	public $term;

	/**
	 * Taxonomy name.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $taxonomy = '';

	/**
	 * Taxonomy thumbnail.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var nodes\Images
	 */
	protected $thumbnail;

	/**
	 * Class Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param int|Taxonomy|WP_Term $term Term ID, term object or node instance.
	 */
	public function __construct( $term = null ) {

		if ( is_numeric( $term ) ) {
			$this->id   = absint( $term );
			$this->term = get_term( $this->id );
		} elseif ( $term instanceof Taxonomy ) {
			$this->id   = absint( $term->id );
			$this->term = $term->term;
		} elseif ( isset( $term->term_id ) ) {
			$this->id   = absint( $term->term_id );
			$this->term = $term;
		}

		$this->init();
	}

	/**
	 * Initialize the class.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function init() {

		/** This filter is documented in includes/core/class-registrar.php */
		$this->suffix = apply_filters( "wpmoly/filter/{$this->taxonomy}/meta/key", '' );

		/**
		 * Filter the default taxonomy meta list.
		 *
		 * @since 3.0.0
		 *
		 * @param array $default_meta
		 */
		$this->default_meta = apply_filters( "wpmoly/filter/default/{$this->taxonomy}/meta", array( 'name', 'thumbnail', 'person_id' ) );
	}

	/**
	 * Magic.
	 *
	 * Add support for Taxonomy::get_{$property}() and Taxonomy::the_{$property}()
	 * methods.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $method Method name.
	 * @param array $arguments Method arguments.
	 *
	 * @return mixed
	 */
	public function __call( $method, $arguments ) {

		if ( preg_match( '/get_[a-z_]+/i', $method ) ) {
			$name = str_replace( 'get_', '', $method );
			return $this->get_the( $name );
		} elseif ( preg_match( '//i', $method ) ) {
			$name = str_replace( 'the_', '', $method );
			$this->the( $name );
		}
	}

	/**
	 * Load metadata.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @param string $name Property name
	 *
	 * @return mixed
	 */
	protected function get_property( $name ) {

		// Load metadata
		$value = get_term_meta( $this->id, $this->suffix . $name, true );

		return $value;
	}

	/**
	 * Property accessor.
	 *
	 * Override Taxonomy::get() to add support for additional data like 'name'.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Property name
	 * @param mixed $default Default value
	 *
	 * @return mixed
	 */
	public function get( $name, $default = null ) {

		if ( 'name' == $name ) {
			return $this->term->name;
		}

		if ( 'description' == $name ) {
			return $this->term->description;
		}

		return parent::get( $name, $default );
	}

	/**
	 * Enhanced property accessor. Unlike Taxonomy::get() this method automatically
	 * escapes the property requested and therefore should be used when the
	 * property is meant for display.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Property name
	 *
	 * @return mixed
	 */
	public function get_the( $name ) {

		/**
		 * Filter properties for display.
		 *
		 * @since 3.0.0
		 *
		 * @param string $name Meta name.
		 * @param mixed $value Meta value.
		 * @param Taxonomy $taxonomy Taxonomy object.
		 */
		return apply_filters( "wpmoly/filter/the/{$this->taxonomy}/" . sanitize_key( $name ), $this->get( $name ), $this );
	}

	/**
	 * Simple property echoer. Use Taxonomy::get_the() to automatically escape
	 * the requested property.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $name Property name
	 */
	public function the( $name ) {

		echo $this->get_the( $name );
	}

	/**
	 * Simple accessor for Taxonomy thumbnail.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $variant Image variant.
	 *
	 * @return Image
	 */
	abstract public function get_thumbnail( $variant = '', $size = 'thumb' );

	/**
	 * Retrieve the Taxonomy custom thumbnail, if any.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $size Image size.
	 *
	 * @return string
	 */
	abstract public function get_custom_thumbnail( $size = 'thumb' );

}
