<?php
/**
 * Plugin Name: Webby Performance
 * Plugin URI:  https://github.com/thingsym/webby-performance
 * Description: Webby Performance plugin adds advanced functionality.
 * Version:     1.0.0
 * Author:      thingsym
 * Author URI:  https://www.thingslabo.com/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: webby-performance
 * Domain Path: /languages/
 *
 * @package     Webby_Performance
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WEBBY_PERFORMANCE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'inc/autoload.php';

if ( class_exists( 'Webby_Performance\Webby_Performance' ) ) {
	new \Webby_Performance\Webby_Performance();
};
