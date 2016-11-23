<?php
/**
 * Plugin Name:    TM Timeline
 * Description:    This plugin allows users to build their own timelines
 * Author:         Template Monster
 * Text Domain:    tm-timeline
 * License:        GPL-3.0+
 * License URI:    http://www.gnu.org/licenses/gpl-3.0.txt
 * Version:        1.0.4
 * Domain Path:    /languages
 *
 * @package        Timeline
 * @author         Template Monster
 * @license        GPL-3.0+
 * @copyright      2016 Template Monster
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Timeline plugin main file
 */

require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'functions.php';

if ( ! function_exists( 'tm_lang' ) ) {
	/**
	 * Load translations
	 */
	function tm_lang() {
		load_plugin_textdomain( 'tm-timeline', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}

add_action( 'plugins_loaded', 'tm_lang' );

if ( false === function_exists( 'tm_init' ) ) {
	/**
	 * Plugin initialization callback
	 */
	function tm_init() {

		// Load class if it's not initialized yet
		if ( false === class_exists( 'Tm_Timeline' ) ) {
			require tm_plugin_path( 'classes/class-tm-timeline.php' );
		}

		// Initialize plugin frontend
		Tm_Timeline::initialize();
	}
}

// Register plugin initialization callback
add_action( 'init', 'tm_init' );

if ( false === function_exists( 'tm_init_admin' ) ) {
	/**
	 * Plugin admin initialization callback
	 *
	 * @return bool
	 */
	function tm_init_admin() {

		// Prevent non admin access
		if ( false === is_admin() ) {
			return false;
		}

		// Load class if it's not initialized yet
		if ( false === class_exists( 'Tm_Timeline_Admin' ) ) {
			require tm_plugin_path( 'admin/classes/class-tm-timeline-admin.php' );
		}

		// Initialize plugin admin
		Tm_Timeline_Admin::initialize();

		return true;
	}
}

// Register plugin admin initialization callback
add_action( 'init', 'tm_init_admin' );

if ( false === function_exists( 'tm_activate' ) ) {
	/**
	 * Plugin activation callback
	 *
	 * @return bool
	 */
	function tm_activate() {

		// Flush the rewrite rules
		flush_rewrite_rules();
	}
}

register_activation_hook( __FILE__, 'tm_activate' );

if ( false === function_exists( 'tm_deactivate' ) ) {
	/**
	 * Plugin deactivation callback
	 *
	 * @return bool
	 */
	function tm_deactivate() {

		// Flush the rewrite rules
		flush_rewrite_rules();
	}
}

register_deactivation_hook( __FILE__, 'tm_deactivate' );
