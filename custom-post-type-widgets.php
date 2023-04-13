<?php
/**
 * Plugin Name: Custom Post Type Widgets
 * Plugin URI:  https://github.com/thingsym/custom-post-type-widgets
 * Description: This plugin adds default custom post type widgets.
 * Version:     1.5.2
 * Author:      thingsym
 * Author URI:  https://www.thingslabo.com/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: custom-post-type-widgets
 * Domain Path: /languages/
 *
 * @package         Custom_Post_Type_Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( '__CUSTOM_POST_TYPE_WIDGETS__', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'inc/class-custom-post-type-widgets.php';

if ( class_exists( 'Custom_Post_Type_Widgets' ) ) {
	new Custom_Post_Type_Widgets();
};
