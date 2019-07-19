<?php
/**
 * Custom Post Type Tag cloud widget class
 *
 * @since 1.0.0
 * @package Custom Post Type Widgets
 */

/**
 * Core class WP_Custom_Post_Type_Widgets_Tag_Cloud
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Tag_Cloud extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_tag_cloud',
			'description' => __( 'A cloud of your most used tags.', 'custom-post-type-widgets' ),
		);
		parent::__construct( 'custom-post-type-tag-cloud', __( 'Tag Cloud (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
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
		$title    = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Tags', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );
		$taxonomy = ! empty( $instance['taxonomy'] ) ? $instance['taxonomy'] : 'post_tag';

		$tag_cloud = wp_tag_cloud(
			apply_filters(
				'widget_tag_cloud_args',
				array(
					'taxonomy' => $taxonomy,
					'echo'     => false,
				)
			)
		);

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
		$instance['title']    = sanitize_text_field( $new_instance['title'] );
		$instance['taxonomy'] = stripslashes( $new_instance['taxonomy'] );
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
		$title    = isset( $instance['title'] ) ? wp_strip_all_tags( $instance['title'] ) : '';
		$taxonomy = isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : 'post_tag';
		$title_id = $this->get_field_id( 'title' );
?>
		<p><label for="<?php echo $title_id; ?>"><?php esc_html_e( 'Title:', 'custom-post-type-widgets' ); ?></label>
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
