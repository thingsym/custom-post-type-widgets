<?php
/**
 * Custom Post Type Search widget class
 *
 * @since 1.0.3
 */
class WP_Custom_Post_Type_Widgets_Search extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_search', 'description' => __( 'Search widget for custom post types.', 'custom-post-type-widgets' ) );
		parent::__construct( 'custom-post-type-search', __( 'Search (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
		$this->alt_option_name = 'widget_custom_post_type_search';
	}

	public function widget( $args, $instance ) {
		$posttype = $instance['posttype'];
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Search', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		?>

		<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
			<label class="screen-reader-text" for="s"><?php _e( 'Search for:', 'custom-post-type-widgets' ); ?></label>
			<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Type and search', 'placeholder', 'custom-post-type-widgets' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'custom-post-type-widgets' ); ?>" />
			<input type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'custom-post-type-widgets' ); ?>" />
			<input type="hidden" name="post_type" value="<?php echo $posttype; ?>" />
		</form>

		<?php
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['posttype'] = strip_tags( $new_instance['posttype'] );
		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'posttype' => 'post' ) );
		$title = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		$posttype = isset( $instance['posttype'] ) ? $instance['posttype']: 'post';
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'custom-post-type-widgets' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'posttype' ); ?>"><?php _e( 'Post Type:', 'custom-post-type-widgets' ); ?></label>
		<select name="<?php echo $this->get_field_name( 'posttype' ); ?>" id="<?php echo $this->get_field_id( 'posttype' ); ?>">
		<?php
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			foreach ( $post_types as $post_type => $value ) {
				if ( 'attachment' == $post_type ) {
					continue;
				}
			?>
				<option value="<?php echo esc_attr( $post_type ); ?>"<?php selected( $post_type, $posttype ); ?>><?php _e( $value->label, 'custom-post-type-widgets' ); ?></option>
		<?php } ?>
		</select>
		</p>
<?php
	}
}
