<?php
/**
 * Class Test_WP_Custom_Post_Type_Widgets_Categories
 *
 * @package Custom_Post_Type_Widgets
 */

/**
 * Sample test case.
 */
class Test_WP_Custom_Post_Type_Widgets_Categories extends WP_UnitTestCase {
	public $wp_custom_post_type_widgets_categories;

	public function setUp(): void {
		parent::setUp();
		$this->wp_custom_post_type_widgets_categories = new WP_Custom_Post_Type_Widgets_Categories();
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_categories
	 */
	function constructor() {
		$this->assertSame( 'custom-post-type-categories', $this->wp_custom_post_type_widgets_categories->id_base );
		$this->assertSame( 'Categories (Custom Post Type)', $this->wp_custom_post_type_widgets_categories->name );

		$this->assertArrayHasKey( 'classname', $this->wp_custom_post_type_widgets_categories->widget_options );
		$this->assertSame( 'widget_categories', $this->wp_custom_post_type_widgets_categories->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->wp_custom_post_type_widgets_categories->widget_options );
		$this->assertStringContainsString( 'A list or dropdown of categories.', $this->wp_custom_post_type_widgets_categories->widget_options['description'] );
		$this->assertArrayHasKey( 'customize_selective_refresh', $this->wp_custom_post_type_widgets_categories->widget_options );
		$this->assertTrue( $this->wp_custom_post_type_widgets_categories->widget_options['customize_selective_refresh'] );

		$this->assertArrayHasKey( 'id_base', $this->wp_custom_post_type_widgets_categories->control_options );
		$this->assertSame( 'custom-post-type-categories', $this->wp_custom_post_type_widgets_categories->control_options['id_base'] );

		$this->assertSame( 'widget_custom-post-type-categories', $this->wp_custom_post_type_widgets_categories->option_name );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_categories
	 */
	function widget() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );

		// $categories = $this->factory->category->create_many( 5 );
		//
		// $args = array(
		// 	'before_widget' => '<aside id="custom-post-type-recent-posts-1" class="widget widget_recent_entries">',
		// 	'after_widget'  => '</aside>',
		// 	'before_title'  => '<h3 class="widget-title">',
		// 	'after_title'   => '</h3>',
		// );
		// $instance = array(
		// 	'title'          => '',
		// 	'taxonomy'       => '',
		// 	'count'          => 0,
		// 	'hierarchical'   => 0,
		// 	'dropdown'       => 0,
		// );
		//
		// var_dump($categories);
		// ob_start();
		// $this->wp_custom_post_type_widgets_categories->widget( $args, $instance );
		// $widget = ob_get_clean();
		//
		// var_dump($widget);

	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_categories
	 */
	function update_case_initial() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );

		// $new_instance = array();
		// $expected = array(
		// 	'title'          => '',
		// 	'posttype'       => '',
		// 	'number'         => 0,
		// 	'show_date'      => false,
		// );
		//
		// $actual = $this->wp_custom_post_type_widgets_categories->update( $new_instance, array() );
		//
		// $this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_categories
	 */
	function update_case_none_input() {
		$new_instance = array(
			'title'          => '',
			'taxonomy'       => '',
			'label'          => '',
			'count'          => '',
			'hierarchical'   => '',
			'dropdown'       => '',
		);
		$expected = array(
			'title'          => '',
			'taxonomy'       => '',
			'label'          => '',
			'count'          => false,
			'hierarchical'   => false,
			'dropdown'       => false,
		);

		$actual = $this->wp_custom_post_type_widgets_categories->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_categories
	 */
	function update_case_input() {
		$new_instance = array(
			'title'          => 'aaaaa',
			'taxonomy'       => 'category',
			'label'          => '',
			'count'          => false,
			'hierarchical'   => false,
			'dropdown'       => false,
		);
		$expected = array(
			'title'          => 'aaaaa',
			'taxonomy'       => 'category',
			'label'          => '',
			'count'          => false,
			'hierarchical'   => false,
			'dropdown'       => false,
		);

		$actual = $this->wp_custom_post_type_widgets_categories->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );

		$new_instance = array(
			'title'          => "as\n<br>df",
			'taxonomy'       => 'category',
			'label'          => '',
			'count'          => true,
			'hierarchical'   => true,
			'dropdown'       => true,
		);
		$expected = array(
			'title'          => sanitize_text_field( "as\n<br>df" ),
			'taxonomy'       => 'category',
			'label'          => '',
			'count'          => true,
			'hierarchical'   => true,
			'dropdown'       => true,
		);

		$actual = $this->wp_custom_post_type_widgets_categories->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_categories
	 */
	function form() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
