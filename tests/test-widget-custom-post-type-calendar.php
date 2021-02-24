<?php
/**
 * Class Test_wp_custom_post_type_widgets_calendar
 *
 * @package Custom_Post_Type_Widgets
 */

/**
 * Sample test case.
 */
class Test_wp_custom_post_type_widgets_calendar extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->wp_custom_post_type_widgets_calendar = new wp_custom_post_type_widgets_calendar();
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_calendar
	 */
	function constructor() {
		$this->assertEquals( 'custom-post-type-calendar', $this->wp_custom_post_type_widgets_calendar->id_base );
		$this->assertEquals( 'Calendar (Custom Post Type)', $this->wp_custom_post_type_widgets_calendar->name );

		$this->assertArrayHasKey( 'classname', $this->wp_custom_post_type_widgets_calendar->widget_options );
		$this->assertEquals( 'widget_calendar', $this->wp_custom_post_type_widgets_calendar->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->wp_custom_post_type_widgets_calendar->widget_options );
		$this->assertContains( 'A calendar of your site&#8217;s Posts.', $this->wp_custom_post_type_widgets_calendar->widget_options['description'] );
		$this->assertArrayHasKey( 'customize_selective_refresh', $this->wp_custom_post_type_widgets_calendar->widget_options );
		$this->assertTrue( $this->wp_custom_post_type_widgets_calendar->widget_options['customize_selective_refresh'] );

		$this->assertArrayHasKey( 'id_base', $this->wp_custom_post_type_widgets_calendar->control_options );
		$this->assertEquals( 'custom-post-type-calendar', $this->wp_custom_post_type_widgets_calendar->control_options['id_base'] );

		$this->assertEquals( 'widget_custom-post-type-calendar', $this->wp_custom_post_type_widgets_calendar->option_name );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_calendar
	 */
	function widget() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_calendar
	 */
	function update_case_none_input() {
		$new_instance = array(
			'title'          => '',
			'posttype'       => '',
		);
		$expected = array(
			'title'          => '',
			'posttype'       => '',
		);

		$actual = $this->wp_custom_post_type_widgets_calendar->update( $new_instance, array() );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_calendar
	 */
	function update_case_input() {
		$new_instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'post',
		);
		$expected = array(
			'title'          => 'aaaaa',
			'posttype'       => 'post',
		);

		$actual = $this->wp_custom_post_type_widgets_calendar->update( $new_instance, array() );

		$this->assertEquals( $expected, $actual );

		$new_instance = array(
			'title'          => "as\n<br>df",
			'posttype'       => 'post',
		);
		$expected = array(
			'title'          => sanitize_text_field( "as\n<br>df" ),
			'posttype'       => 'post',
		);

		$actual = $this->wp_custom_post_type_widgets_calendar->update( $new_instance, array() );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_calendar
	 */
	function form() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_calendar
	 */
	function get_custom_post_type_calendar() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_calendar
	 */
	function get_day_link_custom_post_type() {
		$this->_register_post_type();

		$new_instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'test',
			'count'          => false,
			'dropdown'       => false,
		);

		$this->wp_custom_post_type_widgets_calendar->update( $new_instance, array() );

		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/archives/%post_id%' );

		$this->_register_sidebar_widget( 3, $new_instance );

		$expected = 'http://example.org/archives/test/date/2019/08/13';

		$url = 'http://example.org/archives/date/2019/08/13';
		$actual = $this->wp_custom_post_type_widgets_calendar->get_day_link_custom_post_type( $url, '2019', '08', '13' );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_calendar
	 */
	function get_month_link_custom_post_type() {
		$this->_register_post_type();

		$new_instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'test',
			'count'          => false,
			'dropdown'       => false,
		);

		$this->wp_custom_post_type_widgets_calendar->update( $new_instance, array() );

		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/archives/%post_id%' );

		$this->_register_sidebar_widget( 3, $new_instance );

		$expected = 'http://example.org/archives/test/date/2019/08';

		$url = 'http://example.org/archives/date/2020/02';
		$actual = $this->wp_custom_post_type_widgets_calendar->get_month_link_custom_post_type( $url, '2019', '08' );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Utility
	 */
	function _register_sidebar_widget( $id, $new_option ) {
		$this->wp_custom_post_type_widgets_calendar->number = $id;
		$options  = get_option( $this->wp_custom_post_type_widgets_calendar->option_name );
		$options[ $id ] = $new_option;
		update_option( $this->wp_custom_post_type_widgets_calendar->option_name, $options );
	}

	/**
	 * Utility
	 */
	function _register_post_type() {
		$labels = [
			"name" => "test",
			"singular_name" => "test",
		];

		$args = [
			"label" => "test",
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"delete_with_user" => false,
			"show_in_rest" => true,
			"rest_base" => "",
			"rest_controller_class" => "WP_REST_Posts_Controller",
			"has_archive" => true,
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"delete_with_user" => false,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => [ "slug" => "test", "with_front" => true ],
			"query_var" => true,
			"supports" => [ "title", "editor", "thumbnail", "comments" ],
		];

		register_post_type( "test", $args );
	}

}
