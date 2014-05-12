<?php
/**
 * WPMovieLibrary
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

if ( ! class_exists( 'WPMovieLibrary_Admin' ) ) :

	/**
	* Plugin Admin class.
	*
	* @package WPMovieLibrary_Admin
	* @author  Charlie MERLAND <charlie.merland@gmail.com>
	*/
	class WPMovieLibrary_Admin extends WPML_Module {

		/**
		 * Slug of the plugin screen.
		 *
		 * @since    1.0.0
		 * @var      string
		 */
		protected $plugin_screen_hook_suffix = null;

		/**
		 * Plugin Settings.
		 *
		 * @since    1.0.0
		 * @var      string
		 */
		protected $settings;
		protected static $default_settings;

		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 */
		protected function __construct() {

			if ( ! is_admin() )
				return false;

			$this->register_hook_callbacks();

			$this->modules = array(
				'WPML_Settings'    => WPML_Settings::get_instance(),
				'WPML_TMDb'        => WPML_TMDb::get_instance(),
				'WPML_Utils'       => WPML_Utils::get_instance(),
				'WPML_Edit_Movies' => WPML_Edit_Movies::get_instance(),
				'WPML_Media'       => WPML_Media::get_instance(),
				'WPML_Import'      => WPML_Import::get_instance(),
				'WPML_Queue'       => WPML_Queue::get_instance()
			);

			error_reporting( E_ALL );
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', array( $this, 'init' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			add_filter( 'pre_update_option_wpml_settings', array( $this, 'filter_settings' ), 10, 2 );

			// Add the options page and menu item.
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			//add_action( 'admin_footer', array( $this, 'movie_showcase' ) );

			// highlight the proper top level menu
			add_action( 'parent_file', array( $this, 'admin_menu_highlight' ) );

			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		}

		/**
		 * Register and enqueue admin-specific style sheet.
		 *
		 * @since     1.0.0
		 *
		 * @return    null    Return early if no settings page is registered.
		 */
		public function enqueue_admin_styles() {


			if ( ! isset( $this->plugin_screen_hook_suffix ) )
				return;

			wp_enqueue_style( WPML_SLUG .'-admin-common', WPML_URL . '/assets/css/admin-common.css', array(), WPML_VERSION );

			if ( ! WPML_Utils::is_modern_wp() )
				wp_enqueue_style( WPML_SLUG . '-legacy', WPML_URL . '/assets/css/legacy.css', array(), WPML_VERSION );

			$screen = get_current_screen();
			if ( in_array( $screen->id, $this->plugin_screen_hook_suffix ) )
				wp_enqueue_style( WPML_SLUG .'-admin-styles', WPML_URL . '/assets/css/admin.css', array(), WPML_VERSION );

		}

		/**
		 * Register and enqueue admin-specific JavaScript.
		 *
		 * @since     1.0.0
		 *
		 * @return    null    Return early if no settings page is registered.
		 */
		public function enqueue_admin_scripts() {

			global $current_screen;

			if ( ! isset( $this->plugin_screen_hook_suffix ) || ! in_array( $current_screen->id, $this->plugin_screen_hook_suffix ) )
				return;

			// Main admin script, containing basic functions
			wp_enqueue_script( WPML_SLUG . '-admin-script', WPML_URL . '/assets/js/admin.js', array( 'jquery' ), WPML_VERSION, true );
			wp_localize_script(
				WPML_SLUG . '-admin-script', 'wpml_ajax',
				$this->localize_script()
			);

			// Settings script
			if ( $current_screen->id == $this->plugin_screen_hook_suffix['settings'] )
				wp_enqueue_script( WPML_SLUG . '-settings', WPML_URL . '/assets/js/wpml.settings.js', array( WPML_SLUG . '-admin-script', 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ), WPML_VERSION, true );

			if ( $current_screen->id == $this->plugin_screen_hook_suffix['landing_page'] )
				wp_enqueue_script( WPML_SLUG . '-landing', WPML_URL . '/assets/js/wpml.landing.js', array( 'jquery' ), WPML_VERSION, true );

			/*if ( $current_screen->id == $this->plugin_screen_hook_suffix['new_movie'] )
				wp_enqueue_script( 'jquery-effects-shake' );*/

		}

		private function localize_script() {

			$base_urls = WPML_TMDb::get_image_url();
			$localize = array(
				'utils' => array(
					'wpml_check' => wp_create_nonce( 'wpml-callbacks-nonce' ),
					'base_url' => array(
						'xxsmall'	=> $base_urls['poster']['xx-small'],
						'xsmall'	=> $base_urls['poster']['x-small'],
						'small'		=> $base_urls['backdrop']['small'],
						'medium'	=> $base_urls['backdrop']['medium'],
						'full'		=> $base_urls['backdrop']['full'],
						'original'	=> $base_urls['backdrop']['original'],
					)
				),
				'lang' => array(
					'deleted_movie'		=> __( 'One movie successfully deleted.', WPML_SLUG ),
					'deleted_movies'	=> __( '%s movies successfully deleted.', WPML_SLUG ),
					'dequeued_movie'	=> __( 'One movie removed from the queue.', WPML_SLUG ),
					'dequeued_movies'	=> __( '%s movies removed from the queue.', WPML_SLUG ),
					'done'			=> __( 'Done!', WPML_SLUG ),
					'empty_key'		=> __( 'I can\'t test an empty key, you know.', WPML_SLUG ),
					'enqueued_movie'	=> __( 'One movie added to the queue.', WPML_SLUG ),
					'enqueued_movies'	=> __( '%s movies added to the queue.', WPML_SLUG ),
					'images_added'		=> __( 'Images added!', WPML_SLUG ),
					'image_from'		=> __( 'Image from', WPML_SLUG ),
					'images_uploaded'	=> __( 'Images uploaded!', WPML_SLUG ),
					'import_images'		=> __( 'Import Images', WPML_SLUG ),
					'import_images_title'	=> __( 'Import Images for "%s"', WPML_SLUG ),
					'import_images_wait'	=> __( 'Please wait while the images are uploaded...', WPML_SLUG ),
					'import_poster'		=> __( 'Import Poster', WPML_SLUG ),
					'import_poster_title'	=> __( 'Select a poster for "%s"', WPML_SLUG ),
					'import_poster_wait'	=> __( 'Please wait while the poster is uploaded...', WPML_SLUG ),
					'imported'		=> __( 'Imported', WPML_SLUG ),
					'imported_movie'	=> __( 'One movie successfully imported!', WPML_SLUG ),
					'imported_movies'	=> __( '%s movies successfully imported!', WPML_SLUG ),
					'in_progress'		=> __( 'Progressing', WPML_SLUG ),
					'length_key'		=> __( 'Invalid key: it should be 32 characters long.', WPML_SLUG ),
					'load_images'		=> __( 'Load Images', WPML_SLUG ),
					'load_more'		=> __( 'Load More', WPML_SLUG ),
					'loading_images'	=> __( 'Loading Images…', WPML_SLUG ),
					'oops'			=> __( 'Oops… Did something went wrong?', WPML_SLUG ),
					'poster'		=> __( 'Poster', WPML_SLUG ),
					'save_image'		=> __( 'Saving Images…', WPML_SLUG ),
					'search_movie_title'	=> __( 'Searching movie', WPML_SLUG ),
					'search_movie'		=> __( 'Fetching movie data', WPML_SLUG ),
					'see_less'		=> __( 'see no more', WPML_SLUG ),
					'see_more'		=> __( 'see more', WPML_SLUG ),
					'set_featured'		=> __( 'Setting featured image…', WPML_SLUG ),
				)
			);

			return $localize;
		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                              Settings
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Register the administration menu for this plugin into the WordPress
		 * Dashboard menu.
		 * 
		 * TODO: export support
		 *
		 * @since    1.0.0
		 */
		public function admin_menu() {

			add_menu_page(
				$page_title = WPML_NAME,
				$menu_title = __( 'Movies', WPML_SLUG ),
				$capability = 'manage_options',
				$menu_slug = 'wpmovielibrary',
				$function = null,
				$icon_url = ( WPML_Utils::is_modern_wp() ? 'dashicons-format-video' : WPML_URL . '/assets/img/icon-movie.png' ),
				$position = 6
			);

			$this->plugin_screen_hook_suffix['landing_page'] = add_submenu_page(
				'wpmovielibrary',
				WPML_NAME,
				WPML_NAME,
				'manage_options',
				'wpmovielibrary',
				__CLASS__ . '::landing_page'
			);

			$this->plugin_screen_hook_suffix['all_movies'] = add_submenu_page(
				'wpmovielibrary',
				__( 'All Movies', WPML_SLUG ),
				__( 'All Movies', WPML_SLUG ),
				'manage_options',
				'edit.php?post_type=movie',
				null
			);

			$this->plugin_screen_hook_suffix['new_movie'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Add New', WPML_SLUG ),
				__( 'Add New', WPML_SLUG ),
				'manage_options',
				'post-new.php?post_type=movie',
				null
			);

			if ( WPML_Settings::taxonomies__enable_collection() ) :
			$this->plugin_screen_hook_suffix['collections'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Collections', WPML_SLUG ),
				__( 'Collections', WPML_SLUG ),
				'manage_options',
				'edit-tags.php?taxonomy=collection&post_type=movie',
				null
			);
			endif;

			if ( WPML_Settings::taxonomies__enable_genre() ) :
			$this->plugin_screen_hook_suffix['genres'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Genres', WPML_SLUG ),
				__( 'Genres', WPML_SLUG ),
				'manage_options',
				'edit-tags.php?taxonomy=genre&post_type=movie',
				null
			);
			endif;

			if ( WPML_Settings::taxonomies__enable_actor() ) :
			$this->plugin_screen_hook_suffix['actors'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Actors', WPML_SLUG ),
				__( 'Actors', WPML_SLUG ),
				'manage_options',
				'edit-tags.php?taxonomy=actor&post_type=movie',
				null
			);
			endif;

			$this->plugin_screen_hook_suffix['import'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Import Movies', WPML_SLUG ),
				__( 'Import Movies', WPML_SLUG ),
				'manage_options',
				'wpml_import',
				'WPML_Import::import_page'
			);
			/*add_submenu_page(
				'wpmovielibrary',
				__( 'Export Movies', WPML_SLUG ),
				__( 'Export Movies', WPML_SLUG ),
				'manage_options',
				'export',
				__CLASS__ . '::export_page'
			);*/
			$this->plugin_screen_hook_suffix['settings'] = add_submenu_page(
				'wpmovielibrary',
				__( 'Settings', WPML_SLUG ),
				__( 'Settings', WPML_SLUG ),
				'manage_options',
				'wpml_edit_settings',
				__CLASS__ . '::admin_page'
			);
		}

		/**
		 * Highlight Admin submenu for related admin pages
		 * 
		 * @link    http://wordpress.org/support/topic/moving-taxonomy-ui-to-another-main-menu#post-2432769
		 * 
		 * @since    1.0.0
		 * 
		 * @return   string    Updated parent if needed, current else
		 */
		public function admin_menu_highlight() {

			global $current_screen, $submenu_file, $submenu, $parent_file;

			if ( isset( $submenu['wpmovielibrary'] ) )
				foreach ( $submenu['wpmovielibrary'] as $item )
					if ( htmlspecialchars_decode( $submenu_file ) == $item[ 2 ] )
						$submenu_file = htmlspecialchars_decode( $submenu_file );

			if ( 'movie' != $current_screen->post_type )
				return $parent_file;

			if ( in_array( $current_screen->taxonomy, array( 'collection', 'genre', 'actor' ) ) )
				return $parent_file = 'wpmovielibrary';

			if ( in_array( $current_screen->id, $this->plugin_screen_hook_suffix ) )
				return $parent_file = 'wpmovielibrary';

			return $parent_file;
		}

		/**
		 * Render the Settings Page.
		 * 
		 * Is either one of the maintenance tools is at use, handle it
		 * before doing anything. As of now maintenance tools are 
		 * restricted to default settings restoration and cache cleaning.
		 *
		 * @since    1.0.0
		 */
		public static function admin_page() {

			if ( ! current_user_can( 'manage_options' ) )
				wp_die( __( 'Access denied.', WPML_SLUG ) );

			// Restore default settings?
			if ( isset( $_GET['wpml_restore_default'] ) && 'true' == $_GET['wpml_restore_default'] ) {

				// Check Nonce URL
				if ( ! isset( $_GET['wpml_restore_default_nonce'] ) || ! wp_verify_nonce( $_GET['wpml_restore_default_nonce'], 'wpml-restore-default' ) ) {
					add_settings_error(  null, 'restore_default', __( 'You don\'t have the permission do perform this action.', WPML_SLUG ), 'error' );
				}
				else {
					$action = WPML_Settings::update_settings( $force = true );
					if ( ! $action )
						add_settings_error(  null, 'empty_cache', __( 'Unknown error: failed to restore default settings.', WPML_SLUG ), 'error' );
					else
						add_settings_error(  null, 'empty_cache', __( 'Default settings restored!', WPML_SLUG ), 'updated' );
				}
			}

			// Empty Cache?
			if ( isset( $_GET['wpml_empty_cache'] ) && 'true' == $_GET['wpml_empty_cache'] ) {

				// Check Nonce URL
				if ( ! isset( $_GET['wpml_empty_cache_nonce'] ) || ! wp_verify_nonce( $_GET['wpml_empty_cache_nonce'], 'wpml-empty-cache' ) ) {
					add_settings_error(  null, 'empty_cache', __( 'You don\'t have the permission do perform this action.', WPML_SLUG ), 'error' );
				}
				else {
					$action = WPML_Utils::empty_cache();
					if ( is_wp_error( $action ) )
						add_settings_error(  null, 'empty_cache', $action->get_error_message(), 'error' );
					else
						add_settings_error(  null, 'empty_cache', $action, 'updated' );
				}
			}

			$_allowed = array( 'api', 'movies', 'taxonomies', 'deactivate', 'uninstall', 'maintenance' );
			$_section = ( isset( $_REQUEST['wpml_section'] ) && in_array( $_REQUEST['wpml_section'], $_allowed ) ) ? esc_attr( $_REQUEST['wpml_section'] ) : 'api' ;

			include_once( plugin_dir_path( __FILE__ ) . 'settings/views/page-settings.php' );
		}

		/**
		 * Render WPML Landing Page.
		 * 
		 * Create a nice landing page for the plugin, displaying recent
		 * movies and other stuff like a simple shortcut menu.
		 * 
		 * @since    1.0.0
		 */
		public static function landing_page() {

			global $wpdb;

			$movies = $wpdb->get_results(
				'SELECT p.*, m.meta_value AS meta, mm.meta_value AS rating
				 FROM ' . $wpdb->posts . ' AS p
				 LEFT JOIN ' . $wpdb->postmeta . ' AS m ON m.post_id=p.ID AND m.meta_key="_wpml_movie_data"
				 LEFT JOIN ' . $wpdb->postmeta . ' AS mm ON mm.post_id=p.ID AND mm.meta_key="_wpml_movie_rating"
				 WHERE post_type="movie"
				   AND post_status="publish"
				 GROUP BY p.ID
				 ORDER BY post_date
				 LIMIT 0,8'
			);

			if ( ! empty( $movies ) ) {
				foreach ( $movies as $movie ) {

					$movie->meta = unserialize( $movie->meta );
					$movie->meta = array(
						'title' => apply_filters( 'the_title', $movie->meta['meta']['title'] ),
						'runtime' => apply_filters( 'wpml_filter_filter_runtime', $movie->meta['meta']['runtime'] ),
						'release_date' => apply_filters( 'wpml_filter_filter_release_date', $movie->meta['meta']['release_date'] ),
						'overview' => apply_filters( 'the_content', $movie->meta['meta']['overview'] )
					);
					$movie->meta = json_encode( $movie->meta );

					if ( has_post_thumbnail( $movie->ID ) ) {
						$movie->poster = wp_get_attachment_image_src( get_post_thumbnail_id( $movie->ID ), 'large' );
						$movie->poster = $movie->poster[0];
					}
					else
						$movie->poster = WPML_DEFAULT_POSTER_URL;

					$attachments = get_children( $args = array( 'post_parent' => $movie->ID, 'post_type' => 'attachment' ) );
					if ( ! empty( $attachments ) ) {
						shuffle( $attachments );
						$movie->backdrop = wp_get_attachment_image_src( $attachments[0]->ID, 'full' );
						$movie->backdrop = $movie->backdrop[0];
					}
					else
						$movie->backdrop = $movie->poster;
				}
			}

			include_once( plugin_dir_path( __FILE__ ) . 'common/views/landing-page.php' );
		}

		public function movie_showcase() {

			global $current_screen;

			if ( $current_screen->id != $this->plugin_screen_hook_suffix['landing_page'] )
				return false;

			include_once( plugin_dir_path( __FILE__ ) . 'common/views/movie-showcase.php' );
		}

		/**
		 * Registers settings sections, fields and settings
		 *
		 * @since    1.0.0
		 */
		public function register_settings() {

			global $wpml_settings;

			foreach ( $wpml_settings as $section ) {

				if ( isset( $section['section'] ) && isset( $section['settings'] ) ) {

					$section_id = $section['section']['id'];
					$section_title = $section['section']['title'];

					add_settings_section( "wpml_settings-$section_id", $section_title, __CLASS__ . '::markup_section_headers', 'wpml_settings' );

					foreach ( $section['settings'] as $id => $field ) {

						$callback = isset( $field['callback'] ) ? $field['callback'] : 'markup_fields';

						add_settings_field( $id, __( $field['title'], WPML_SLUG ), array( $this, $callback ), 'wpml_settings', "wpml_settings-$section_id", array( 'id' => $id, 'section' => $section_id ) + $field );
					}
				}
			}

			// The settings container
			register_setting(
				'wpml_edit_settings',
				'wpml_settings'
			);
		}

		/**
		 * Adds the section introduction text to the Settings page
		 *
		 * @mvc Controller
		 *
		 * @param array $section
		 */
		public static function markup_section_headers( $section ) {
			include( plugin_dir_path( __FILE__ ) . 'settings/views/page-settings-section-headers.php' );
		}

		/**
		 * Delivers the markup for settings fields
		 *
		 * @mvc Controller
		 *
		 * @param array $field
		 */
		public function markup_fields( $field ) {

			$settings = WPML_Settings::get_settings();

			$_type  = esc_attr( $field['type'] );
			$_title = esc_attr( $field['title'] );
			$_id    = "wpml_settings-{$field['section']}-{$field['id']}";
			$_name  = "wpml_settings[{$field['section']}][{$field['id']}]";
			$_value = $settings[ $field['section'] ][ $field['id'] ];

			include( plugin_dir_path( __FILE__ ) . 'settings/views/page-settings-fields.php' );
		}

		/**
		 * Delivers the markup for default_movie_meta settings fields
		 *
		 * @param array $field
		 */
		public function sorted_markup_fields( $field ) {

			$settings = WPML_Settings::get_settings();

			$_type  = 'sorted';
			$_title = esc_attr( $field['title'] );
			$_id    = "wpml_settings-{$field['section']}-{$field['id']}";
			$_name  = "wpml_settings[{$field['section']}][{$field['id']}]";

			if ( 'default_movie_meta' == $field['id'] && isset( $settings['wpml']['default_movie_meta_sorted'] ) )
				$_value = $settings[ $field['section'] ]['default_movie_meta_sorted'];
			else
				$_value = $settings[ $field['section'] ][ $field['id'] ];

			$items      = WPML_Settings::get_supported_movie_meta();
			$selected   = $_value;
			$selectable = array_diff( array_keys( $items ), $selected );
			$selectable = empty( $selectable ) ? array_keys( $items ) : $selectable;

			$draggable = ''; $droppable = ''; $options = '';

			foreach ( $selected as $meta ) :
				if ( isset( $items[ $meta ] ) )
					$draggable .= '<li data-movie-meta="' . $meta . '" class="default_movie_meta_selected">' . __( $items[ $meta ]['title'], WPML_SLUG ) . '</li>';
			endforeach;
			foreach ( $selectable as $meta ) :
				$droppable .= '<li data-movie-meta="' . $meta . '" class="default_movie_meta_droppable">' . __( $items[ $meta ]['title'], WPML_SLUG ) . '</li>';
			endforeach;

			foreach ( $items as $slug => $meta ) :
				$check = in_array( $slug, $_value );
				$options .= '<option value="' . $slug . '"' . selected( $check, true, false ) . '>' . __( $meta['title'], WPML_SLUG ) . '</option>';
			endforeach;

			include( plugin_dir_path( __FILE__ ) . 'settings/views/page-settings-fields.php' );
		}

		/**
		 * Filter the submitted settings to detect unsupported data.
		 * 
		 * Most fields can be tested easily, but the default movie meta
		 * and details need a for specific test using array_intersect()
		 * to avoid storing unsupported values.
		 * 
		 * Settings submitted as array when there's no use to are converted
		 * to simpler types.
		 *
		 * @since    1.0.0
		 * 
		 * @param    array    $settings Settings array to filter
		 * @param    array    $defaults Default Settings to match against submitted settings
		 * 
		 * @return   array    Filtered Settings
		 */
		protected static function validate_settings( $settings, $defaults = array() ) {

			$defaults = ( ! empty( $defaults ) ? $defaults : WPML_Settings::get_default_settings() );
			$_settings = array();

			if ( is_null( $settings ) || ! is_array( $settings ) )
				return $settings;

			// Loop through settings
			foreach ( $settings as $slug => $setting ) {

				// Is the setting valid?
				if ( isset( $defaults[ $slug ] ) ) {

					if ( in_array( $slug, array( 'default_movie_meta', 'default_movie_details' ) ) ) {
						$allowed = array_keys( call_user_func( 'WPML_Settings::get_supported_' . str_replace( 'default_', '', $slug ) ) );
						$_settings[ $slug ] = array_intersect( $setting, $allowed );
					}
					else {
						if ( is_array( $setting ) && 1 == count( $setting ) )
							$setting = $setting[0];

						if ( is_array( $setting ) )
							$setting = self::validate_settings( $setting, $defaults[ $slug ] );
						else if ( is_numeric( $setting ) )
							$setting = filter_var( $setting, FILTER_VALIDATE_INT );
						else
							$setting = sanitize_text_field( $setting );
						$_settings[ $slug ] = $setting;
					}
				}
			}

			return $_settings;
		}

		/**
		 * Validate the submitted Settings
		 * 
		 * This essentially checks for sorted movie meta, as this option
		 * is more a visual stuff an as such, not stored in a regular
		 * setting field.
		 * 
		 * Also check for changes on the URL Rewriting of Taxonomies to
		 * update the Rewrite Rules if needed. We need to do so to avoid
		 * users to get 404 when they try to access their content if they
		 * didn't previously reload the Dashboard Permalink page.
		 * 
		 * @since    1.0.0
		 * 
		 * @param    array    $new_settings Array containing the new settings
		 * @param    array    $old_settings Array containing the old settings
		 * 
		 * @return   array    Validated settings
		 */
		public static function filter_settings( $new_settings, $old_settings ) {

			$settings = self::validate_settings( $new_settings );
			$settings[ WPML_SETTINGS_REVISION_NAME ] = WPML_SETTINGS_REVISION;

			if ( isset( $new_settings['wpml']['default_movie_meta_sorted'] ) && '' != $new_settings['wpml']['default_movie_meta_sorted'] ) {

				$meta_sorted = explode( ',', $new_settings['wpml']['default_movie_meta_sorted'] );
				$meta = WPML_Settings::get_supported_movie_meta();

				foreach ( $meta_sorted as $i => $_meta )
					if ( ! in_array( $_meta, array_keys( $meta ) ) )
						unset( $meta_sorted[ $i ] );

				$settings['wpml']['default_movie_meta_sorted'] = $meta_sorted;
				$settings['wpml']['default_movie_meta'] = $meta_sorted;
			}

			// Check for changes in URL Rewrite
			$updated_movie_rewrite = ( isset( $old_settings['wpml']['movie_rewrite'] ) &&
						   isset( $settings['wpml']['movie_rewrite'] ) &&
						   $old_settings['wpml']['movie_rewrite'] != $settings['wpml']['movie_rewrite'] );

			$updated_details_rewrite = ( isset( $old_settings['wpml']['details_rewrite'] ) &&
						   isset( $settings['wpml']['details_rewrite'] ) &&
						   $old_settings['wpml']['details_rewrite'] != $settings['wpml']['details_rewrite'] );

			$updated_collection_rewrite = ( isset( $old_settings['taxonomies']['collection_rewrite'] ) &&
							isset( $settings['taxonomies']['collection_rewrite'] ) &&
							$old_settings['taxonomies']['collection_rewrite'] != $settings['taxonomies']['collection_rewrite'] );

			$updated_genre_rewrite = ( isset( $old_settings['taxonomies']['genre_rewrite'] ) &&
						   isset( $settings['taxonomies']['genre_rewrite'] ) &&
						   $old_settings['taxonomies']['genre_rewrite'] != $settings['taxonomies']['genre_rewrite'] );

			$updated_actor_rewrite = ( isset( $old_settings['taxonomies']['actor_rewrite'] ) &&
						   isset( $settings['taxonomies']['actor_rewrite'] ) &&
						   $old_settings['taxonomies']['actor_rewrite'] != $settings['taxonomies']['actor_rewrite'] );

			// Update Rewrite Rules if needed
			if ( $updated_movie_rewrite || $updated_details_rewrite || $updated_collection_rewrite || $updated_genre_rewrite || $updated_actor_rewrite )
				add_settings_error( null, 'url_rewrite', sprintf( __( 'You update the taxonomies URL rewrite. You should visit <a href="%s">WordPress Permalink</a> page to update the Rewrite rules; you may experience errors when trying to load pages using the new URL if the structures are not update correctly. Tip: you don\'t need to change anything in the Permalink page: simply loading it will update the rules.', WPML_SLUG ), admin_url( '/options-permalink.php' ) ), 'updated' );

			return $settings;
		}

		/**
		 * Render movie export page
		 *
		 * @since    1.0.0
		 */
		public function export_page() {
			// TODO: implement export
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0.0
		 *
		 * @param bool $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0.0
		 */
		public function deactivate() {}

		/**
		 * Initializes variables
		 *
		 * @since    1.0.0
		 */
		public function init() {

			$this->plugin_screen_hook_suffix = array(
				'edit_movie' => 'edit-movie',
				'movie' => 'movie',
				'plugins' => 'plugins'
			);

			self::$default_settings = WPML_Settings::get_default_settings();
			$this->settings         = WPML_Settings::get_settings();

		}

	}
endif;
