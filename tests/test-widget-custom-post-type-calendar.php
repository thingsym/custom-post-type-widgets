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
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_calendar
	 */
	function get_month_link_custom_post_type() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
