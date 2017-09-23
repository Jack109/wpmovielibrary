<?php
/**
 * The file that defines the core plugin class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

use wpmoly\core;
use wpmoly\rest;
use wpmoly\registrars;
use wpmoly\Dashboard;
use wpmoly\Library;

/**
 * The core plugin class.
 *
 * @since 3.0.0
 * @package WPMovieLibrary
 * 
 * @author Charlie Merland <charlie@caercam.org>
 */
final class WPMovieLibrary {

	/**
	 * The single instance of the plugin.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access private
	 *
	 * @var WPMovieLibrary
	 */
	private static $_instance = null;

	/**
	 * Plugin version.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $version = WPMOLY_VERSION;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Get the instance of this class, insantiating it if it doesn't exist
	 * yet.
	 *
	 * @since 3.0.0
	 *
	 * @static
	 * @access public
	 *
	 * @return WPMovieLibrary
	 */
	public static function get_instance() {

		if ( ! is_object( self::$_instance ) ) {
			self::$_instance = new static;
			self::$_instance->init();
		}
		
		return self::$_instance;
	}

	/**
	 * Initialize core.
	 *
	 * @since 3.0.0
	 *
	 * @access protected
	 */
	protected function init() {

		// Run the plugin.
		add_action( 'plugins_loaded', array( &$this, 'run' ) );

		// Load translations.
		add_action( 'wpmoly/run', array( &$this, 'translate' ) );

		// Load required files.
		add_action( 'wpmoly/run', array( &$this, 'require_registrar_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_helper_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_core_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_template_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_node_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_rest_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_tmdb_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_dashboard_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_editor_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_widget_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_shortcode_files' ) );
		add_action( 'wpmoly/run', array( &$this, 'require_library_files' ) );

		// Register Custom Post Types, Taxonomies…
		add_action( 'wpmoly/registrars/loaded', array( &$this, 'register_post_types' ) );
		add_action( 'wpmoly/registrars/loaded', array( &$this, 'register_taxonomies' ) );
		add_action( 'wpmoly/registrars/loaded', array( &$this, 'register_post_meta' ) );
		add_action( 'wpmoly/registrars/loaded', array( &$this, 'register_term_meta' ) );

		add_action( 'wpmoly/taxonomies/registered', array( &$this, 'register_taxonomy_filters' ), 10, 2 );

		// Localize.
		add_action( 'wpmoly/core/loaded', array( &$this, 'localize' ) );

		// Register Helpers.
		add_action( 'wpmoly/helpers/loaded', array( &$this, 'register_helper_filters' ) );

		// Register Query.
		add_action( 'wpmoly/core/loaded',      array( &$this, 'register_query' ) );
		add_action( 'wpmoly/query/registered', array( &$this, 'register_query_filters' ), 10, 2 );

		// REST API.
		add_action( 'wpmoly/rest/loaded', array( &$this, 'register_rest_api' ) );

		// Register Widgets.
		add_action( 'wpmoly/widgets/loaded', array( &$this, 'register_widgets' ) );

		// Register Shortcodes.
		add_action( 'wpmoly/shortcodes/loaded',     array( &$this, 'register_shortcodes' ) );
		add_action( 'wpmoly/shortcodes/registered', array( &$this, 'register_shortcode_filters' ) );

		// Register dashboard.
		add_action( 'wpmoly/dashboard/loaded', array( &$this, 'register_dashboard' ) );

		// Register front library.
		add_action( 'wpmoly/library/loaded', array( &$this, 'register_library' ) );
	}

	/**
	 * Load required registrar files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_registrar_files() {

		/**
		 * Fires before loading registrar files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/registrars/load', array( &$this ) );

		require_once WPMOLY_PATH . 'includes/registrars/class-post-types.php';
		require_once WPMOLY_PATH . 'includes/registrars/class-taxonomies.php';
		require_once WPMOLY_PATH . 'includes/registrars/class-post-meta.php';
		require_once WPMOLY_PATH . 'includes/registrars/class-term-meta.php';

		/**
		 * Fires after registrar files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/registrars/loaded', array( &$this ) );
	}

	/**
	 * Load required core files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_core_files() {

		/**
		 * Fires before loading core files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/core/load', array( &$this ) );

		require_once WPMOLY_PATH . 'includes/core/class-assets.php';
		require_once WPMOLY_PATH . 'includes/core/class-l10n.php';
		require_once WPMOLY_PATH . 'includes/core/class-query.php';

		/**
		 * Fires after core files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/core/loaded', array( &$this ) );
	}

	/**
	 * Load required template files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_template_files() {

		/**
		 * Fires before loading template files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/templates/load', array( &$this ) );

		require_once WPMOLY_PATH . 'includes/templates/class-template.php';
		require_once WPMOLY_PATH . 'includes/templates/class-admin.php';
		require_once WPMOLY_PATH . 'includes/templates/class-front.php';
		require_once WPMOLY_PATH . 'includes/templates/class-javascript.php';
		require_once WPMOLY_PATH . 'includes/templates/class-grid.php';
		require_once WPMOLY_PATH . 'includes/templates/class-headbox.php';

		/**
		 * Fires after template files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/templates/loaded', array( &$this ) );
	}

	/**
	 * Load required helper files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_helper_files() {

		/**
		 * Fires before loading helper files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/helpers/load', array( &$this ) );

		require_once WPMOLY_PATH . 'includes/helpers/defaults.php';
		require_once WPMOLY_PATH . 'includes/helpers/utils.php';
		require_once WPMOLY_PATH . 'includes/helpers/templates.php';
		require_once WPMOLY_PATH . 'includes/helpers/permalinks.php';
		require_once WPMOLY_PATH . 'includes/helpers/formatting.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-country.php';
		require_once WPMOLY_PATH . 'includes/helpers/class-language.php';

		/**
		 * Fires after helper files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/helpers/loaded', array( &$this ) );
	}

	/**
	 * Load required node files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_node_files() {

		/**
		 * Fires before loading node files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/nodes/load', array( &$this ) );

		require_once WPMOLY_PATH . 'includes/nodes/class-nodes.php';
		require_once WPMOLY_PATH . 'includes/nodes/class-node.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-image.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-default-image.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-default-backdrop.php';
		require_once WPMOLY_PATH . 'includes/nodes/images/class-default-poster.php';
		require_once WPMOLY_PATH . 'includes/nodes/posts/class-movie.php';
		require_once WPMOLY_PATH . 'includes/nodes/posts/class-grid.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-taxonomy.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-actor.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-collection.php';
		require_once WPMOLY_PATH . 'includes/nodes/taxonomies/class-genre.php';
		require_once WPMOLY_PATH . 'includes/nodes/headboxes/class-headbox.php';
		require_once WPMOLY_PATH . 'includes/nodes/headboxes/class-post.php';
		require_once WPMOLY_PATH . 'includes/nodes/headboxes/class-term.php';

		/**
		 * Fires after node files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/nodes/loaded', array( &$this ) );
	}

	/**
	 * Load required REST API files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_rest_files() {

		/**
		 * Fires before loading REST API files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/rest/load', array( &$this ) );

		require_once WPMOLY_PATH . 'includes/rest-api/class-api.php';
		require_once WPMOLY_PATH . 'includes/rest-api/fields/class-grid-meta.php';
		require_once WPMOLY_PATH . 'includes/rest-api/fields/class-movie-meta.php';
		require_once WPMOLY_PATH . 'includes/rest-api/controllers/class-grids.php';
		require_once WPMOLY_PATH . 'includes/rest-api/controllers/class-movies.php';

		/**
		 * Fires after REST API files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/rest/loaded', array( &$this ) );
	}

	/**
	 * Load required TMDb files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_tmdb_files() {

		/**
		 * Fires before loading tmdb files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/api/load', array( &$this ) );

		require_once WPMOLY_PATH . 'includes/api/class-api.php';
		require_once WPMOLY_PATH . 'includes/api/tmdb/class-tmdb.php';
		require_once WPMOLY_PATH . 'includes/api/tmdb/class-movie.php';

		/**
		 * Fires after tmdb files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/api/loaded', array( &$this ) );
	}

	/**
	 * Load required Dashboard files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_dashboard_files() {

		if ( ! is_admin() ) {
			return false;
		}

		/**
		 * Fires before loading dashboard files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/dashboard/load', array( &$this ) );

		require_once WPMOLY_PATH . 'admin/class-dashboard.php';
		require_once WPMOLY_PATH . 'admin/class-library.php';

		/**
		 * Fires after dashboard files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/dashboard/loaded', array( &$this ) );
	}

	/**
	 * Load required Editor files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_editor_files() {

		if ( ! is_admin() ) {
			return false;
		}

		/**
		 * Fires before loading editor files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/editors/load', array( &$this ) );

		require_once WPMOLY_PATH . 'admin/editors/class-permalinks.php';
		require_once WPMOLY_PATH . 'admin/editors/class-editor.php';
		require_once WPMOLY_PATH . 'admin/editors/class-page.php';
		require_once WPMOLY_PATH . 'admin/editors/class-grid.php';
		require_once WPMOLY_PATH . 'admin/editors/class-movie.php';
		require_once WPMOLY_PATH . 'admin/editors/class-term.php';

		/**
		 * Fires after editor files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/editors/loaded', array( &$this ) );
	}

	/**
	 * Load required Widget files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_widget_files() {

		/**
		 * Fires before loading widget files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/widgets/load', array( &$this ) );

		require_once WPMOLY_PATH . 'includes/widgets/class-widget.php';
		require_once WPMOLY_PATH . 'includes/widgets/class-statistics.php';
		require_once WPMOLY_PATH . 'includes/widgets/class-details.php';
		require_once WPMOLY_PATH . 'includes/widgets/class-grid.php';

		/**
		 * Fires after widget files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/widgets/loaded', array( &$this ) );
	}

	/**
	 * Load required Shortcode files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_shortcode_files() {

		/**
		 * Fires before loading shortcode files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/shortcodes/load', array( &$this ) );

		require_once WPMOLY_PATH . 'public/shortcodes/class-shortcode.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-grid.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-headbox.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-images.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-metadata.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-detail.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-countries.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-languages.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-runtime.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-release-date.php';
		require_once WPMOLY_PATH . 'public/shortcodes/class-local-release-date.php';

		/**
		 * Fires after shortcode files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/shortcodes/loaded', array( &$this ) );
	}

	/**
	 * Load required Library files.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function require_library_files() {

		/**
		 * Fires before loading library files.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/library/load', array( &$this ) );

		require_once WPMOLY_PATH . 'public/class-library.php';

		/**
		 * Fires after library files are loaded.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/library/loaded', array( &$this ) );
	}

	/**
	 * Localize the plugin.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function localize( $wpmovielibrary ) {

		/**
		 * Fires before localizing the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/localize', array( &$this ) );

		$l10n = core\L10n::get_instance();
		add_filter( 'wpmoly/filter/post_types', array( $l10n, 'localize_post_types' ) );
		add_filter( 'wpmoly/filter/taxonomies', array( $l10n, 'localize_taxonomies' ) );

		/**
		 * Fires after localizing the plugin.
		 *
		 * @since 3.0.0
		 *
		 * @param Library &$this The Plugin instance (passed by reference).
		 * @param L10n    &$l10n The L10n instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/localized', array( &$this, &$l10n ) );
	}

	/**
	 * Register the plugin's custom post types and statuses.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_post_types( $wpmovielibrary ) {

		/**
		 * Fires before registering post types.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/post_types/register', array( &$this ) );

		$registrar = new registrars\Post_Types;

		add_action( 'init', array( $registrar, 'register_post_types' ) );
		add_action( 'init', array( $registrar, 'register_post_statuses' ) );

		/**
		 * Fires after post types are registered.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this      The Plugin instance (passed by reference).
		 * @param Post_Types     &$registrar The Post Types registrar instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/post_types/registered', array( &$this, &$registrar ) );
	}

	/**
	 * Register the plugin's custom taxonomies.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_taxonomies( $wpmovielibrary ) {

		/**
		 * Fires before registering taxonomies.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/taxonomies/register', array( &$this ) );

		$registrar = new registrars\Taxonomies;

		add_action( 'init', array( $registrar, 'register_taxonomies' ) );

		/**
		 * Fires after taxonomies are registered.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this      The Plugin instance (passed by reference).
		 * @param Taxonomies     &$registrar The Taxonomies registrar instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/taxonomies/registered', array( &$this, &$registrar ) );
	}

	/**
	 * Register the plugin's default taxonomy filters.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary &$library    The Plugin instance (passed by reference).
	 * @param Taxonomies     &$taxonomies The Taxonomies registrar instance (passed by reference).
	 */
	public function register_taxonomy_filters( $wpmovielibrary, $taxonomies ) {

		add_filter( 'get_the_terms',       array( $taxonomies, 'get_the_terms' ),            10, 3 );
		add_filter( 'wp_get_object_terms', array( $taxonomies, 'get_ordered_object_terms' ), 10, 4 );
		add_filter( 'term_link',           array( $taxonomies, 'filter_term_link' ), 10, 3 );
		//add_filter( 'wpmoly/filter/post_type/movie', array( $taxonomies, 'movie_standard_taxonomies' ) );
	}

	/**
	 * Register the plugin's custom post meta.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_post_meta( $wpmovielibrary ) {

		/**
		 * Fires before registering post meta.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/post_meta/register', array( &$this ) );

		$registrar = new registrars\Post_Meta;

		add_action( 'init', array( $registrar, 'register_post_meta' ) );

		/**
		 * Fires after post meta are registered.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this      The Plugin instance (passed by reference).
		 * @param Post_Meta      &$registrar The post meta registrar instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/post_meta/registered', array( &$this, &$registrar ) );
	}

	/**
	 * Register the plugin's custom term meta.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_term_meta( $wpmovielibrary ) {

		/**
		 * Fires before registering term meta.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/term_meta/register', array( &$this ) );

		$registrar = new registrars\Term_Meta;

		add_action( 'init', array( $registrar, 'register_term_meta' ) );

		/**
		 * Fires after term meta are registered.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this      The Plugin instance (passed by reference).
		 * @param Term_Meta      &$registrar The term meta registrar instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/term_meta/registered', array( &$this, &$registrar ) );
	}

	/**
	 * Register the plugin's default helper filters.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_helper_filters( $wpmovielibrary ) {

		// Meta prefix
		add_filter( 'wpmoly/filter/actor/meta/key',      'prefix_actor_meta_key',  15, 1 );
		add_filter( 'wpmoly/filter/collection/meta/key', 'prefix_collection_meta_key',  15, 1 );
		add_filter( 'wpmoly/filter/genre/meta/key',      'prefix_genre_meta_key',  15, 1 );
		add_filter( 'wpmoly/filter/grid/meta/key',       'prefix_grid_meta_key',  15, 1 );
		add_filter( 'wpmoly/filter/movie/meta/key',      'prefix_movie_meta_key', 15, 1 );

		// Meta Formatting
		add_filter( 'wpmoly/filter/the/movie/actors',               'get_formatted_movie_cast',               15, 2 );
		add_filter( 'wpmoly/filter/the/movie/adult',                'get_formatted_movie_adult',              15, 2 );
		add_filter( 'wpmoly/filter/the/movie/author',               'get_formatted_movie_author',             15, 2 );
		add_filter( 'wpmoly/filter/the/movie/budget',               'get_formatted_movie_budget',             15, 2 );
		add_filter( 'wpmoly/filter/the/movie/cast',                 'get_formatted_movie_cast',               15, 2 );
		add_filter( 'wpmoly/filter/the/movie/certification',        'get_formatted_movie_certification',      15, 2 );
		add_filter( 'wpmoly/filter/the/movie/composer',             'get_formatted_movie_composer',           15, 2 );
		add_filter( 'wpmoly/filter/the/movie/director',             'get_formatted_movie_director',           15, 2 );
		add_filter( 'wpmoly/filter/the/movie/format',               'get_formatted_movie_format',             15, 2 );
		add_filter( 'wpmoly/filter/the/movie/genres',               'get_formatted_movie_genres',             15, 2 );
		add_filter( 'wpmoly/filter/the/movie/homepage',             'get_formatted_movie_homepage',           15, 2 );
		add_filter( 'wpmoly/filter/the/movie/imdb_id',              'get_formatted_movie_imdb_id',            15, 2 );
		add_filter( 'wpmoly/filter/the/movie/language',             'get_formatted_movie_language',           15, 2 );
		add_filter( 'wpmoly/filter/the/movie/local_release_date',   'get_formatted_movie_local_release_date', 15, 2 );
		add_filter( 'wpmoly/filter/the/movie/media',                'get_formatted_movie_media',              15, 2 );
		add_filter( 'wpmoly/filter/the/movie/photography',          'get_formatted_movie_photography',        15, 2 );
		add_filter( 'wpmoly/filter/the/movie/production_countries', 'get_formatted_movie_countries',          15, 2 );
		add_filter( 'wpmoly/filter/the/movie/production_companies', 'get_formatted_movie_production',         15, 2 );
		add_filter( 'wpmoly/filter/the/movie/producer',             'get_formatted_movie_producer',           15, 2 );
		add_filter( 'wpmoly/filter/the/movie/rating',               'get_formatted_movie_rating',             15, 2 );
		add_filter( 'wpmoly/filter/the/movie/release_date',         'get_formatted_movie_release_date',       15, 2 );
		add_filter( 'wpmoly/filter/the/movie/revenue',              'get_formatted_movie_revenue',            15, 2 );
		add_filter( 'wpmoly/filter/the/movie/runtime',              'get_formatted_movie_runtime',            15, 2 );
		add_filter( 'wpmoly/filter/the/movie/spoken_languages',     'get_formatted_movie_spoken_languages',   15, 2 );
		add_filter( 'wpmoly/filter/the/movie/status',               'get_formatted_movie_status',             15, 2 );
		add_filter( 'wpmoly/filter/the/movie/subtitles',            'get_formatted_movie_subtitles',          15, 2 );
		add_filter( 'wpmoly/filter/the/movie/tmdb_id',              'get_formatted_movie_tmdb_id',            15, 2 );
		add_filter( 'wpmoly/filter/the/movie/writer',               'get_formatted_movie_writer',             15, 2 );
		add_filter( 'wpmoly/filter/the/movie/year',                 'get_formatted_movie_year',               15, 2 );

		// Meta Permalinks
		add_filter( 'wpmoly/filter/meta/adult/url',              'get_movie_adult_url',            15, 2 );
		add_filter( 'wpmoly/filter/meta/author/url',             'get_movie_author_url',           15, 2 );
		add_filter( 'wpmoly/filter/meta/budget/url',             'get_movie_budget_url',           15, 2 );
		add_filter( 'wpmoly/filter/meta/certification/url',      'get_movie_certification_url',    15, 2 );
		add_filter( 'wpmoly/filter/meta/composer/url',           'get_movie_composer_url',         15, 2 );
		add_filter( 'wpmoly/filter/meta/homepage/url',           'get_movie_homepage_url',         15, 2 );
		add_filter( 'wpmoly/filter/meta/imdb_id/url',            'get_movie_imdb_id_url',          15, 2 );
		add_filter( 'wpmoly/filter/meta/local_release_date/url', 'get_movie_release_date_url',     15, 2 );
		add_filter( 'wpmoly/filter/meta/photography/url',        'get_movie_photography_url',      15, 2 );
		add_filter( 'wpmoly/filter/meta/producer/url',           'get_movie_producer_url',         15, 2 );
		add_filter( 'wpmoly/filter/meta/production/url',         'get_movie_production_url',       15, 2 );
		add_filter( 'wpmoly/filter/meta/country/url',            'get_movie_country_url',          15, 2 );
		add_filter( 'wpmoly/filter/meta/release_date/url',       'get_movie_release_date_url',     15, 2 );
		add_filter( 'wpmoly/filter/meta/revenue/url',            'get_movie_revenue_url',          15, 2 );
		add_filter( 'wpmoly/filter/meta/spoken_languages/url',   'get_movie_spoken_languages_url', 15, 2 );
		add_filter( 'wpmoly/filter/meta/tmdb_id/url',            'get_movie_tmdb_id_url',          15, 2 );
		add_filter( 'wpmoly/filter/meta/writer/url',             'get_movie_writer_url',           15, 2 );
		add_filter( 'wpmoly/filter/meta/year/url',               'get_movie_year_url',             15, 2 );

		// Details Permalinks
		add_filter( 'wpmoly/filter/detail/format/url',           'get_movie_format_url',    15, 2 );
		add_filter( 'wpmoly/filter/detail/language/url',         'get_movie_language_url',  15, 2 );
		add_filter( 'wpmoly/filter/detail/media/url',            'get_movie_media_url',     15, 2 );
		add_filter( 'wpmoly/filter/detail/rating/url',           'get_movie_rating_url',    15, 2 );
		add_filter( 'wpmoly/filter/detail/status/url',           'get_movie_status_url',    15, 2 );
		add_filter( 'wpmoly/filter/detail/subtitles/url',        'get_movie_subtitles_url', 15, 2 );
	}

	/**
	 * Register the plugin's query hooks.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_query( $wpmovielibrary ) {

		/**
		 * Fires before registering query.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/query/register', array( &$this ) );

		$query = core\Query::get_instance();
		add_filter( 'init',           array( $query, 'add_rewrite_tags' ) );
		add_filter( 'query_vars',     array( $query, 'add_query_vars' ) );
		add_filter( 'post_type_link', array( $query, 'replace_movie_link_tags' ), 10, 4 );
		add_filter( 'posts_where',    array( $query, 'filter_movies_by_letter' ), 10, 2 );
		add_filter( 'pre_get_posts',  array( $query, 'filter_movies_by_preset' ), 10, 1 );

		/**
		 * Fires after query is registered.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this  The Plugin instance (passed by reference).
		 * @param Query          &$query The query instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/query/registered', array( &$this, &$query ) );
	}

	/**
	 * Register the plugin's default query filters.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 * @param Query          &$query         The query instance (passed by reference).
	 */
	public function register_query_filters( $wpmovielibrary, $query ) {

		add_filter( 'wpmoly/filter/query/movies/alphabetical/preset/param',   array( $query, 'filter_alphabetical_movies_preset_param' ) );
		add_filter( 'wpmoly/filter/query/movies/unalphabetical/preset/param', array( $query, 'filter_unalphabetical_movies_preset_param' ) );
		add_filter( 'wpmoly/filter/query/movies/current-year/preset/param',   array( $query, 'filter_current_year_movies_preset_param' ) );
		add_filter( 'wpmoly/filter/query/movies/last-year/preset/param',      array( $query, 'filter_last_year_movies_preset_param' ) );
		add_filter( 'wpmoly/filter/query/movies/last-added/preset/param',     array( $query, 'filter_last_added_movies_preset_param' ) );
		add_filter( 'wpmoly/filter/query/movies/first-added/preset/param',    array( $query, 'filter_first_added_movies_preset_param' ) );
		add_filter( 'wpmoly/filter/query/movies/last-released/preset/param',  array( $query, 'filter_last_released_movies_preset_param' ) );
		add_filter( 'wpmoly/filter/query/movies/first-released/preset/param', array( $query, 'filter_first_released_movies_preset_param' ) );
		add_filter( 'wpmoly/filter/query/movies/incoming/preset/param',       array( $query, 'filter_incoming_movies_preset_param' ) );
		add_filter( 'wpmoly/filter/query/movies/most-rated/preset/param',     array( $query, 'filter_most_rated_movies_preset_param' ) );
		add_filter( 'wpmoly/filter/query/movies/least-rated/preset/param',    array( $query, 'filter_least_rated_movies_preset_param' ) );

		add_filter( 'wpmoly/filter/query/actors/alphabetical/preset/param',        array( $query, 'filter_alphabetical_actors_preset_param' ) );
		add_filter( 'wpmoly/filter/query/actors/unalphabetical/preset/param',      array( $query, 'filter_unalphabetical_actors_preset_param' ) );
		add_filter( 'wpmoly/filter/query/collections/alphabetical/preset/param',   array( $query, 'filter_alphabetical_collections_preset_param' ) );
		add_filter( 'wpmoly/filter/query/collections/unalphabetical/preset/param', array( $query, 'filter_unalphabetical_collections_preset_param' ) );
		add_filter( 'wpmoly/filter/query/genres/alphabetical/preset/param',        array( $query, 'filter_alphabetical_genres_preset_param' ) );
		add_filter( 'wpmoly/filter/query/genres/unalphabetical/preset/param',      array( $query, 'filter_unalphabetical_genres_preset_param' ) );

		add_filter( 'wpmoly/filter/query/movies/actor/param',         array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/adult/param',         array( $query, 'filter_meta_query_param') , 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/author/param',        array( $query, 'filter_meta_author_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/budget/param',        array( $query, 'filter_meta_interval_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/certification/param', array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/company/param',       array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/composer/param',      array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/country/param',       array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/director/param',      array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/format/param',        array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/genre/param',         array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/language/param',      array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/languages/param',     array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/local_release/param', array( $query, 'filter_meta_interval_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/media/param',         array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/photography/param',   array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/producer/param',      array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/rating/param',        array( $query, 'filter_meta_interval_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/release/param',       array( $query, 'filter_meta_interval_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/revenue/param',       array( $query, 'filter_meta_interval_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/runtime/param',       array( $query, 'filter_meta_interval_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/status/param',        array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/subtitles/param',     array( $query, 'filter_meta_query_param' ), 10, 4 );
		add_filter( 'wpmoly/filter/query/movies/writer/param',        array( $query, 'filter_meta_query_param' ), 10, 4 );

		add_filter( 'wpmoly/filter/query/movies/actor/value',         array( $query, 'filter_actor_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/adult/value',         array( $query, 'filter_adult_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/author/value',        array( $query, 'filter_author_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/budget/value',        array( $query, 'filter_money_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/certification/value', array( $query, 'filter_certification_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/company/value',       array( $query, 'filter_company_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/composer/value',      array( $query, 'filter_composer_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/country/value',       array( $query, 'filter_country_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/director/value',      array( $query, 'filter_director_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/format/value',        array( $query, 'filter_format_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/genre/value',         array( $query, 'filter_genre_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/language/value',      array( $query, 'filter_language_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/languages/value',     array( $query, 'filter_languages_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/local_release/value', array( $query, 'filter_release_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/media/value',         array( $query, 'filter_media_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/photography/value',   array( $query, 'filter_photography_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/producer/value',      array( $query, 'filter_producer_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/rating/value',        array( $query, 'filter_rating_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/release/value',       array( $query, 'filter_release_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/revenue/value',       array( $query, 'filter_money_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/runtime/value',       array( $query, 'filter_runtime_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/status/value',        array( $query, 'filter_status_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/subtitles/value',     array( $query, 'filter_subtitles_query_var' ), 10, 2 );
		add_filter( 'wpmoly/filter/query/movies/writer/value',        array( $query, 'filter_writer_query_var' ), 10, 2 );

		add_filter( 'wpmoly/filter/query/movies/rating/type', array( $query, 'filter_rating_query_type' ), 10, 2 );
	}

	/**
	 * Register the plugin's REST API functions.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_rest_api( $wpmovielibrary ) {

		/**
		 * Fires before registering REST API.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/rest/register', array( &$this ) );

		$rest_api = rest\API::get_instance();
		add_action( 'rest_api_init',                     array( $rest_api, 'register_fields' ) );
		add_filter( 'rest_movie_query',                  array( $rest_api, 'add_post_query_params' ), 10, 2 );
		add_filter( 'rest_actor_query',                  array( $rest_api, 'add_term_query_params' ), 10, 2 );
		add_filter( 'rest_collection_query',             array( $rest_api, 'add_term_query_params' ), 10, 2 );
		add_filter( 'rest_genre_query',                  array( $rest_api, 'add_term_query_params' ), 10, 2 );
		add_filter( 'rest_movie_collection_params',      array( $rest_api, 'register_collection_params' ), 10, 2 );
		add_filter( 'rest_actor_collection_params',      array( $rest_api, 'register_collection_params' ), 10, 2 );
		add_filter( 'rest_collection_collection_params', array( $rest_api, 'register_collection_params' ), 10, 2 );
		add_filter( 'rest_genre_collection_params',      array( $rest_api, 'register_collection_params' ), 10, 2 );
		add_filter( 'rest_prepare_movie',                array( $rest_api, 'prepare_movie_for_response' ), 10, 3 );

		/**
		 * Fires after REST API is registered.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this     The Plugin instance (passed by reference).
		 * @param API            &$rest_api The REST API instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/rest/registered', array( &$this, &$rest_api ) );
	}

	/**
	 * Register the plugin's Dashboard.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_dashboard( $wpmovielibrary ) {

		if ( ! is_admin() ) {
			return false;
		}

		/**
		 * Fires before registering dashboard.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/dashboard/register', array( &$this ) );

		$dashboard = new Dashboard;
		$dashboard->register_assets();

		add_action( 'wpmoly/editors/loaded', array( $dashboard, 'register_archive_editor' ) );
		add_action( 'wpmoly/editors/loaded', array( $dashboard, 'register_grid_editor' ) );
		add_action( 'wpmoly/editors/loaded', array( $dashboard, 'register_movie_editor' ) );
		add_action( 'wpmoly/editors/loaded', array( $dashboard, 'register_term_editor' ) );
		add_action( 'wpmoly/editors/loaded', array( $dashboard, 'register_permalinks_editor' ) );

		/**
		 * Fires after dashboard is registered.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this      The Plugin instance (passed by reference).
		 * @param Dashboard      &$dashboard The Dashboard instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/dashboard/registered', array( &$this, &$dashboard ) );
	}

	/**
	 * Register the plugin's Library.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_library( $wpmovielibrary ) {

		/**
		 * Fires before registering library.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/library/register', array( &$this ) );

		$library = new Library;
		$library->register_assets();

		// Custom Admin Bar menu.
		add_action( 'admin_bar_menu', array( $library, 'admin_bar_menu' ), 95, 1 );

		// Post content and title.
		add_filter( 'the_content',       array( $library, 'set_movie_post_content' ) );
		add_filter( 'the_content',       array( $library, 'set_archive_page_content' ), 10, 1 );
		add_filter( 'single_post_title', array( $library, 'set_archive_page_title' ), 10, 2 );
		add_filter( 'the_title',         array( $library, 'set_archive_page_post_title' ), 10, 2 );

		/**
		 * Fires after library is registered.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this    The Plugin instance (passed by reference).
		 * @param Library        &$library The Library instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/library/registered', array( &$this, &$library ) );
	}

	/**
	 * Register the plugin's Shortcodes.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_shortcodes( $wpmovielibrary ) {

		if ( is_admin() ) {
			return false;
		}

		/**
		 * Fires before registering shortcodes.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/shortcodes/register', array( &$this ) );

		/**
		 * Filter the list of available Shortcodes.
		 *
		 * @since 3.0.0
		 *
		 * @param array $shortcodes List of Shortcodes Classes name.
		 */
		$shortcodes = apply_filters( 'wpmoly/filter/shortcodes', array(
			'\wpmoly\shortcodes\Grid',
			'\wpmoly\shortcodes\Headbox',
			'\wpmoly\shortcodes\Images',
			'\wpmoly\shortcodes\Metadata',
			'\wpmoly\shortcodes\Detail',
			'\wpmoly\shortcodes\Countries',
			'\wpmoly\shortcodes\Languages',
			'\wpmoly\shortcodes\Local_Release_Date',
			'\wpmoly\shortcodes\Release_Date',
			'\wpmoly\shortcodes\Runtime',
		) );

		foreach ( $shortcodes as $shortcode ) {
			if ( class_exists( $shortcode ) ) {
				add_action( 'init', array( $shortcode, 'register' ) );
			}
		}

		/**
		 * Fires after shortcodes are registered.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this       The Plugin instance (passed by reference).
		 * @param array          &$shortcodes The Shortcodes list (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/shortcodes/registered', array( &$this, &$shortcodes ) );
	}

	/**
	 * Register the plugin's default shortcodes filters.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_shortcode_filters( $wpmovielibrary ) {

		// Meta Formatting
		add_filter( 'wpmoly/shortcode/format/adult/value',                'get_formatted_movie_adult',              15, 2 );
		add_filter( 'wpmoly/shortcode/format/author/value',               'get_formatted_movie_author',             15, 2 );
		add_filter( 'wpmoly/shortcode/format/budget/value',               'get_formatted_movie_budget',             15, 2 );
		add_filter( 'wpmoly/shortcode/format/certification/value',        'get_formatted_movie_certification',      15, 2 );
		add_filter( 'wpmoly/shortcode/format/composer/value',             'get_formatted_movie_composer',           15, 2 );
		add_filter( 'wpmoly/shortcode/format/director/value',             'get_formatted_movie_director',           15, 2 );
		add_filter( 'wpmoly/shortcode/format/homepage/value',             'get_formatted_movie_homepage',           15, 2 );
		add_filter( 'wpmoly/shortcode/format/cast/value',                 'get_formatted_movie_cast',               15, 2 );
		add_filter( 'wpmoly/shortcode/format/genres/value',               'get_formatted_movie_genres',             15, 2 );
		add_filter( 'wpmoly/shortcode/format/imdb_id/value',              'get_formatted_movie_imdb_id',            15, 2 );
		add_filter( 'wpmoly/shortcode/format/local_release_date/value',   'get_formatted_movie_local_release_date', 15, 2 );
		add_filter( 'wpmoly/shortcode/format/photography/value',          'get_formatted_movie_photography',        15, 2 );
		add_filter( 'wpmoly/shortcode/format/production_countries/value', 'get_formatted_movie_countries',          15, 2 );
		add_filter( 'wpmoly/shortcode/format/production_companies/value', 'get_formatted_movie_production',         15, 2 );
		add_filter( 'wpmoly/shortcode/format/producer/value',             'get_formatted_movie_producer',           15, 2 );
		add_filter( 'wpmoly/shortcode/format/release_date/value',         'get_formatted_movie_release_date',       15, 2 );
		add_filter( 'wpmoly/shortcode/format/revenue/value',              'get_formatted_movie_revenue',            15, 2 );
		add_filter( 'wpmoly/shortcode/format/runtime/value',              'get_formatted_movie_runtime',            15, 2 );
		add_filter( 'wpmoly/shortcode/format/spoken_languages/value',     'get_formatted_movie_spoken_languages',   15, 2 );
		add_filter( 'wpmoly/shortcode/format/tmdb_id/value',              'get_formatted_movie_tmdb_id',            15, 2 );
		add_filter( 'wpmoly/shortcode/format/writer/value',               'get_formatted_movie_writer',             15, 2 );

		// Details Formatting
		add_filter( 'wpmoly/shortcode/format/format/value',               'get_formatted_movie_format',             15, 2 );
		add_filter( 'wpmoly/shortcode/format/language/value',             'get_formatted_movie_language',           15, 2 );
		add_filter( 'wpmoly/shortcode/format/media/value',                'get_formatted_movie_media',              15, 2 );
		add_filter( 'wpmoly/shortcode/format/rating/value',               'get_formatted_movie_rating',             15, 2 );
		add_filter( 'wpmoly/shortcode/format/status/value',               'get_formatted_movie_status',             15, 2 );
		add_filter( 'wpmoly/shortcode/format/subtitles/value',            'get_formatted_movie_subtitles',          15, 2 );
	}

	/**
	 * Register the plugin's Widgets.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_widgets( $wpmovielibrary ) {

		/**
		 * Fires before registering widgets.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this The Plugin instance (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/widgets/register', array( &$this ) );

		/**
		 * Filter the list of available Widgets.
		 *
		 * @since 3.0.0
		 *
		 * @param array $widgets List of Widgets Classes name.
		 */
		$widgets = apply_filters( 'wpmoly/filter/widgets', array(
			'\wpmoly\widgets\Statistics',
			'\wpmoly\widgets\Details',
			'\wpmoly\widgets\Grid',
		) );

		foreach ( $widgets as $widget ) {
			if ( class_exists( $widget ) ) {
				add_action( 'widgets_init', array( $widget, 'register' ) );
			}
		}

		/**
		 * Fires after widgets are registered.
		 *
		 * @since 3.0.0
		 *
		 * @param WPMovieLibrary &$this    The Plugin instance (passed by reference).
		 * @param array          &$widgets The Widgets list (passed by reference).
		 */
		do_action_ref_array( 'wpmoly/widgets/registered', array( &$this, &$widgets ) );
	}

	/**
	 * Register the plugin's default widgets filters.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 *
	 * @param WPMovieLibrary $wpmovielibrary The Plugin instance (passed by reference).
	 */
	public function register_widget_filters( $wpmovielibrary ) {

		// Details Formatting
		add_filter( 'wpmoly/widget/format/format/value',    'get_formatted_movie_format',    15, 2 );
		add_filter( 'wpmoly/widget/format/language/value',  'get_formatted_movie_language',  15, 2 );
		add_filter( 'wpmoly/widget/format/media/value',     'get_formatted_movie_media',     15, 2 );
		add_filter( 'wpmoly/widget/format/rating/value',    'get_formatted_movie_rating',    15, 2 );
		add_filter( 'wpmoly/widget/format/status/value',    'get_formatted_movie_status',    15, 2 );
		add_filter( 'wpmoly/widget/format/subtitles/value', 'get_formatted_movie_subtitles', 15, 2 );
	}

	/**
	 * Load plugin translations.
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function translate() {

		$plugin_path = dirname( plugin_basename( __FILE__ ) ) . '/languages/';

		// Load main translations.
		load_plugin_textdomain( 'wpmovielibrary', false, $plugin_path );

		// Load countries and languages translations.
		load_plugin_textdomain( 'wpmovielibrary-iso', false, $plugin_path );
	}

	/**
	 * Run Forrest, run!
	 *
	 * @since 3.0.0
	 *
	 * @access public
	 */
	public function run() {

		// Run Forrest, run!
		do_action( 'wpmoly/run' );
	}

}
