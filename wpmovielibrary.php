<?php
/**
 * WPMovieLibrary Bootstrap file.
 *
 * @link              http://wpmovielibrary.com
 * @since             3.0
 * @package           WPMovieLibrary
 *
 * @wordpress-plugin
 * Plugin Name:       WPMovieLibrary
 * Plugin URI:        https://wpmovielibrary.com
 * Description:       WordPress Movie Library is an advanced movie library managing plugin to turn your WordPress Blog into a Movie Library. 
 * Version:           3.0-alpha2
 * Author:            Charlie Merland
 * Author URI:        https://charliemerland.me/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       wpmovielibrary
 * Domain Path:       /languages
 */

namespace wpmoly;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPMOLY_SLUG',    'wpmoly' );
define( 'WPMOLY_NAME',    'WPMovieLibrary' );
define( 'WPMOLY_DOMAIN',  'wpmovielibrary' );
define( 'WPMOLY_VERSION', '3.0-alpha2' );
define( 'WPMOLY_URL',     plugins_url( basename( __DIR__ ) ) . '/' );
define( 'WPMOLY_PATH',    plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 *
 * @since    3.0
 */
function activate() {

	require_once WPMOLY_PATH . 'includes/core/class-activator.php';
	Core\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * @since    3.0
 */
function deactivate() {

	require_once WPMOLY_PATH . 'includes/core/class-deactivator.php';
	Core\Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate' );
register_deactivation_hook( __FILE__, 'deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 *
 * @since    3.0
 */
require WPMOLY_PATH . 'includes/class-library.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    3.0
 */
$wpmoly = Library::get_instance();
$wpmoly->run();
