<?php
/**
 * Class Test_WP_Custom_Post_Type_Widgets_Tag_Cloud
 *
 * @package Custom_Post_Type_Widgets
 */

/**
 * Sample test case.
 */
class Test_WP_Custom_Post_Type_Widgets_Tag_Cloud extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->wp_custom_post_type_widgets_tag_cloud = new WP_Custom_Post_Type_Widgets_Tag_Cloud();
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_tag_cloud
	 */
	function constructor() {
		$this->assertEquals( 'custom-post-type-tag-cloud', $this->wp_custom_post_type_widgets_tag_cloud->id_base );
		$this->assertEquals( 'Tag Cloud (Custom Post Type)', $this->wp_custom_post_type_widgets_tag_cloud->name );

		$this->assertArrayHasKey( 'classname', $this->wp_custom_post_type_widgets_tag_cloud->widget_options );
		$this->assertEquals( 'widget_tag_cloud', $this->wp_custom_post_type_widgets_tag_cloud->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->wp_custom_post_type_widgets_tag_cloud->widget_options );
		$this->assertContains( 'A cloud of your most used tags.', $this->wp_custom_post_type_widgets_tag_cloud->widget_options['description'] );

		$this->assertArrayHasKey( 'id_base', $this->wp_custom_post_type_widgets_tag_cloud->control_options );
		$this->assertEquals( 'custom-post-type-tag-cloud', $this->wp_custom_post_type_widgets_tag_cloud->control_options['id_base'] );

		$this->assertEquals( 'widget_custom-post-type-tag-cloud', $this->wp_custom_post_type_widgets_tag_cloud->option_name );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_tag_cloud
	 */
	function update_case_none_input() {
		$new_instance = array(
			'title'          => '',
			'taxonomy'       => '',
		);
		$expected = array(
			'title'          => '',
			'taxonomy'       => '',
		);

		$validate = $this->wp_custom_post_type_widgets_tag_cloud->update( $new_instance, array() );

		$this->assertEquals( $validate, $expected );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_tag_cloud
	 */
	function update_case_input() {
		$new_instance = array(
			'title'          => 'aaaaa',
			'taxonomy'       => 'post_tag',
		);
		$expected = array(
			'title'          => 'aaaaa',
			'taxonomy'       => 'post_tag',
		);

		$validate = $this->wp_custom_post_type_widgets_tag_cloud->update( $new_instance, array() );

		$this->assertEquals( $validate, $expected );

		$new_instance = array(
			'title'          => "as\n<br>df",
			'taxonomy'       => 'post_tag',
		);
		$expected = array(
			'title'          => sanitize_text_field( "as\n<br>df" ),
			'taxonomy'       => 'post_tag',
		);

		$validate = $this->wp_custom_post_type_widgets_tag_cloud->update( $new_instance, array() );

		$this->assertEquals( $validate, $expected );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_tag_cloud
	 */
	function update() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_tag_cloud
	 */
	function form() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
