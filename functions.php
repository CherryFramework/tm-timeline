<?php
/**
 * Utilities
 *
 * @package    Tm_Timeline
 * @subpackage Tm_Timeline
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2016 Template Monster
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Useful utilities
 */

if ( false === function_exists( 'tm_plugin_url' ) ) {
	/**
	 * Get plugin file/folder url
	 *
	 * @param String $path Relative path.
	 *
	 * @return String
	 */
	function tm_plugin_url( $path ) {
		$plugin_name = basename( dirname( __FILE__ ) );

		return WP_PLUGIN_URL . '/' . $plugin_name . $path;
	}
}

if ( false === function_exists( 'tm_plugin_path' ) ) {
	/**
	 * Get plugin file/folder absolute path
	 *
	 * @param String $path Relative path.
	 *
	 * @return String|Boolean Returns absolute path or `false` on failure
	 * @throws InvalidArgumentException If given path is not a valid path.
	 */
	function tm_plugin_path( $path ) {
		$separators = array(
			'/',
			'//',
			'\\',
		);

		$path = array(
			plugin_dir_path( __FILE__ ),
			$path,
		);

		$path = realpath( str_replace( $separators, DIRECTORY_SEPARATOR, join( DIRECTORY_SEPARATOR, $path ) ) );

		if ( false === $path ) {
			throw new InvalidArgumentException( 'Invalid path!' );
		}

		return $path;
	}
}

if ( false === function_exists( 'tm_notify' ) ) {
	/**
	 * Output alert/error/warning messages
	 *
	 * @param String $message    Text of the message.
	 * @param String $class_name CSS class name for styling the message.
	 *
	 * @return String
	 */
	function tm_notify( $message, $class_name = 'error' ) {
		return sprintf(
			'<div class="%s updated"><p>%s</p></div>',
			esc_html( $class_name ),
			esc_html( $message )
		);
	}
}
