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

		$this->assertArrayHasKey( 'id_base', $this->wp_custom_post_type_widgets_calendar->control_options );
		$this->assertEquals( 'custom-post-type-calendar', $this->wp_custom_post_type_widgets_calendar->control_options['id_base'] );

		$this->assertEquals( 'widget_custom-post-type-calendar', $this->wp_custom_post_type_widgets_calendar->option_name );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_calendar
	 */
	function widget() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
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

		$validate = $this->wp_custom_post_type_widgets_calendar->update( $new_instance, array() );

		$this->assertEquals( $validate, $expected );
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

		$validate = $this->wp_custom_post_type_widgets_calendar->update( $new_instance, array() );

		$this->assertEquals( $validate, $expected );

		$new_instance = array(
			'title'          => "as\n<br>df",
			'posttype'       => 'post',
		);
		$expected = array(
			'title'          => sanitize_text_field( "as\n<br>df" ),
			'posttype'       => 'post',
		);

		$validate = $this->wp_custom_post_type_widgets_calendar->update( $new_instance, array() );

		$this->assertEquals( $validate, $expected );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_calendar
	 */
	function form() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}

}
