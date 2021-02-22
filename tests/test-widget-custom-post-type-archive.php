<?php
/**
 * Class Test_WP_Custom_Post_Type_Widgets_Archives
 *
 * @package Custom_Post_Type_Widgets
 */

/**
 * Sample test case.
 */
class Test_WP_Custom_Post_Type_Widgets_Archives extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->wp_custom_post_type_widgets_archives = new WP_Custom_Post_Type_Widgets_Archives();
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function constructor() {
		$this->assertEquals( 'custom-post-type-archives', $this->wp_custom_post_type_widgets_archives->id_base );
		$this->assertEquals( 'Archives (Custom Post Type)', $this->wp_custom_post_type_widgets_archives->name );

		$this->assertArrayHasKey( 'classname', $this->wp_custom_post_type_widgets_archives->widget_options );
		$this->assertEquals( 'widget_archive', $this->wp_custom_post_type_widgets_archives->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->wp_custom_post_type_widgets_archives->widget_options );
		$this->assertContains( 'A monthly archive of your site&#8217;s Posts.', $this->wp_custom_post_type_widgets_archives->widget_options['description'] );
		$this->assertArrayHasKey( 'customize_selective_refresh', $this->wp_custom_post_type_widgets_archives->widget_options );
		$this->assertTrue( $this->wp_custom_post_type_widgets_archives->widget_options['customize_selective_refresh'] );

		$this->assertArrayHasKey( 'id_base', $this->wp_custom_post_type_widgets_archives->control_options );
		$this->assertEquals( 'custom-post-type-archives', $this->wp_custom_post_type_widgets_archives->control_options['id_base'] );

		$this->assertEquals( 'widget_custom-post-type-archives', $this->wp_custom_post_type_widgets_archives->option_name );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function widget() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}
	// link
	// select

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function update_case_none_input() {
		$new_instance = array(
			'title'          => '',
			'posttype'       => '',
			'count'          => '',
			'dropdown'       => '',
		);
		$expected = array(
			'title'          => '',
			'posttype'       => '',
			'count'          => false,
			'dropdown'       => false,
		);

		$actual = $this->wp_custom_post_type_widgets_archives->update( $new_instance, array() );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function update_case_input() {
		$new_instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'post',
			'count'          => false,
			'dropdown'       => false,
		);
		$expected = array(
			'title'          => 'aaaaa',
			'posttype'       => 'post',
			'count'          => false,
			'dropdown'       => false,
		);

		$actual = $this->wp_custom_post_type_widgets_archives->update( $new_instance, array() );

		$this->assertEquals( $expected, $actual );

		$new_instance = array(
			'title'          => "as\n<br>df",
			'posttype'       => 'post',
			'count'          => true,
			'dropdown'       => true,
		);
		$expected = array(
			'title'          => sanitize_text_field( "as\n<br>df" ),
			'posttype'       => 'post',
			'count'          => true,
			'dropdown'       => true,
		);

		$actual = $this->wp_custom_post_type_widgets_archives->update( $new_instance, array() );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function form() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function get_month_link_custom_post_type() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function trim_post_type() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
