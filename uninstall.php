<?php
/**
 * Uninstall the plugin
 *
 * @package    Timeline
 * @subpackage Timeline
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2017 Template Monster
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Deny foreign access
if ( false === defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Load dependencies
require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'functions.php';

if ( false === class_exists( 'Tm_Timeline' ) ) {
	require realpath(
		plugin_dir_path( __FILE__ ) . DIRECTORY_SEPARATOR .
		'classes' . DIRECTORY_SEPARATOR . 'class-tm-timeline.php'
	);
}

// Perform uninstall
Tm_Timeline::uninstall();
