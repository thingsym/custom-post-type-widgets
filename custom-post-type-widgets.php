<?php
/**
 * Plugin Name: Custom Post Type Widgets
 * Plugin URI: https://github.com/thingsym/custom-post-type-widgets
 * Description: This plugin adds default custom post type widgets.
 * Version: 1.1.2
 * Author: thingsym
 * Author URI: http://www.thingslabo.com/
 * License: GPL2
 * Text Domain: custom-post-type-widgets
 * Domain Path: /languages/
 */

class Custom_Post_Type_Widgets {
	public function __construct() {
		add_action( 'widgets_init', array( $this, 'load' ), 9 );
		add_action( 'widgets_init', array( $this, 'init' ), 10 );
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	public function load() {
		$dir = plugin_dir_path( __FILE__ );

		include_once( $dir . 'inc/widget-custom-post-type-recent-posts.php' );
		include_once( $dir . 'inc/widget-custom-post-type-archive.php' );
		include_once( $dir . 'inc/widget-custom-post-type-categories.php' );
		include_once( $dir . 'inc/widget-custom-post-type-calendar.php' );
		include_once( $dir . 'inc/widget-custom-post-type-recent-comments.php' );
		include_once( $dir . 'inc/widget-custom-post-type-tag-cloud.php' );
		include_once( $dir . 'inc/widget-custom-post-type-search.php' );
	}

	public function init() {
		if ( ! is_blog_installed() ) {
			return;
		}

		load_plugin_textdomain( 'custom-post-type-widgets', false, 'custom-post-type-widgets/languages' );

		register_widget( 'WP_Custom_Post_Type_Widgets_Recent_Posts' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Archives' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Categories' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Calendar' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Recent_Comments' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Tag_Cloud' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Search' );
	}

	public function uninstall() {}
}

$custom_post_type_widgets = new Custom_Post_Type_Widgets();
