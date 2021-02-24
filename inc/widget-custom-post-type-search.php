<?php
/**
 * Custom Post Type Search widget class
 *
 * @since 1.0.3
 * @package Custom Post Type Widgets
 */

/**
 * Core class WP_Custom_Post_Type_Widgets_Search
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Search extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_search',
			'description'                 => __( 'A search form for your site.', 'custom-post-type-widgets' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'custom-post-type-search', __( 'Search (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
		$this->alt_option_name = 'widget_custom_post_type_search';

		if ( ! is_admin() ) {
			add_action( 'pre_get_posts', array( $this, 'query_search_filter_only_post_type' ) );
		}
	}

	/**
	 * Outputs the content for the widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Search', 'custom-post-type-widgets' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['before_widget'];
		if ( $title ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $args['before_title'] . $title . $args['after_title'];
		}

		add_filter( 'get_search_form', array( $this, 'add_form_input_post_type' ), 10, 1 );
		get_search_form();
		remove_filter( 'get_search_form', array( $this, 'add_form_input_post_type' ) );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Archives widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form() method.
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['title']    = sanitize_text_field( $new_instance['title'] );
		$instance['posttype'] = wp_strip_all_tags( $new_instance['posttype'] );

		return $instance;
	}

	/**
	 * Outputs the settings form for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? $instance['title'] : '';
		$posttype  = isset( $instance['posttype'] ) ? $instance['posttype'] : 'post';
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Title:', 'custom-post-type-widgets' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="<?php echo $this->get_field_name( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<?php
		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		printf(
			'<p><label for="%1$s">%2$s</label>' .
			'<select class="widefat" id="%1$s" name="%3$s">',
			/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
			$this->get_field_id( 'posttype' ),
			/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
			__( 'Post Type:', 'custom-post-type-widgets' ),
			/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
			$this->get_field_name( 'posttype' )
		);

		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( 'any' ),
			selected( 'any', $posttype, false ),
			esc_html__( 'All', 'custom-post-type-widgets' )
		);

		foreach ( $post_types as $post_type => $value ) {
			if ( 'attachment' === $post_type ) {
				continue;
			}

			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $post_type ),
				selected( $post_type, $posttype, false ),
				esc_html__( $value->label, 'custom-post-type-widgets' )
			);

		}
		echo '</select></p>';
	}

	/**
	 * Adds the post_type to query.
	 *
	 * Hooks to pre_get_posts
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param string $query
	 */
	public function query_search_filter_only_post_type( $query ) {
		/**
		* The publicly_queryable of 'page' post type is false.
		* query_vars 'post_type' is unset, or set 'any'
		* see function 'parse_request' in wp-includes/class-wp.php
		* function that set post_type to $query
		*/

		/* @phpstan-ignore-next-line */
		if ( $query->is_search ) {
			$filter_post_type = '';

			$post_types          = get_post_types( array( 'public' => true ), 'objects' );
			$post_types['any'] = array();

			// 'page' post type only
			if ( isset( $_GET['post_type'] ) && 'page' === $_GET['post_type'] ) {
				$filter_post_type = 'page';
			}

			/**
			 * Filters the arguments for the Search widget.
			 *
			 * Filter hook: custom_post_type_widgets/search/filter_post_type
			 *
			 * @since 1.0.0
			 *
			 * @param string $filter_post_type filters the post type
			 */
			$filter_post_type = apply_filters( 'custom_post_type_widgets/search/filter_post_type', $filter_post_type );

			if ( $filter_post_type && array_key_exists( $filter_post_type, $post_types ) ) {
				/* @phpstan-ignore-next-line */
				$query->set( 'post_type', $filter_post_type );
			}
		}
	}

	/**
	 * Adds post_type input with search form.
	 *
	 * Hooks to get_search_form
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param string $form
	 *
	 * @return string $form
	 */
	public function add_form_input_post_type( $form ) {
		$options  = get_option( $this->option_name );
		$posttype = ! empty( $options[ $this->number ]['posttype'] ) ? $options[ $this->number ]['posttype'] : '';
		$insert   = '<input type="hidden" name="post_type" value="' . $posttype . '">';

		$form = str_replace( '</form>', $insert . '</form>', $form );

		return $form;
	}
}
