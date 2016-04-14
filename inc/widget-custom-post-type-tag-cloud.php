<?php
/**
 * Custom Post Type Tag cloud widget class
 *
 * @since 1.0.0
 * @package Custom Post Type Widgets
 */

class WP_Custom_Post_Type_Widgets_Tag_Cloud extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_tag_cloud', 'description' => __( 'A cloud of your most used tags.', 'custom-post-type-widgets' ) );
		parent::__construct( 'custom-post-type-tag_cloud', __( 'Tag Cloud (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Tags', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );
		$taxonomy = $instance['taxonomy'];

		$tag_cloud = wp_tag_cloud( apply_filters( 'widget_tag_cloud_args', array(
			'taxonomy' => $taxonomy,
			'echo' => false,
		) ) );

		if ( empty( $tag_cloud ) ) {
			return;
		}

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo '<div class="tagcloud">';
		echo $tag_cloud;
		echo '</div>';
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['taxonomy'] = stripslashes( $new_instance['taxonomy'] );
		return $instance;
	}

	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		$taxonomy = isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : 'post_tag';
		$title_id = $this->get_field_id( 'title' );
?>
		<p><label for="<?php echo $title_id; ?>"><?php _e( 'Title:', 'custom-post-type-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $title_id; ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

<?php
		$taxonomies = get_taxonomies( '', 'objects' );
		if ( $taxonomies ) {
			printf(
				'<p><label for="%1$s">%2$s</label>' .
				'<select class="widefat" id="%1$s" name="%3$s">',
				$this->get_field_id( 'taxonomy' ),
				__( 'Taxonomy:', 'custom-post-type-widgets' ),
				$this->get_field_name( 'taxonomy' )
			);

			foreach ( $taxonomies as $taxobjects => $value ) {
				if ( ! $value->show_tagcloud || empty( $value->labels->name ) ) {
					continue;
				}
				if ( $value->hierarchical ) {
					continue;
				}
				if ( 'nav_menu' === $taxobjects || 'link_category' === $taxobjects || 'post_format' === $taxobjects ) {
					continue;
				}

				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $taxobjects ),
					selected( $taxobjects, $taxonomy, false ),
					__( $value->label, 'custom-post-type-widgets' ) . ' ' . $taxobjects
				);
			}
			echo '</select></p>';
		}
		else {
			echo '<p>' . __( 'The tag cloud will not be displayed since there are no taxonomies that support the tag cloud widget.', 'custom-post-type-widgets' ) . '</p>';
		}
	}
}
