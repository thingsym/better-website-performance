<?php
/**
 * Plugin Name: Better Website Performance
 * Plugin URI:  https://github.com/thingsym/better-website-performance
 * Description: The Better Website Performance plugin adds advanced features to improve website performance.
 * Version:     1.0.1
 * Author:      thingsym
 * Author URI:  https://www.thingslabo.com/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: better-website-performance
 * Domain Path: /languages/
 *
 * @package     Better_Website_Performance
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BETTER_WEBSITE_PERFORMANCE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'inc/autoload.php';

if ( class_exists( 'Better_Website_Performance\Better_Website_Performance' ) ) {
	new \Better_Website_Performance\Better_Website_Performance();
};
