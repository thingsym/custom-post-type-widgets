<?php
/**
 * Class Test_Custom_Post_Type_Widgets_Basic
 *
 * @package Custom_Post_Type_Widgets
 */

/**
 * Basic test case.
 */
class Test_Custom_Post_Type_Widgets_Basic extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->custom_post_type_widgets = new Custom_Post_Type_Widgets();
	}

	/**
	 * @test
	 * @group basic
	 */
	function constructor() {
		$this->assertSame( 10, has_action( 'plugins_loaded', array( $this->custom_post_type_widgets, 'load_textdomain' ) ) );
		$this->assertSame( 10, has_action( 'plugins_loaded', array( $this->custom_post_type_widgets, 'init' ) ) );
		$this->assertSame( 10, has_action( 'plugins_loaded', array( $this->custom_post_type_widgets, 'load_file' ) ) );
		$this->assertSame( 10, has_action( 'widgets_init', array( $this->custom_post_type_widgets, 'register_widgets' ) ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	function init() {
		$this->custom_post_type_widgets->init();

		$this->assertSame( 10, has_filter( 'plugin_row_meta', array( $this->custom_post_type_widgets, 'plugin_metadata_links' ) ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	function load() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group basic
	 */
	function register_widgets() {
		$this->custom_post_type_widgets->register_widgets();

		global $wp_widget_factory;

		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Recent_Posts', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Archives', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Categories', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Calendar', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Recent_Comments', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Tag_Cloud', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Search', $wp_widget_factory->widgets );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function load_textdomain() {
		$loaded = $this->custom_post_type_widgets->load_textdomain();
		$this->assertFalse( $loaded );

		unload_textdomain( 'custom-post-type-widgets' );

		add_filter( 'locale', [ $this, '_change_locale' ] );
		add_filter( 'load_textdomain_mofile', [ $this, '_change_textdomain_mofile' ], 10, 2 );

		$loaded = $this->custom_post_type_widgets->load_textdomain();
		$this->assertTrue( $loaded );

		remove_filter( 'load_textdomain_mofile', [ $this, '_change_textdomain_mofile' ] );
		remove_filter( 'locale', [ $this, '_change_locale' ] );

		unload_textdomain( 'custom-post-type-widgets' );
	}

	/**
	 * hook for load_textdomain
	 */
	function _change_locale( $locale ) {
		return 'ja';
	}

	function _change_textdomain_mofile( $mofile, $domain ) {
		if ( $domain === 'custom-post-type-widgets' ) {
			$locale = determine_locale();
			$mofile = plugin_dir_path( __CUSTOM_POST_TYPE_WIDGETS__ ) . 'languages/custom-post-type-widgets-' . $locale . '.mo';

			$this->assertSame( $locale, get_locale() );
			$this->assertFileExists( $mofile );
		}

		return $mofile;
	}

	/**
	 * @test
	 * @group basic
	 */
	public function plugin_metadata_links() {
		$links = $this->custom_post_type_widgets->plugin_metadata_links( array(), plugin_basename( __CUSTOM_POST_TYPE_WIDGETS__ ) );
		$this->assertContains( '<a href="https://github.com/sponsors/thingsym">Become a sponsor</a>', $links );
	}

	/**
	 * @test
	 * @group basic
	 */
	function uninstall() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
