<?php
/**
 * The file that defines the library class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly;

/**
 * The library class.
 *
 * Define public features: archive pages content and titles, public styles and
 * scripts, admin bar menu...
 *
 * @since 3.0.0
 * @package WPMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Library {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		add_filter( 'wpmoly/filter/archive/page/wp_title',   array( &$this, 'filter_archive_title' ), 10, 3 );
		add_filter( 'wpmoly/filter/archive/page/post_title', array( &$this, 'filter_archive_title' ), 10, 3 );

		add_filter( 'wpmoly/filter/taxonomy/archive/page/content', array( &$this, 'filter_taxonomy_archive_page_content' ), 10, 3 );
		add_filter( 'wpmoly/filter/movie/archive/page/content',    array( &$this, 'filter_movie_archive_page_content' ), 10, 2 );
	}

	/**
	 * Register the plugin's assets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function register_assets() {

		$assets = core\Assets::get_instance();
		add_action( 'wp_enqueue_scripts',      array( $assets, 'enqueue_public_styles' ), 95 );
		add_action( 'wp_enqueue_scripts',      array( $assets, 'enqueue_public_scripts' ), 95 );
		add_action( 'wp_print_footer_scripts', array( $assets, 'enqueue_public_templates' ), 95 );
	}

	/**
	 * Add a submenu to the 'Edit Post' menu to edit the grid related to an
	 * archive page.
	 *
	 * The admin bar is used on both front side and dashboard, we need to
	 * make this public.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public function admin_bar_menu( $wp_admin_bar ) {

		$post_id = get_the_ID();
		if ( ! $post_id || ! is_archive_page( $post_id ) ) {
			return false;
		}

		// Missing edit menu
		if ( ! $wp_admin_bar->get_node( 'edit' ) ) {
			return false;
		}

		// Retrieve related grid
		$grid_id = get_post_meta( $post_id, '_wpmoly_grid_id', true );
		if ( empty( $grid_id ) ) {
			return false;
		}

		// Add a new node
		$wp_admin_bar->add_node( array(
			'id'     => 'edit-grid',
			'title'  => __( 'Edit Grid', 'wpmovielibrary' ),
			'parent' => 'edit',
			'href'   => get_edit_post_link( $grid_id ),
		) );
	}

	/**
	 * Show the movie Headbox before post content.
	 *
	 * @TODO support custom integration of the Headbox inside post content.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $content The Post content.
	 */
	public function set_movie_post_content( $content = '' ) {

		if ( 'movie' != get_post_type() ) {
			return $content;
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return $content;
		}

		$movie = get_movie( $post_id );
		$headbox = get_headbox( $movie );

		if ( is_single() ) {
			$headbox->set_theme( 'extended' );
		} elseif ( is_archive() || is_search() ) {
			$headbox->set_theme( 'default' );
		}

		$template = get_movie_headbox_template( $headbox );

		return $template->render() . $content;
	}

	/**
	 * Adapt Archive Page post titles to match content.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string  $post_title Page original post title.
	 * @param WP_Post $post       Archive page Post instance.
	 *
	 * @return string
	 */
	public function set_archive_page_title( $post_title, $post ) {

		if ( is_admin() || ! is_archive_page( $post->ID ) ) {
			return $post_title;
		}

		$adapt = get_post_meta( $post->ID, '_wpmoly_adapt_page_title', true );
		if ( ! _is_bool( $adapt ) ) {
			return $post_title;
		}

		/**
		 * Filter Archive Page titles to match content.
		 *
		 * @since 3.0.0
		 *
		 * @access public
		 *
		 * @param string $title   Page original title.
		 * @param int    $post_id Archive page Post ID.
		 * @param string $context Context.
		 */
		$new_title = apply_filters( 'wpmoly/filter/archive/page/wp_title', $post_title, $post->ID, 'wp_title' );

		return $new_title;
	}

	/**
	 * Adapt Archive Page titles to match content.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $post_title Page original post title.
	 * @param int    $post_id    Archive page Post ID.
	 *
	 * @return string
	 */
	public function set_archive_page_post_title( $post_title, $post_id ) {

		global $wp_query;

		if ( is_admin() || ! is_archive_page( $post_id ) || ! in_the_loop() ) {
			return $post_title;
		}

		$adapt = get_post_meta( $post_id, '_wpmoly_adapt_post_title', true );
		if ( ! _is_bool( $adapt ) ) {
			return $post_title;
		}

		/**
		 * Filter Archive Page titles to match content.
		 *
		 * @since 3.0.0
		 *
		 * @access public
		 *
		 * @param string $title   Page original post title.
		 * @param int    $post_id Archive page Post ID.
		 * @param string $context Context.
		 */
		$new_title = apply_filters( 'wpmoly/filter/archive/page/post_title', $post_title, $post_id, 'post_title' );

		return $new_title;
	}

	/**
	 * Adapt Archive Page titles to match content.
	 *
	 * Mostly used to feature the term name in the page and post title when
	 * showing a single term archives.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $title   Page original post title.
	 * @param int    $post_id Archive page Post ID.
	 * @param string $context Context, either 'wp_title' (page title) or 'post_title' (page post title).
	 *
	 * @return string
	 */
	public function filter_archive_title( $title, $post_id, $context ) {

		$type = get_archive_page_type( $post_id );
		$name = get_query_var( $type );
		if ( empty( $name ) ) {
			return $title;
		}

		$term = get_term_by( 'slug', $name, $type );
		if ( ! $term ) {
			return $title;
		}

		$title = sprintf( _x( '%1$s: %2$s', 'Archive page title', 'wpmovielibrary' ), $title, $term->name );

		return $title;
	}

	/**
	 * Filter post content to add grid to archive pages.
	 *
	 * Determine if we're dealing with a single item, ie. a term, or a real
	 * archive page.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $content Post content.
	 *
	 * @return string
	 */
	public function set_archive_page_content( $content ) {

		$post_id = get_the_ID();
		if ( is_admin() || ! is_archive_page( $post_id ) ) {
			return $content;
		}

		$type = get_archive_page_type( $post_id );
		if ( ! empty( get_query_var( $type ) ) ) {

			/**
			 * Filter taxonomy archive page content.
			 *
			 * @since 3.0.0
			 *
			 * @param string $content Current post content.
			 * @param int    $post_id Current Post ID.
			 * @param string $type    Archive page type.
			 */
			$content = apply_filters( 'wpmoly/filter/taxonomy/archive/page/content', $content, $post_id, $type );
		} else {

			/**
			 * Filter movie archive page content.
			 *
			 * @since 3.0.0
			 *
			 * @param string $content Current post content.
			 * @param int    $post_id Current Post ID.
			 */
			$content = apply_filters( 'wpmoly/filter/movie/archive/page/content', $content, $post_id );
		}

		return $content;
	}

	/**
	 * Handle single item content.
	 *
	 * Mostly used to show custom pages for taxonomy terms.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $content Post content.
	 * @param int    $post_id Current Post ID.
	 * @param string $type    Archive page type.
	 *
	 * @return string
	 */
	public function filter_taxonomy_archive_page_content( $content, $post_id, $type ) {

		$show = get_post_meta( $post_id, '_wpmoly_single_terms', true );
		if ( ! _is_bool( $show ) ) {
			return $content;
		}

		$pre_content = '';

		$name = get_query_var( $type );
		$term = get_term_by( 'slug', $name, $type );
		if ( $term ) {

			$name = $term->name;

			$theme = get_post_meta( $post_id, '_wpmoly_headbox_theme', true );
			$headbox = get_term_headbox( $term );
			$headbox->set_theme( $theme );

			$headbox_template = get_headbox_template( $headbox );
			$pre_content = $headbox_template->render();
		}

		$archive_page_id = get_archives_page_id( 'movie' );
		if ( ! $archive_page_id ) {
			return $pre_content;
		}

		$grid_id = get_post_meta( $archive_page_id, '_wpmoly_grid_id', true );
		if ( empty( $grid_id ) ) {
			return $pre_content;
		}

		$grid = get_grid( (int) $grid_id );
		$grid->set_preset( array(
			$type => $name,
		) );

		$grid_template = get_grid_template( $grid );

		$pre_content .= $grid_template->render() . $content;

		return $pre_content;
	}

	/**
	 * Filter archive page content. Insert grid before or after regular post
	 * content depending on the archive page setting.
	 *
	 * @TODO support custom integration of the Headbox inside post content.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param string $content Post content.
	 * @param int    $post_id Current Post ID.
	 *
	 * @return string
	 */
	public function filter_movie_archive_page_content( $content, $post_id ) {

		$pre_content = '';

		$grid_id = get_post_meta( $post_id, '_wpmoly_grid_id', true );
		if ( empty( $grid_id ) ) {
			return $pre_content;
		}

		$grid = get_grid( (int) $grid_id );

		$preset = get_query_var( 'preset' );
		if ( ! empty( $preset ) ) {
			$preset = prefix_meta_key( $preset, '', true );
			$grid->set_preset( array(
				$preset => get_query_var( $preset ),
			) );
		}

		$grid_template = get_grid_template( $grid );

		$position = get_post_meta( $post_id, '_wpmoly_grid_position', true );
		if ( 'top' === $position ) {
			$pre_content = $grid_template->render() . $pre_content;
		} elseif ( 'bottom' === $position ) {
			$pre_content = $pre_content . $grid_template->render();
		}

		return $pre_content;
	}
}
