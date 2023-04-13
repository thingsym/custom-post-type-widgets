<?php
/**
 * Custom_Post_Type_Widgets class
 *
 * @package Custom_Post_Type_Widgets
 *
 * @since 1.4.0
 */

/**
 * Core class Custom_Post_Type_Widgets
 *
 * @since 1.0.0
 */
class Custom_Post_Type_Widgets {

	/**
	 * Sets up a new widget instance.
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'plugins_loaded', array( $this, 'load_file' ) );

		register_uninstall_hook( __CUSTOM_POST_TYPE_WIDGETS__, array( __CLASS__, 'uninstall' ) );

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	/**
	 * Initialize.
	 *
	 * Hooks to plugins_loaded
	 *
	 * @access public
	 *
	 * @since 1.4.0
	 */
	public function init() {
		add_filter( 'plugin_row_meta', array( $this, 'plugin_metadata_links' ), 10, 2 );
	}

	/**
	 * File loader
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 */
	public function load_file() {
		$dir = plugin_dir_path( __CUSTOM_POST_TYPE_WIDGETS__ ) . 'inc/';

		include_once $dir . 'widget-custom-post-type-recent-posts.php';
		include_once $dir . 'widget-custom-post-type-archive.php';
		include_once $dir . 'widget-custom-post-type-categories.php';
		include_once $dir . 'widget-custom-post-type-calendar.php';
		include_once $dir . 'widget-custom-post-type-recent-comments.php';
		include_once $dir . 'widget-custom-post-type-tag-cloud.php';
		include_once $dir . 'widget-custom-post-type-search.php';
	}

	/**
	 * Regist widget
	 *
	 * @access public
	 *
	 * @since 1.0.0
	 */
	public function register_widgets() {
		if ( ! is_blog_installed() ) {
			return;
		}

		register_widget( 'WP_Custom_Post_Type_Widgets_Recent_Posts' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Archives' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Categories' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Calendar' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Recent_Comments' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Tag_Cloud' );
		register_widget( 'WP_Custom_Post_Type_Widgets_Search' );
	}

	/**
	 * Load textdomain
	 *
	 * @access public
	 *
	 * @return boolean
	 *
	 * @since 1.3.0
	 */
	public function load_textdomain() {
		return load_plugin_textdomain(
			'custom-post-type-widgets',
			false,
			plugin_dir_path( __CUSTOM_POST_TYPE_WIDGETS__ ) . 'languages'
		);
	}

	/**
	 * Set links below a plugin on the Plugins page.
	 *
	 * Hooks to plugin_row_meta
	 *
	 * @see https://developer.wordpress.org/reference/hooks/plugin_row_meta/
	 *
	 * @access public
	 *
	 * @param array  $links  An array of the plugin's metadata.
	 * @param string $file   Path to the plugin file relative to the plugins directory.
	 *
	 * @return array $links
	 *
	 * @since 1.4.0
	 */
	public function plugin_metadata_links( $links, $file ) {
		if ( $file == plugin_basename( __CUSTOM_POST_TYPE_WIDGETS__ ) ) {
			$links[] = '<a href="https://github.com/sponsors/thingsym">' . __( 'Become a sponsor', 'custom-post-type-widgets' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Uninstall.
	 *
	 * Hooks to uninstall_hook
	 *
	 * @access public static
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public static function uninstall() {}
}
