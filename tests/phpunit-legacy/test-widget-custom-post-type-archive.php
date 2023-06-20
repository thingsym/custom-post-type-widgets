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
		$this->assertSame( 'custom-post-type-archives', $this->wp_custom_post_type_widgets_archives->id_base );
		$this->assertSame( 'Archives (Custom Post Type)', $this->wp_custom_post_type_widgets_archives->name );

		$this->assertArrayHasKey( 'classname', $this->wp_custom_post_type_widgets_archives->widget_options );
		$this->assertSame( 'widget_archive', $this->wp_custom_post_type_widgets_archives->widget_options['classname'] );
		$this->assertArrayHasKey( 'description', $this->wp_custom_post_type_widgets_archives->widget_options );
		$this->assertContains( 'A monthly archive of your site&#8217;s Posts.', $this->wp_custom_post_type_widgets_archives->widget_options['description'] );
		$this->assertArrayHasKey( 'customize_selective_refresh', $this->wp_custom_post_type_widgets_archives->widget_options );
		$this->assertTrue( $this->wp_custom_post_type_widgets_archives->widget_options['customize_selective_refresh'] );

		$this->assertArrayHasKey( 'id_base', $this->wp_custom_post_type_widgets_archives->control_options );
		$this->assertSame( 'custom-post-type-archives', $this->wp_custom_post_type_widgets_archives->control_options['id_base'] );

		$this->assertSame( 'widget_custom-post-type-archives', $this->wp_custom_post_type_widgets_archives->option_name );
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
			'archive_type'   => '',
			'count'          => '',
			'dropdown'       => '',
			'order'          => '',

		);
		$expected = array(
			'title'          => '',
			'posttype'       => '',
			'archive_type'   => '',
			'count'          => false,
			'dropdown'       => false,
			'order'          => '',
		);

		$actual = $this->wp_custom_post_type_widgets_archives->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function update_case_input() {
		$new_instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'post',
			'archive_type'   => 'monthly',
			'count'          => false,
			'dropdown'       => false,
			'order'          => 'DESC',
		);
		$expected = array(
			'title'          => 'aaaaa',
			'posttype'       => 'post',
			'archive_type'   => 'monthly',
			'count'          => false,
			'dropdown'       => false,
			'order'          => 'DESC',
		);

		$actual = $this->wp_custom_post_type_widgets_archives->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );

		$new_instance = array(
			'title'          => "as\n<br>df",
			'posttype'       => 'post',
			'archive_type'   => 'monthly',
			'count'          => true,
			'dropdown'       => true,
			'order'          => 'ASC',
		);
		$expected = array(
			'title'          => sanitize_text_field( "as\n<br>df" ),
			'posttype'       => 'post',
			'archive_type'   => 'monthly',
			'count'          => true,
			'dropdown'       => true,
			'order'          => 'ASC',
		);

		$actual = $this->wp_custom_post_type_widgets_archives->update( $new_instance, array() );

		$this->assertSame( $expected, $actual );
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
	function get_year_link_custom_post_type() {
		$this->_register_post_type();

		$new_instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'test',
			'archive_type'   => 'yearly',
			'count'          => false,
			'dropdown'       => false,
			'order'          => 'DESC',
		);

		$this->wp_custom_post_type_widgets_archives->update( $new_instance, array() );

		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/archives/%post_id%' );

		$this->_register_sidebar_widget( 3, $new_instance );

		$expected = 'http://example.org/archives/test/date/2019';

		$url = 'http://example.org/archives/date/2019';
		$actual = $this->wp_custom_post_type_widgets_archives->get_year_link_custom_post_type( $url, '2019' );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function get_day_link_custom_post_type() {
		$this->_register_post_type();

		$new_instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'test',
			'archive_type'   => 'daily',
			'count'          => false,
			'dropdown'       => false,
			'order'          => 'DESC',
		);

		$this->wp_custom_post_type_widgets_archives->update( $new_instance, array() );

		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/archives/%post_id%' );

		$this->_register_sidebar_widget( 3, $new_instance );

		$expected = 'http://example.org/archives/test/date/2019/08/13';

		$url = 'http://example.org/archives/date/2019/08/13';
		$actual = $this->wp_custom_post_type_widgets_archives->get_day_link_custom_post_type( $url, '2019', '08', '13' );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function get_month_link_custom_post_type() {
		$this->_register_post_type();

		$new_instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'test',
			'archive_type'   => 'monthly',
			'count'          => false,
			'dropdown'       => false,
			'order'          => 'DESC',
		);

		$this->wp_custom_post_type_widgets_archives->update( $new_instance, array() );

		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/archives/%post_id%' );

		$this->_register_sidebar_widget( 3, $new_instance );

		$expected = 'http://example.org/archives/test/date/2019/08';

		$url = 'http://example.org/archives/date/2019/08';
		$actual = $this->wp_custom_post_type_widgets_archives->get_month_link_custom_post_type( $url, '2019', '08' );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function trim_post_type() {
		$new_instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'test',
			'archive_type'   => 'monthly',
			'count'          => false,
			'dropdown'       => false,
			'order'          => 'DESC',
		);

		$this->wp_custom_post_type_widgets_archives->update( $new_instance, array() );

		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/archives/%post_id%' );

		$this->_register_sidebar_widget( 3, $new_instance );

		$expected = 'http://example.org/archives/test/date/2019/08';

		$url = 'http://example.org/archives/test/date/2019/08?post_type=test';
		$actual = $this->wp_custom_post_type_widgets_archives->trim_post_type( $url );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @test
	 * @group wp_custom_post_type_widgets_archives
	 */
	function trim_post_type_filter() {
		$new_instance = array(
			'title'          => 'aaaaa',
			'posttype'       => 'test',
			'archive_type'   => 'monthly',
			'count'          => false,
			'dropdown'       => false,
			'order'          => 'DESC',
		);

		$this->wp_custom_post_type_widgets_archives->update( $new_instance, array() );

		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/archives/%post_id%' );

		$this->_register_sidebar_widget( 3, $new_instance );

		add_filter( 'custom_post_type_widgets/archive/trim_post_type', array( $this, '_filter_trim_post_type' ), 10, 3 );

		$expected = 'http://example.org/archives/test/date/2019/08?post_type=abc';

		$url = 'http://example.org/archives/test/date/2019/08?post_type=test';
		$actual = $this->wp_custom_post_type_widgets_archives->trim_post_type( $url );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * hook test
	 */
	function _filter_trim_post_type( $new_link_html, $link_html, $posttype ) {
		$this->assertSame( $new_link_html, 'http://example.org/archives/test/date/2019/08' );
		$this->assertSame( $link_html, 'http://example.org/archives/test/date/2019/08?post_type=test' );
		$this->assertSame( $posttype, 'test' );

		$new_link_html = $new_link_html . '?post_type=abc';

		return $new_link_html;
	}

	/**
	 * Utility
	 */
	function _register_sidebar_widget( $id, $new_option ) {
		$this->wp_custom_post_type_widgets_archives->number = $id;
		$options  = get_option( $this->wp_custom_post_type_widgets_archives->option_name );
		$options[ $id ] = $new_option;
		update_option( $this->wp_custom_post_type_widgets_archives->option_name, $options );
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
