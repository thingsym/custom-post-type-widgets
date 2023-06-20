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
	public $wp_custom_post_type_widgets_tag_cloud;

	public function setUp(): void {
		parent::setUp();
		$this->wp_custom_post_type_widgets_tag_cloud = new WP_Custom_Post_Type_Widgets_Tag_Cloud();
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_tag_cloud
	 */
	function constructor() {
		$this->assertSame( 'custom-post-type-tag-cloud', $this->wp_custom_post_type_widgets_tag_cloud->id_base );
		$this->assertSame( 'Tag Cloud (Custom Post Type)', $this->wp_custom_post_type_widgets_tag_cloud->name );

		$this->assertArrayHasKey( 'classname', $this->wp_custom_post_type_widgets_tag_cloud->widget_options );
		$this->assertSame( 'widget_tag_cloud', $this->wp_custom_post_type_widgets_tag_cloud->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->wp_custom_post_type_widgets_tag_cloud->widget_options );
		$this->assertContains( 'A cloud of your most used tags.', $this->wp_custom_post_type_widgets_tag_cloud->widget_options['description'] );
		$this->assertArrayHasKey( 'customize_selective_refresh', $this->wp_custom_post_type_widgets_tag_cloud->widget_options );
		$this->assertTrue( $this->wp_custom_post_type_widgets_tag_cloud->widget_options['customize_selective_refresh'] );

		$this->assertArrayHasKey( 'id_base', $this->wp_custom_post_type_widgets_tag_cloud->control_options );
		$this->assertSame( 'custom-post-type-tag-cloud', $this->wp_custom_post_type_widgets_tag_cloud->control_options['id_base'] );

		$this->assertSame( 'widget_custom-post-type-tag-cloud', $this->wp_custom_post_type_widgets_tag_cloud->option_name );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_tag_cloud
	 */
	function update_case_none_input() {
		$new_instance = array(
			'title'          => '',
			'taxonomy'       => '',
			'count'          => false,
		);
		$expected = array(
			'title'          => '',
			'taxonomy'       => '',
			'count'          => false,
		);

		$actual = $this->wp_custom_post_type_widgets_tag_cloud->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );

		$new_instance = array(
			'title'          => '',
			'taxonomy'       => '',
			'count'          => true,
		);
		$expected = array(
			'title'          => '',
			'taxonomy'       => '',
			'count'          => true,
		);

		$actual = $this->wp_custom_post_type_widgets_tag_cloud->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_tag_cloud
	 */
	function update_case_input() {
		$new_instance = array(
			'title'          => 'aaaaa',
			'taxonomy'       => 'post_tag',
			'count'          => false,
		);
		$expected = array(
			'title'          => 'aaaaa',
			'taxonomy'       => 'post_tag',
			'count'          => false,
		);

		$actual = $this->wp_custom_post_type_widgets_tag_cloud->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );

		$new_instance = array(
			'title'          => "as\n<br>df",
			'taxonomy'       => 'post_tag',
			'count'          => false,
		);
		$expected = array(
			'title'          => sanitize_text_field( "as\n<br>df" ),
			'taxonomy'       => 'post_tag',
			'count'          => false,
		);

		$actual = $this->wp_custom_post_type_widgets_tag_cloud->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_tag_cloud
	 */
	function form() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_tag_cloud
	 */
	function get_taxonomy() {
		$instance = array();
		$taxonomy = $this->wp_custom_post_type_widgets_tag_cloud->get_taxonomy( $instance );

		$this->assertSame( $taxonomy, 'post_tag' );

		$instance = array(
			'taxonomy' => 'aaa'
		);
		$taxonomy = $this->wp_custom_post_type_widgets_tag_cloud->get_taxonomy( $instance );

		$this->assertSame( $taxonomy, 'post_tag' );

		$instance = array(
			'taxonomy' => 'nav_menu'
		);
		$taxonomy = $this->wp_custom_post_type_widgets_tag_cloud->get_taxonomy( $instance );

		$this->assertSame( $taxonomy, 'nav_menu' );
	}

}
