<?php
/**
 * Plugin Name: TM Timeline
 * Plugin URI:  https://wordpress.org/plugins/tm-timeline/
 * Description: This plugin allows users to build their own timelines
 * Author:      Jetimpex
 * Author URI:  https://jetimpex.com/wordpress/
 * Text Domain: tm-timeline
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Version:     1.1.1
 * Domain Path: /languages
 *
 * @package     Timeline
 * @author      Template Monster
 * @license     GPL-3.0+
 * @copyright   2017 Template Monster
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'TM_TIMELINE_VERSION', '1.1.1' );

add_action( 'plugins_loaded', 'tm_timeline_lang', 1 );
add_action( 'plugins_loaded', 'tm_timeline_init', 2 );
add_action( 'plugins_loaded', 'tm_timeline_init_admin', 3 );

register_activation_hook( __FILE__, 'tm_timeline_activate' );
register_deactivation_hook( __FILE__, 'tm_timeline_deactivate' );

/**
 * Timeline plugin main file.
 */
require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'functions.php';


if ( ! function_exists( 'tm_timeline_lang' ) ) {
	/**
	 * Load translations.
	 */
	function tm_timeline_lang() {
		load_plugin_textdomain( 'tm-timeline', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}

if ( ! function_exists( 'tm_timeline_init' ) ) {
	/**
	 * Plugin initialization callback.
	 */
	function tm_timeline_init() {

		// Load class if it's not initialized yet.
		if ( ! class_exists( 'Tm_Timeline' ) ) {
			require tm_timeline_plugin_path( 'classes/class-tm-timeline.php' );
		}

		// Initialize plugin frontend.
		Tm_Timeline::initialize();
	}
}

if ( ! function_exists( 'tm_timeline_init_admin' ) ) {
	/**
	 * Plugin admin initialization callback.
	 */
	function tm_timeline_init_admin() {

		// Prevent non admin access.
		if ( ! is_admin() ) {
			return;
		}

		// Load class if it's not initialized yet.
		if ( ! class_exists( 'Tm_Timeline_Admin' ) ) {
			require tm_timeline_plugin_path( 'admin/classes/class-tm-timeline-admin.php' );
		}

		// Initialize plugin admin.
		Tm_Timeline_Admin::initialize();
	}
}

if ( ! function_exists( 'tm_timeline_activate' ) ) {
	/**
	 * Plugin activation callback.
	 */
	function tm_timeline_activate() {
		flush_rewrite_rules();
	}
}

if ( ! function_exists( 'tm_timeline_deactivate' ) ) {
	/**
	 * Plugin deactivation callback.
	 */
	function tm_timeline_deactivate() {
		flush_rewrite_rules();
	}
}
