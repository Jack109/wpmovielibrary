<?php
/**
 * Define the Ajax class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/ajax
 */

namespace wpmoly\Ajax;

use WP_Error;

/**
 * Handle all the plugin's AJAX callbacks.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/ajax
 * @author     Charlie Merland <charlie@caercam.org>
 */
class Ajax {

	/**
	 * Admin Ajax callback methods.
	 *
	 * @var      array
	 */
	public $admin_callbacks;

	/**
	 * Public Ajax callback methods.
	 *
	 * @var      array
	 */
	public $public_callbacks;

	/**
	 * Current instance.
	 *
	 * @var      Library
	 */
	public static $instance;

	/**
	 * Hook list.
	 *
	 * @var    array
	 */
	public $hooks = array();

	/**
	 * Class constructor.
	 *
	 * @since    3.0
	 */
	public function __construct() {

		$admin_callbacks = array(
			'save_meta'              => array( 'wpmoly\Ajax\Meta', 'save_meta' ),
			'save_details'           => array( 'wpmoly\Ajax\Meta', 'save_details' ),
			'autosave_meta'          => array( 'wpmoly\Ajax\Meta', 'save_meta' ),
			'autosave_details'       => array( 'wpmoly\Ajax\Meta', 'save_details' ),
			'autosave_collections'   => array( 'wpmoly\Ajax\Meta', 'save_collections' ),
			'autosave_genres'        => array( 'wpmoly\Ajax\Meta', 'save_genres' ),
			'autosave_actors'        => array( 'wpmoly\Ajax\Meta', 'save_actors' ),
			'autosave_grid_setting'  => array( 'wpmoly\Ajax\Meta', 'save_grid_setting' ),

			'api_search_movie'       => array( 'wpmoly\Ajax\API', 'search_movie' ),
			'api_fetch_movie'        => array( 'wpmoly\Ajax\API', 'fetch_movie' ),
			'api_fetch_backdrops'    => array( 'wpmoly\Ajax\API', 'fetch_backdrops' ),
			'api_fetch_posters'      => array( 'wpmoly\Ajax\API', 'fetch_posters' ),
			'api_fetch_images'       => array( 'wpmoly\Ajax\API', 'fetch_images' ),

			'remove_backdrop'        => '',
			'remove_poster'          => '',
			'set_backdrops'          => '',
			'set_posters'            => '',

			'query_backdrops'        => '',
			'query_posters'          => '',
			'save_settings'          => '',
		);
		$public_callbacks = array();

		/**
		 * Filter the list of allowed admin callbacks.
		 *
		 * This should only be used to remove existing callbacks. Adding
		 * new ones won't change anything anyway.
		 *
		 * @since    3.0
		 *
		 * @param    array    $admin_callbacks
		 */
		$this->admin_callbacks = apply_filters( 'wpmoly/filter/ajax/admin_callbacks', $admin_callbacks );

		/**
		 * Filter the list of allowed admin callbacks.
		 *
		 * This should only be used to remove existing callbacks. Adding
		 * new ones won't change anything anyway.
		 *
		 * @since    3.0
		 *
		 * @param    array    $admin_callbacks
		 */
		$this->public_callbacks = apply_filters( 'wpmoly/filter/ajax/public_callbacks', $public_callbacks );

		$this->hooks['actions'] = array();
		$this->hooks['filters'] = array();

		if ( is_admin() ) {
			$this->define_admin_hooks();
		} else {
			$this->define_public_hooks();
		}
	}

	/**
	 * Singleton.
	 *
	 * @since    3.0
	 *
	 * @return   Ajax
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new static;
		}

		return self::$instance;
	}

	/**
	 * Handle callbacks function for Ajax requests.
	 *
	 * Callbacks can be part of other classes in the Ajax namespace.
	 *
	 * @since    3.0
	 *
	 * @param    string    $method Method name.
	 * @param    array     $arguments Method arguments.
	 *
	 * @return   mixed
	 */
	public function __call( $method, $arguments ) {

		$method = str_replace( '_callback', '', $method );
		if ( ! isset( $method, $this->admin_callbacks ) && ! isset( $method, $this->public_callbacks ) ) {
			wp_send_json_error();
		}

		// Admin or Public
		if ( ! empty( $this->admin_callbacks[ $method ] ) ) {
			list( $class, $callback ) = $this->admin_callbacks[ $method ];
		} elseif ( ! empty( $this->public_callbacks[ $method ] ) ) {
			list( $class, $callback ) = $this->public_callbacks[ $method ];
		}

		// This or another class
		if ( empty( $callback ) && method_exists( $this, $method ) ) {
			return $this->$method();
		} elseif ( ! empty( $callback ) && method_exists( $class, $callback ) ) {
			$instance = new $class;
			return $instance->$callback();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Register Ajax admin hooks.
	 *
	 * @since    3.0
	 */
	public function define_admin_hooks() {

		foreach ( array_keys( $this->admin_callbacks ) as $callback ) {
			$this->hooks['actions'][] = array( "wp_ajax_wpmoly_$callback", $this, "{$callback}_callback", null, null );
		}
	}

	/**
	 * Register Ajax public hooks.
	 *
	 * @since    3.0
	 */
	public function define_public_hooks() {

		foreach ( array_keys( $this->public_callbacks ) as $callback ) {
			$this->hooks['actions'][] = array( "wp_ajax_wpmoly_$callback",        $this, "{$callback}_callback", null, null );
			$this->hooks['actions'][] = array( "wp_ajax_nopriv_wpmoly_$callback", $this, "{$callback}_callback", null, null );
		}
	}

	/**
	 * Query Backdrops for the current post.
	 *
	 * @since    3.0
	 */
	private function query_backdrops() {

		$this->query_images( 'backdrops' );
	}

	/**
	 * Query Posters for the current post.
	 *
	 * @since    3.0
	 */
	private function query_posters() {

		$this->query_images( 'posters' );
	}

	/**
	 * Query Images for the current post.
	 *
	 * @since    3.0
	 *
	 * @param    string    $type Images type, 'backdrops' or 'posters'
	 */
	private function query_images( $type ) {

		$post_id = ! empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : false;
		if ( ! $post_id || ! get_post( $post_id ) ) {
			wp_send_json_error( __( 'Invalid Post ID.', 'wpmovielibrary' ) );
		}

		if ( ! in_array( $type, array( 'backdrops', 'posters' ) ) ) {
			wp_send_json_error( __( 'Invalid Image type.', 'wpmovielibrary' ) );
		}

		$movie = get_movie( $post_id );
		if ( 'posters' == $type ) {
			$media = $movie->get_posters( true );
		} elseif ( 'backdrops' == $type ) {
			$media = $movie->get_backdrops( true );
		}

		if ( $media->has_items() ) {
			while ( $media->has_items() ) {
				$image = $media->the_item();
				$attachment = wp_prepare_attachment_for_js( $image->id );
				$image = array_merge( $image->data, $attachment );
				$media->add( $image, $media->key() );
			}
		}

		wp_send_json_success( $media );
	}

	/**
	 * Remove a backdrop from the backdrops list.
	 *
	 * @since    3.0
	 */
	private function remove_backdrop() {

		$this->remove_image( 'backdrop' );
	}

	/**
	 * Remove a poster from the posters list.
	 *
	 * @since    3.0
	 */
	private function remove_poster() {

		$this->remove_image( 'poster' );
	}

	/**
	 * Remove an image from the backdrops/posters list.
	 *
	 * @since    3.0
	 *
	 * @param    string    $type Image type, 'backdrop' or 'poster'
	 */
	private function remove_image( $type ) {

		$post_id = ! empty( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : false;
		if ( ! $post_id || ! get_post( $post_id ) ) {
			wp_send_json_error( __( 'Invalid Post ID.', 'wpmovielibrary' ) );
		}

		$tmdb_id = ! empty( $_POST['tmdb_id'] ) ? intval( $_POST['tmdb_id'] ) : false;
		if ( ! $tmdb_id ) {
			wp_send_json_error( __( 'Invalid TMDb ID.', 'wpmovielibrary' ) );
		}

		if ( ! in_array( $type, array( 'backdrop', 'poster' ) ) ) {
			wp_send_json_error( __( 'Invalid Image type.', 'wpmovielibrary' ) );
		}

		// Retro compatibility: prior to version 3.0 backdrops were
		// referred to as 'images'
		if ( 'backdrop' == $type ) {
			$type = 'image';
		}

		if ( delete_post_meta( $post_id, "_wpmoly_{$type}_related_tmdb_id", $tmdb_id ) ) {
			wp_send_json_success();
		}

		wp_send_json_error( sprintf( __( 'An unknown error occurred while trying to remove image #%1$d from the %2$s list.', 'wpmovielibrary' ), $post_id, $type ) );
	}

	/**
	 * Set an existing image as backdrop.
	 *
	 * @since    3.0
	 */
	private function set_backdrops() {

		$this->set_images( 'backdrop' );
	}

	/**
	 * Set an existing image as poster.
	 *
	 * @since    3.0
	 */
	private function set_posters() {

		$this->set_images( 'poster' );
	}

	/**
	 * Set an existing image as backdrop or poster.
	 *
	 * @since    3.0
	 *
	 * @param    string    $type Image type, 'backdrop' or 'poster'.
	 */
	private function set_images( $type ) {

		$error = new WP_Error();

		$tmdb_id = ! empty( $_POST['tmdb_id'] ) ? intval( $_POST['tmdb_id'] ) : false;
		if ( ! $tmdb_id ) {
			wp_send_json_error( __( 'Invalid TMDb ID.', 'wpmovielibrary' ) );
		}

		if ( ! in_array( $type, array( 'backdrop', 'poster' ) ) ) {
			wp_send_json_error( __( 'Invalid Image type.', 'wpmovielibrary' ) );
		}

		// Retro compatibility: prior to version 3.0 backdrops were
		// referred to as 'images'
		if ( 'backdrop' == $type ) {
			$type = 'image';
		}

		$images = ! empty( $_POST['images'] ) ? (array) $_POST['images'] : false;
		foreach ( $images as $image ) {
			if ( ! $image || ! get_post( $image ) || 'attachment' != get_post_type( $image ) ) {
				$error->add( 'invalid_attachment_id', sprintf( __( 'Invalid Attachment ID %d.', 'wpmovielibrary' ), $image ) );
			} elseif ( ! add_post_meta( $image, "_wpmoly_{$type}_related_tmdb_id", $tmdb_id ) ) {
				$error->add( 'unknown_error', sprintf( __( 'An unknown error occurred while trying to set image #%1$d as %2$s.', 'wpmovielibrary' ), $post_id, $type ) );
			}
		}

		if ( ! empty( $error->errors ) ) {
			wp_send_json_error( $error );
		}

		wp_send_json_success();
	}

	/**
	 * Save search settings.
	 *
	 * @since    3.0
	 */
	private function save_settings() {

		global $wpmoly;

		$settings = ! empty( $_POST['settings'] ) ? $_POST['settings'] : false;
		if ( ! $settings ) {
			wp_send_json_error( new WP_Error( 'invalid_settings', __( 'Invalid Settings.', 'wpmovielibrary' ) ) );
		}

		$error = new WP_Error();
		foreach ( $settings as $key => $value ) {
			if ( ! $wpmoly->options->__isset( $key ) ) {
				$error->add( 'invalid_setting', sprintf( __( 'Invalid Setting: "%s" does not exist.', 'wpmovielibrary' ), esc_attr( $key ) ) );
			} else {
				$wpmoly->options->set( $key, $value );
			}
		}

		if ( ! empty( $error->errors ) ) {
			wp_send_json_error( $error );
		}

		wp_send_json_success();
	}

}
