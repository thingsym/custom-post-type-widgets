<?php
/**
 * Custom Post Type Tag cloud widget class
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Tag_Cloud extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_tag_cloud', 'description' => __( 'A cloud of your most used tags.', 'custom-post-type-widgets' ) );
		parent::__construct( 'custom-post-type-tag_cloud', __( 'Tag Cloud (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	public function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_custom_post_type_tag_cloud', 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Tags', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );
		$taxonomy = $instance['taxonomy'];

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo '<div class="tagcloud">';
		wp_tag_cloud( apply_filters( 'widget_tag_cloud_args', array( 'taxonomy' => $taxonomy ) ) );
		echo '</div>';
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['taxonomy'] = stripslashes( $new_instance['taxonomy'] );

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_custom_post_type_tag_cloud'] ) ) {
			delete_option( 'widget_custom_post_type_tag_cloud' );
		}

		return $instance;
	}

	public function flush_widget_cache() {
		wp_cache_delete( 'widget_custom_post_type_tag_cloud', 'widget' );
	}

	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		$taxonomy = isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : 'post_tag';
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'custom-post-type-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy:', 'custom-post-type-widgets' ); ?></label>
		<select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>">
		<?php
		$taxonomies = get_taxonomies( '', 'objects' );
		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxobjects => $value ) {
				if ( ! $value->show_tagcloud || empty( $value->labels->name ) ) {
					continue;
				}
				if ( $value->hierarchical ) {
					continue;
				}
				if ( 'nav_menu' == $taxobjects || 'link_category' == $taxobjects || 'post_format' == $taxobjects ) {
					continue;
				}
		?>
				<option value="<?php echo $taxobjects; ?>"<?php selected( $taxobjects, $taxonomy ); ?>><?php _e( $value->label, 'custom-post-type-widgets' ); ?> (<?php echo $taxobjects; ?>)</option>
			<?php } ?>
		</select></p>
		<?php }
	}
}
