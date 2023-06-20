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
	public $wp_custom_post_type_widgets_search;

	public function setUp(): void {
		parent::setUp();
		$this->wp_custom_post_type_widgets_search = new WP_Custom_Post_Type_Widgets_Search();
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_search
	 */
	function constructor() {
		$this->assertSame( 'custom-post-type-search', $this->wp_custom_post_type_widgets_search->id_base );
		$this->assertSame( 'Search (Custom Post Type)', $this->wp_custom_post_type_widgets_search->name );

		$this->assertArrayHasKey( 'classname', $this->wp_custom_post_type_widgets_search->widget_options );
		$this->assertSame( 'widget_search', $this->wp_custom_post_type_widgets_search->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->wp_custom_post_type_widgets_search->widget_options );
		$this->assertContains( 'A search form for your site.', $this->wp_custom_post_type_widgets_search->widget_options['description'] );
		$this->assertArrayHasKey( 'customize_selective_refresh', $this->wp_custom_post_type_widgets_search->widget_options );
		$this->assertTrue( $this->wp_custom_post_type_widgets_search->widget_options['customize_selective_refresh'] );

		$this->assertArrayHasKey( 'id_base', $this->wp_custom_post_type_widgets_search->control_options );
		$this->assertSame( 'custom-post-type-search', $this->wp_custom_post_type_widgets_search->control_options['id_base'] );

		$this->assertSame( 'widget_custom-post-type-search', $this->wp_custom_post_type_widgets_search->option_name );
		$this->assertSame( 'widget_custom_post_type_search', $this->wp_custom_post_type_widgets_search->alt_option_name );

		$this->assertSame( 10, has_action( 'pre_get_posts', array( $this->wp_custom_post_type_widgets_search, 'query_search_filter_only_post_type' ) ) );
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

		$actual = $this->wp_custom_post_type_widgets_search->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );
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

		$actual = $this->wp_custom_post_type_widgets_search->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );

		$new_instance = array(
			'title'          => "as\n<br>df",
			'posttype'       => 'post',
		);
		$expected = array(
			'title'          => sanitize_text_field( "as\n<br>df" ),
			'posttype'       => 'post',
		);

		$actual = $this->wp_custom_post_type_widgets_search->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_search
	 */
	function form() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_search
	 */
	function query_search_filter_only_post_type() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_search
	 */
	function search_form_input() {
		ob_start();
		get_search_form();
		$form = ob_get_clean();

		$options[0] = array(
			'posttype' => ''
		);
		update_option( 'widget_custom-post-type-search', $options );

		$actual = $this->wp_custom_post_type_widgets_search->add_form_input_post_type( $form );

		$this->assertRegExp( '#<input type="hidden" name="post_type" value="">#', $actual );

		$options[0] = array(
			'posttype' => 'post'
		);
		update_option( 'widget_custom-post-type-search', $options );

		$actual = $this->wp_custom_post_type_widgets_search->add_form_input_post_type( $form );

		$this->assertRegExp( '#<input type="hidden" name="post_type" value="post">#', $actual );

		$options[0] = array(
			'posttype' => 'test'
		);
		update_option( 'widget_custom-post-type-search', $options );

		$actual = $this->wp_custom_post_type_widgets_search->add_form_input_post_type( $form );

		$this->assertRegExp( '#<input type="hidden" name="post_type" value="test">#', $actual );

	}

}
