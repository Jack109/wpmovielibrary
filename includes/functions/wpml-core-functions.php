<?php
/**
 * WPMovieLibrary Core functions.
 * 
 * 
 * 
 * @since     1.3
 * 
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

require WPML_PATH . 'includes/functions/wpml-movies-functions.php';
require WPML_PATH . 'includes/functions/wpml-ajax-functions.php';
require WPML_PATH . 'includes/functions/wpml-legacy-functions.php';

/**
 * Filter a string value to determine a suitable boolean value.
 * 
 * This is mostly used for Shortcodes where boolean-like values
 * can be used.
 * 
 * @since    1.1.0
 * 
 * @param    string    Value to filter
 * 
 * @return   boolean   Filtered value
 */
function wpml_is_boolean( $value ) {

	$value = strtolower( $value );

	$true = array( 'true', true, 'yes', '1', 1 );
	$false = array( 'false', false, 'no', '0', 0 );

	foreach ( $true as $t )
		if ( $value === $t )
			return true;

	foreach ( $false as $f )
		if ( $value === $f )
			return false;

	return false;
}

/**
 * Convert an Array shaped list to a separated string.
 * 
 * @since    1.0.0
 * 
 * @param    array    $array Array shaped list
 * @param    string   $subrow optional subrow to select in subitems
 * @param    string   $separator Separator string to use to implode the list
 * 
 * @return   string   Separated list
 */
function wpml_stringify_array( $array, $subrow = 'name', $separator = ', ' ) {

	if ( ! is_array( $array ) || empty( $array ) )
		return $array;

	foreach ( $array as $i => $row ) {
		if ( ! is_array( $row ) )
			$array[ $i ] = $row;
		else if ( false === $subrow || ! is_array( $row ) )
			$array[ $i ] = wpml_stringify_array( $row, $subrow, $separator );
		else if ( is_array( $row ) && isset( $row[ $subrow ] ) )
			$array[ $i ] = $row[ $subrow ];
		else if ( is_array( $row ) )
			$array[ $i ] = implode( $separator, $row );
	}

	$array = implode( $separator, $array );

	return $array;
}

/**
 * Filter an array to detect empty associative arrays.
 * Uses wpml_stringify_array to stringify the array and check its length.
 * 
 * @since    1.0.0
 * 
 * @param    array    $array Array to check
 * 
 * @return   array    Original array plus and notification row if empty
 */
function wpml_filter_empty_array( $array ) {

	if ( ! is_array( $array ) || empty( $array ) )
		return array();

	$_array = wpml_stringify_array( $array, false, '' );

	return strlen( $_array ) > 0 ? $array : array_merge( array( '_empty' => true ), $array );
}

/**
 * Filter an array to remove any sub-array, reducing multidimensionnal
 * arrays.
 * 
 * @since    1.0.0
 * 
 * @param    array    $array Array to check
 * 
 * @return   array    Reduced array
 */
function wpml_filter_undimension_array( $array ) {

	if ( ! is_array( $array ) || empty( $array ) )
		return $array;

	$_array = array();

	foreach ( $array as $key => $row ) {
		if ( is_array( $row ) )
			$_array = array_merge( $_array, wpml_filter_undimension_array( $row ) );
		else
			$_array[ $key ] = $row;
	}

	return $_array;
}

/**
 * Provide a plugin-wide, generic method for generating nonce.
 *
 * @since    1.0.0
 * 
 * @param    string    $action Action name for nonce
 */
function wpml_create_nonce( $action ) {

	return wp_create_nonce( 'wpml-' . $action );
}

/**
 * Provide a plugin-wide, generic method for generating nonce fields.
 *
 * @since    1.0.0
 * 
 * @param    string    $action Action name for nonce
 */
function wpml_nonce_field( $action, $referer = true, $echo = true ) {

	$nonce_action = 'wpml-' . $action;
	$nonce_name = '_wpmlnonce_' . str_replace( '-', '_', $action );

	return wp_nonce_field( $nonce_action, $nonce_name, $referer, $echo );
}

/**
 * Provide a plugin-wide, generic method for checking admin nonces.
 *
 * @since    1.0.0
 * 
 * @param    string    $action Action name for nonce
 */
function wpml_check_admin_referer( $action, $query_arg = false ) {

	if ( ! $query_arg )
		$query_arg = '_wpmlnonce_' . str_replace( '-', '_', $action );

	$error = new WP_Error();
	$check = check_ajax_referer( 'wpml-' . $action, $query_arg );

	if ( $check )
		return true;

	$error->add( 'invalid_nonce', __( 'Are you sure you want to do this?' ) );

	return $error;
}

/**
 * Provide a plugin-wide, generic method for checking AJAX nonces.
 *
 * @since    1.0.0
 * 
 * @param    string    $action Action name for nonce
 */
function wpml_check_ajax_referer( $action, $query_arg = false, $die = false ) {

	if ( ! $query_arg )
		$query_arg = 'nonce';

	$error = new WP_Error();
	$check = check_ajax_referer( 'wpml-' . $action, $query_arg, $die );

	if ( $check )
		return true;

	$error->add( 'invalid_nonce', __( 'Are you sure you want to do this?' ) );
	wpml_ajax_response( $error, null, wpml_create_nonce( $action ) );
}

/**
 * Application/JSON headers content-type.
 * If no header was sent previously, send new header.
 *
 * @since    1.0.0
 * 
 * @param    boolean    $error Error header or normal?
 */
function wpml_json_header( $error = false ) {

	if ( false !== headers_sent() )
		return false;

	if ( $error ) {
		header( 'HTTP/1.1 500 Internal Server Error' );
		header( 'Content-Type: application/json; charset=UTF-8' );
	}	
	else {
		header( 'Content-type: application/json' );
	}
}
