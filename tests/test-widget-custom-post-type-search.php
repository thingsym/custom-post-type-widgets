<?php
/**
 * Class Test_WP_Custom_Post_Type_Widgets_Search
 *
 * @package Custom_Post_Type_Widgets
 */

/**
 * Sample test case.
 */
class Test_WP_Custom_Post_Type_Widgets_Search extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->wp_custom_post_type_widgets_search = new WP_Custom_Post_Type_Widgets_Search();
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_search
	 */
	function constructor() {
		$this->assertEquals( 'custom-post-type-search', $this->wp_custom_post_type_widgets_search->id_base );
		$this->assertEquals( 'Search (Custom Post Type)', $this->wp_custom_post_type_widgets_search->name );

		$this->assertArrayHasKey( 'classname', $this->wp_custom_post_type_widgets_search->widget_options );
		$this->assertEquals( 'widget_search', $this->wp_custom_post_type_widgets_search->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->wp_custom_post_type_widgets_search->widget_options );
		$this->assertContains( 'A search form for your site.', $this->wp_custom_post_type_widgets_search->widget_options['description'] );
		$this->assertArrayHasKey( 'customize_selective_refresh', $this->wp_custom_post_type_widgets_search->widget_options );
		$this->assertTrue( $this->wp_custom_post_type_widgets_search->widget_options['customize_selective_refresh'] );

		$this->assertArrayHasKey( 'id_base', $this->wp_custom_post_type_widgets_search->control_options );
		$this->assertEquals( 'custom-post-type-search', $this->wp_custom_post_type_widgets_search->control_options['id_base'] );

		$this->assertEquals( 'widget_custom-post-type-search', $this->wp_custom_post_type_widgets_search->option_name );
		$this->assertEquals( 'widget_custom_post_type_search', $this->wp_custom_post_type_widgets_search->alt_option_name );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_search
	 */
	function widget() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_search
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

		$validate = $this->wp_custom_post_type_widgets_search->update( $new_instance, array() );

		$this->assertEquals( $validate, $expected );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_search
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

		$validate = $this->wp_custom_post_type_widgets_search->update( $new_instance, array() );

		$this->assertEquals( $validate, $expected );

		$new_instance = array(
			'title'          => "as\n<br>df",
			'posttype'       => 'post',
		);
		$expected = array(
			'title'          => sanitize_text_field( "as\n<br>df" ),
			'posttype'       => 'post',
		);

		$validate = $this->wp_custom_post_type_widgets_search->update( $new_instance, array() );

		$this->assertEquals( $validate, $expected );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_search
	 */
	function form() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
