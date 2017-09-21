<?php
/**
 * Define the Local Release Date Shortcode class.
 *
 * @link https://wpmovielibrary.com
 * @since 3.0.0
 *
 * @package WPMovieLibrary
 */

namespace wpmoly\shortcodes;

use wpmoly\templates\Front as Template;

/**
 * Local Release Date Shortcode class.
 *
 * @since 3.0.0
 * @package WPMovieLibrary
 *
 * @author Charlie Merland <charlie@caercam.org>
 */
class Languages extends Metadata {

	/**
	 * Shortcode name, used for declaring the Shortcode
	 *
	 * @var string
	 */
	public static $name = 'movie_languages';

	/**
	 * Shortcode attributes sanitizers
	 *
	 * @var array
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
	);

	/**
	 * Shortcode aliases
	 *
	 * @var array
	 */
	protected static $aliases = array(
		'movie_language'  => 'spoken_languages',
		'movie_lang'      => 'spoken_languages',
	);

	/**
	 * Build the Shortcode.
	 *
	 * @since 3.0.0
	 */
	protected function make() {

		parent::make();

		// Hard set key
		$this->attributes['key'] = 'spoken_languages';
	}
}
