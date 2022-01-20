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
			'classname'                   => 'widget_tag_cloud',
			'description'                 => __( 'A cloud of your most used tags.', 'custom-post-type-widgets' ),
			'customize_selective_refresh' => true,
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
		$taxonomy   = $this->get_taxonomy( $instance );
		$show_count = ! empty( $instance['count'] );

		if ( ! empty( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			if ( 'post_tag' === $taxonomy ) {
				$title = __( 'Tags', 'custom-post-type-widgets' );
			} else {
				$tax   = get_taxonomy( $taxonomy );
				$title = $tax->labels->name;
			}
		}

		/**
		 * Filters the taxonomy used in the Tag Cloud widget.
		 *
		 * Filter hook: custom_post_type_widgets/tag_cloud/widget_tag_cloud_args
		 *
		 * @since 2.8.0
		 * @since 3.0.0 Added taxonomy drop-down.
		 * @since 4.9.0 Added the `$instance` parameter.
		 *
		 * @see wp_tag_cloud()
		 *
		 * @param array  $args     Args used for the tag cloud widget.
		 * @param array  $instance Array of settings for the current widget.
		 * @param string $id Widget id.
		 * @param string $taxonomy Taxonomy.
		 */
		$tag_cloud = wp_tag_cloud(
			apply_filters(
				'custom_post_type_widgets/tag_cloud/widget_tag_cloud_args',
				array(
					'taxonomy'   => $taxonomy,
					'echo'       => false,
					'show_count' => $show_count,
				),
				$instance,
				$this->id,
				$taxonomy
			)
		);

		if ( empty( $tag_cloud ) ) {
			return;
		}

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['before_widget'];
		if ( $title ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo '<div class="tagcloud">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $tag_cloud;
		echo '</div>';
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
		$instance['taxonomy'] = stripslashes( $new_instance['taxonomy'] );
		$instance['count']    = ! empty( $new_instance['count'] ) ? (bool) $new_instance['count'] : false;

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
		$title    = isset( $instance['title'] ) ? $instance['title'] : '';
		$taxonomy = isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : 'post_tag';
		$count    = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Title:', 'custom-post-type-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="<?php echo $this->get_field_name( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<?php
		$taxonomies = get_taxonomies( array( 'show_tagcloud' => true ), 'objects' );
		if ( $taxonomies ) {
			printf(
				'<p><label for="%1$s">%2$s</label>' .
				'<select class="widefat" id="%1$s" name="%3$s">',
				/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
				$this->get_field_id( 'taxonomy' ),
				/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
				__( 'Taxonomy (slug):', 'custom-post-type-widgets' ),
				/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
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
					esc_html__( $value->label, 'custom-post-type-widgets' ) . ' ' . esc_html( $taxobjects )
				);
			}
			echo '</select></p>';
			?>
			<p><input class="checkbox" type="checkbox" <?php checked( $count ); ?> id="<?php echo $this->get_field_id( 'count' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="<?php echo $this->get_field_name( 'count' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" />
			<label for="<?php echo $this->get_field_id( 'count' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Show tag counts', 'custom-post-type-widgets' ); ?></label></p>
			<?php
		}
		else {
			echo '<p>' . esc_html__( 'The tag cloud will not be displayed since there are no taxonomies that support the tag cloud widget.', 'custom-post-type-widgets' ) . '</p>';
		}
	}

	/**
	 * Retrieves the taxonomy for the current Tag cloud widget instance.
	 *
	 * @since 4.4.0
	 *
	 * @param array $instance Current settings.
	 * @return string Name of the current taxonomy if set, otherwise 'post_tag'.
	 */
	public function get_taxonomy( $instance ) {
		if ( ! empty( $instance['taxonomy'] ) && taxonomy_exists( $instance['taxonomy'] ) ) {
			return $instance['taxonomy'];
		}

		return 'post_tag';
	}
}
