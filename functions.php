<?php
/**
 * Utilities
 *
 * @package    Tm_Timeline
 * @subpackage Tm_Timeline
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2017 Template Monster
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Useful utilities
 */

if ( ! function_exists( 'tm_timeline_plugin_url' ) ) {
	/**
	 * Get plugin file/folder url
	 *
	 * @since  1.0.0
	 * @since  1.0.5  Refactor a logic.
	 * @param  string $path Relative path.
	 * @return string
	 */
	function tm_timeline_plugin_url( $path ) {
		return untrailingslashit( plugin_dir_url( __FILE__ ) ) . $path;
	}
}

if ( ! function_exists( 'tm_timeline_plugin_path' ) ) {
	/**
	 * Get plugin file/folder absolute path.
	 *
	 * @since  1.0.0
	 * @since  1.0.5  Refactor a logic.
	 * @param  string $path Relative path.
	 * @return string
	 */
	function tm_timeline_plugin_path( $path ) {
		return trailingslashit( plugin_dir_path( __FILE__ ) ) . $path;
	}
}
