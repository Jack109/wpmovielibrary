<?php
/**
 * Define the grid class.
 *
 * @link       http://wpmovielibrary.com
 * @since      3.0
 *
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 */

namespace wpmoly\Node;

/**
 * Handle grids.
 *
 * @since      3.0
 * @package    WPMovieLibrary
 * @subpackage WPMovieLibrary/includes/node
 * @author     Charlie Merland <charlie@caercam.org>
 * 
 * @property    int       $id Grid ID.
 * @property    string    $type Grid type: movie, actor, genre…
 * @property    string    $mode Grid mode: grid, list or archive
 * @property    string    $preset Grid content preset.
 * @property    string    $order_by Grid content order by.
 * @property    string    $order Grid content order.
 * @property    int       $columns Number of columns to use.
 * @property    int       $rows Number of rows to use.
 * @property    int       $total Number of Nodes to use.
 * @property    int       $show_menu Show the Grid menu to users.
 * @property    int       $mode_control Allow users to control the Grid mode.
 * @property    int       $content_control Allow users to control the Grid content.
 * @property    int       $display_control Allow users to control the Grid display.
 * @property    int       $order_control Allow users to control the Grid content ordering.
 * @property    int       $show_pagination Show the Grid pagination to users.
 */
class Grid extends Node {

	/**
	 * Node Collection.
	 * 
	 * @var    Collection
	 */
	public $items;

	/**
	 * Supported Grid types.
	 * 
	 * @var    array
	 */
	private $supported_types = array();

	/**
	 * Supported Grid modes.
	 * 
	 * @var    array
	 */
	private $supported_modes = array();

	/**
	 * Supported Grid themes.
	 * 
	 * @var    array
	 */
	private $supported_themes = array();

	/**
	 * Initialize the Grid.
	 * 
	 * @since    3.0
	 * 
	 * @return   void
	 */
	public function init() {

		$this->suffix = '_wpmoly_' . $this->type . '_grid_';
		$this->items = new Collection;

		/**
		 * Filter the supported Grid types.
		 * 
		 * @since    3.0
		 * 
		 * @param    array    $supported_types
		 */
		$this->supported_types = apply_filters( 'wpmoly/filter/grid/supported/types', array( 'movie', 'actor', 'genre' ) );

		foreach ( $this->supported_types as $type ) {

			/**
			 * Filter the supported Grid modes.
			 * 
			 * @since    3.0
			 * 
			 * @param    array    $supported_modes
			 */
			$this->supported_modes[ $type ] = apply_filters( 'wpmoly/filter/grid/supported/' . $type . '/modes', array( 'grid', 'list', 'archive' ) );

			/**
			 * Filter the supported Grid themes.
			 * 
			 * @since    3.0
			 * 
			 * @param    array    $supported_themes
			 */
			$this->supported_themes[ $type ] = apply_filters( 'wpmoly/filter/grid/supported/' . $type . '/themes', array( 'default' ) );
		}

		$this->build();
	}

	/**
	 * Build the Grid.
	 * 
	 * Load items depending on presets or custom settings.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function build() {

		if ( 'custom' != $this->preset ) {
			$query = $this->get_query_callback();
			if ( is_callable( $query ) ) {
				$items = call_user_func( $query );
				foreach ( (array) $items as $item ) {
					$this->items->add( $item );
				}
				return $this->items;
			}
		}

		return $this->build_query();
	}

	/**
	 * Determine the callback to use based on the Grid type.
	 * 
	 * This should return a valid array( $class, $method ) callback.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function get_query_callback() {

		$classes = array(
			'movie' => '\wpmoly\Query\Movies',
			'actor' => '\wpmoly\Query\Actors',
			'genre' => '\wpmoly\Query\Genres'
		);

		if ( isset( $classes[ $this->type ] ) ) {
			$class = $classes[ $this->type ];
			$method = str_replace( '-', '_', $this->preset );
			if ( method_exists( $class, $method ) ) {
				return array( $class, $method );
			}
		}

		return array();
	}

	/**
	 * Perform a custom query.
	 * 
	 * @since    3.0
	 * 
	 * @return   array
	 */
	private function build_query() {

		return array();
	}

	/**
	 * Return a valid number of rows.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $rows Number of rows.
	 * 
	 * @return   int
	 */
	public function validate_rows( $rows ) {

		/**
		 * Filter the minimum number of rows.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $min Default minimum number of rows.
		 * @param    Grid    $grid Grid instance.
		 */
		$min = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/rows/min', 1, $this );

		/**
		 * Filter the maximum number of rows.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $max Default maximum number of rows.
		 * @param    Grid    $grid Grid instance.
		 */
		$max = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/rows/max', 10, $this );

		/**
		 * Filter the default number of rows.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $default Default number of rows.
		 * @param    Grid    $grid Grid instance.
		 */
		$default = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/rows/default', 4, $this );

		return ! empty( $rows ) ? max( $min, min( $rows, $max ) ) : $default;
	}

	/**
	 * Return a valid number of columns.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $rows Number of columns.
	 * 
	 * @return   int
	 */
	public function validate_columns( $columns ) {

		/**
		 * Filter the minimum number of columns.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $min Default minimum number of columns.
		 * @param    Grid    $grid Grid instance.
		 */
		$min = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/columns/min', 1, $this );

		/**
		 * Filter the maximum number of columns.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $max Default maximum number of columns.
		 * @param    Grid    $grid Grid instance.
		 */
		$max = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/columns/max', 12, $this );

		/**
		 * Filter the default number of columns.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $default Default number of columns.
		 * @param    Grid    $grid Grid instance.
		 */
		$default = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/columns/default', 5, $this );

		return ! empty( $columns ) ? max( $min, min( $columns, $max ) ) : $default;
	}

	/**
	 * Return a valid ideal column width.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $column_width Ideal column width.
	 * 
	 * @return   int
	 */
	public function validate_column_width( $column_width ) {

		/**
		 * Filter the default ideal column width.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $ideal_width Default ideal column width.
		 * @param    Grid    $grid Grid instance.
		 */
		$ideal_width = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/columns/ideal_width', 160, $this );

		return ! empty( $column_width ) ? intval( $column_width ) : $ideal_width;
	}

	/**
	 * Return a valid ideal row height.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    int    $row_width Ideal row height.
	 * 
	 * @return   int
	 */
	public function validate_row_height( $row_height ) {

		/**
		 * Filter the default ideal row height.
		 * 
		 * @since    3.0
		 * 
		 * @param    int     $ideal_width Default ideal row height.
		 * @param    Grid    $grid Grid instance.
		 */
		$ideal_height = apply_filters( 'wpmoly/filter/grid/' . $this->type . '/rows/ideal_height', 240, $this );

		return ! empty( $row_height ) ? intval( $row_height ) : $ideal_height;
	}

	/**
	 * Make sure a Grid preset is supported.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $preset Grid preset to validate.
	 * 
	 * @return   string
	 */
	public function validate_preset( $preset ) {

		if ( empty( $preset ) ) {
			$preset = 'default_preset';
		}

		return $preset;
	}

	/**
	 * Make sure a Grid theme is supported.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $theme Grid theme to validate.
	 * 
	 * @return   string
	 */
	public function validate_theme( $theme ) {

		return in_array( $theme, $this->supported_themes[ $this->type ] ) ? $theme : 'default';
	}

	/**
	 * Make sure a Grid mode is supported.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $mode Grid mode to validate.
	 * 
	 * @return   string
	 */
	public function validate_mode( $mode ) {

		return in_array( $mode, $this->supported_modes[ $this->type ] ) ? $mode : 'grid';
	}

	/**
	 * Make sure a Grid type is supported.
	 * 
	 * Used by Node::__validate().
	 * 
	 * @since    3.0
	 * 
	 * @param    string    $type Grid type to validate.
	 * 
	 * @return   string
	 */
	public function validate_type( $type ) {

		return in_array( $type, $this->supported_types ) ? $type : 'movie';
	}
}