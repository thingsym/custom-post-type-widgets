<?php
/**
 * Class Test_WP_Custom_Post_Type_Widgets_Recent_Posts
 *
 * @package Custom_Post_Type_Widgets
 */

/**
 * Sample test case.
 */
class Test_WP_Custom_Post_Type_Widgets_Recent_Posts extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->wp_custom_post_type_widgets_recent_posts = new WP_Custom_Post_Type_Widgets_Recent_Posts();
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_recent_posts
	 */
	function constructor() {
		$this->assertSame( 'custom-post-type-recent-posts', $this->wp_custom_post_type_widgets_recent_posts->id_base );
		$this->assertSame( 'Recent Posts (Custom Post Type)', $this->wp_custom_post_type_widgets_recent_posts->name );

		$this->assertArrayHasKey( 'classname', $this->wp_custom_post_type_widgets_recent_posts->widget_options );
		$this->assertSame( 'widget_recent_entries', $this->wp_custom_post_type_widgets_recent_posts->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->wp_custom_post_type_widgets_recent_posts->widget_options );
		$this->assertStringContainsString( 'Your site&#8217;s most recent custom Posts.', $this->wp_custom_post_type_widgets_recent_posts->widget_options['description'] );
		$this->assertArrayHasKey( 'customize_selective_refresh', $this->wp_custom_post_type_widgets_recent_posts->widget_options );
		$this->assertTrue( $this->wp_custom_post_type_widgets_recent_posts->widget_options['customize_selective_refresh'] );

		$this->assertArrayHasKey( 'id_base', $this->wp_custom_post_type_widgets_recent_posts->control_options );
		$this->assertSame( 'custom-post-type-recent-posts', $this->wp_custom_post_type_widgets_recent_posts->control_options['id_base'] );

		$this->assertSame( 'widget_custom-post-type-recent-posts', $this->wp_custom_post_type_widgets_recent_posts->option_name );
		$this->assertSame( 'widget_custom_post_type_recent_posts', $this->wp_custom_post_type_widgets_recent_posts->alt_option_name );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_recent_posts
	 */
	function widget() {
		$posts = $this->factory->post->create_many( 5 );

		$args = array(
			'before_widget' => '<aside id="custom-post-type-recent-posts-1" class="widget widget_recent_entries">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		);
		$instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'post',
			'number'         => 5,
			'show_date'      => false,
		);

		ob_start();
		$this->wp_custom_post_type_widgets_recent_posts->widget( $args, $instance );
		$widget = ob_get_clean();

		$this->assertRegExp( '#<h3 class="widget-title">aaaaa</h3>#', $widget );
		$this->assertRegExp( '#<aside id="custom-post-type-recent-posts-1" class="widget widget_recent_entries">#', $widget );

		$count = mb_substr_count($widget, "<li>");
		$this->assertSame( $count, 5 );

	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_recent_posts
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
		// $actual = $this->wp_custom_post_type_widgets_recent_posts->update( $new_instance, array() );
		//
		// $this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_recent_posts
	 */
	function update_case_none_input() {
		$new_instance = array(
			'title'          => '',
			'posttype'       => '',
			'number'         => '',
			'show_date'      => false,
		);
		$expected = array(
			'title'          => '',
			'posttype'       => '',
			'number'         => 0,
			'show_date'      => false,
		);

		$actual = $this->wp_custom_post_type_widgets_recent_posts->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_recent_posts
	 */
	function update_case_input() {
		$new_instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'post',
			'number'         => 5,
			'show_date'      => true,
		);
		$expected = array(
			'title'          => 'aaaaa',
			'posttype'       => 'post',
			'number'         => 5,
			'show_date'      => true,
		);

		$actual = $this->wp_custom_post_type_widgets_recent_posts->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );

		$new_instance = array(
			'title'          => "as\n<br>df",
			'posttype'       => 'post',
			'number'         => 'aaaa',
			'show_date'      => null,
		);
		$expected = array(
			'title'          => sanitize_text_field( "as\n<br>df" ),
			'posttype'       => 'post',
			'number'         => 0,
			'show_date'      => false,
		);

		$actual = $this->wp_custom_post_type_widgets_recent_posts->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_recent_posts
	 */
	function form() {
		$instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'post',
			'number'         => 5,
			'show_date'      => true,
		);

		ob_start();
		$this->wp_custom_post_type_widgets_recent_posts->form( $instance );
		$form = ob_get_clean();

		$this->assertRegExp( '#name="widget\-custom\-post\-type\-recent\-posts\[\]\[title\]" type="text" value="aaaaa"#', $form );
		$this->assertRegExp( '#value="post" selected=\'selected\'#', $form );
		$this->assertRegExp( '#name="widget\-custom\-post\-type\-recent\-posts\[\]\[number\]" type="text" value="5"#', $form );
		$this->assertRegExp( '#type="checkbox"  checked=\'checked\' id="widget\-custom\-post\-type\-recent\-posts\-\-show_date"#', $form );
	}

}
