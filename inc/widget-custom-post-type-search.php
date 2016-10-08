<?php
/**
 * Custom Post Type Search widget class
 *
 * @since 1.0.3
 * @package Custom Post Type Widgets
 */

class WP_Custom_Post_Type_Widgets_Search extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_search', 'description' => __( 'A search form for your site.', 'custom-post-type-widgets' ) );
		parent::__construct( 'custom-post-type-search', __( 'Search (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
		$this->alt_option_name = 'widget_custom_post_type_search';

		if ( ! is_admin() ) {
			add_action( 'pre_get_posts', array( $this, 'query_search_filter_only_post_type' ) );
		}
	}

	public function query_search_filter_only_post_type( $query ) {
		/**
		* publicly_queryable of 'page' post type is false.
		* query_vars 'post_type' is unset, or set 'any'
		* see function 'parse_request' in wp-includes/class-wp.php
		* function that set post_type to $query
		*/

		if ( $query->is_search ) {
			$filter_post_type = '';
			$post_types = get_post_types( array( 'public' => true ), 'objects' );

			// 'page' post type only
			if ( isset($_GET['post_type']) && 'page' === $_GET['post_type'] ) {
				$filter_post_type = 'page';
			}

			$filter_post_type = apply_filters( 'wp_custom_post_type_widgets_search_filter_post_type', $filter_post_type );

			if ( $filter_post_type && array_key_exists( $filter_post_type, $post_types ) ) {
				$query->set( 'post_type', $filter_post_type );
			}
		}
	}

	public function add_form_input_post_type( $form ) {
		$options = get_option( $this->option_name );
		$posttype = ! empty( $options[$this->number]['posttype'] ) ? $options[$this->number]['posttype'] : '';
		$insert = '<input type="hidden" name="post_type" value="' . $posttype . '">';

		$form = str_replace( '</form>', $insert . '</form>', $form );

		return $form;
	}

	public function widget( $args, $instance ) {
		$posttype = ! empty( $instance['posttype']) ? $instance['posttype'] : '';
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Search', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		add_filter( 'get_search_form', array( $this, 'add_form_input_post_type' ), 10, 1 );
		get_search_form();
		remove_filter( 'get_search_form', array( $this, 'add_form_input_post_type' ) );

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['posttype'] = strip_tags( $new_instance['posttype'] );
		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'posttype' => 'post' ) );
		$title = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		$posttype = isset( $instance['posttype'] ) ? $instance['posttype']: 'post';
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'custom-post-type-widgets' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

<?php
		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		printf(
			'<p><label for="%1$s">%2$s</label>' .
			'<select class="widefat" id="%1$s" name="%3$s">',
			$this->get_field_id( 'posttype' ),
			__( 'Post Type:', 'custom-post-type-widgets' ),
			$this->get_field_name( 'posttype' )
		);

		printf(
			'<option value="%s"%s>%s</option>',
			esc_attr( '' ),
			selected( '', $posttype, false ),
			__( 'All', 'custom-post-type-widgets' )
		);

		foreach ( $post_types as $post_type => $value ) {
			if ( 'attachment' === $post_type ) {
				continue;
			}

			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $post_type ),
				selected( $post_type, $posttype, false ),
				__( $value->label, 'custom-post-type-widgets' )
			);

		}
		echo '</select></p>';
	}
}
