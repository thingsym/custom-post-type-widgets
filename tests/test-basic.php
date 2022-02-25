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
		$result = $this->custom_post_type_widgets->load_textdomain();
		$this->assertNull( $result );
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
