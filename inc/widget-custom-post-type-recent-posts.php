<?php
/**
 * Custom Post Type Recent Posts widget class
 *
 * @since 1.0.0
 * @package Custom Post Type Widgets
 */

/**
 * Core class WP_Custom_Post_Type_Widgets_Recent_Posts
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Recent_Posts extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_recent_entries',
			'description'                 => __( 'Your site&#8217;s most recent custom Posts.', 'custom-post-type-widgets' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'custom-post-type-recent-posts', __( 'Recent Posts (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
		$this->alt_option_name = 'widget_custom_post_type_recent_posts';
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
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Posts', 'custom-post-type-widgets' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$posttype = ! empty( $instance['posttype'] ) ? $instance['posttype'] : 'post';
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}
		$show_date = ! empty( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;

		$post_types          = get_post_types( array( 'public' => true ), 'objects' );
		$post_types['any'] = array();

		if ( array_key_exists( $posttype, (array) $post_types ) ) {
			/**
			 * Filters the arguments for the Recent Posts widget.
			 *
			 * Filter hook: custom_post_type_widgets/recent_posts/widget_posts_args
			 *
			 * @since 3.4.0
			 * @since 4.9.0 Added the `$instance` parameter.
			 *
			 * @see WP_Query::get_posts()
			 *
			 * @param array  $args     An array of arguments used to retrieve the recent posts.
			 * @param array  $instance Array of settings for the current widget.
			 * @param string $id Widget id.
			 * @param string $posttype Post type.
			 */
			$r = new WP_Query(
				apply_filters(
					'custom_post_type_widgets/recent_posts/widget_posts_args',
					array(
						'post_type'           => $posttype,
						'posts_per_page'      => $number,
						'no_found_rows'       => true,
						'post_status'         => 'publish',
						'ignore_sticky_posts' => true,
					),
					$instance,
					$this->id,
					$posttype
				)
			);

			if ( ! $r->have_posts() ) {
				return;
			}
			?>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $args['before_widget'];

			if ( $title ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $args['before_title'] . $title . $args['after_title'];
			}

			/**
			 * Actions the arguments for the Recent Posts widget.
			 *
			 * Action hook: custom_post_type_widgets/recent_posts/widget/before
			 *
			 * @since 1.2.0
			 *
			 * @param string $this->id Widget id.
			 * @param string $posttype Post type.
			 * @param array  $instance Array of settings for the current widget.
			 */
			do_action(
				'custom_post_type_widgets/recent_posts/widget/before',
				$this->id,
				$posttype,
				$instance
			);
			?>
			<ul>
				<?php foreach ( $r->posts as $recent_post ) : ?>
					<?php
					$post_title = get_the_title( $recent_post->ID );
					$title      = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)', 'custom-post-type-widgets' );
					?>
				<li>
					<?php
					/**
					 * Actions the arguments for the Recent Posts widget.
					 *
					 * Action hook: custom_post_type_widgets/recent_posts/widget/prepend
					 *
					 * @since 1.2.0
					 *
					 * @param string $this->id    Widget id.
					 * @param string $posttype    Post type.
					 * @param array  $instance    Array of settings for the current widget.
					 * @param array  $recent_post Array of Post for the recent post
					 */
					do_action(
						'custom_post_type_widgets/recent_posts/widget/prepend',
						$this->id,
						$posttype,
						$instance,
						$recent_post
					);
					?>
					<a href="<?php the_permalink( $recent_post->ID ); ?>"><?php echo esc_html( $title ); ?></a>
					<?php if ( $show_date ) : ?>
						<span class="post-date"><?php echo get_the_date( '', $recent_post->ID ); ?></span>
					<?php endif; ?>
					<?php
					/**
					 * Actions the arguments for the Recent Posts widget.
					 *
					 * Action hook: custom_post_type_widgets/recent_posts/widget/append
					 *
					 * @since 1.2.0
					 *
					 * @param string $this->id    Widget id.
					 * @param string $posttype    Post type.
					 * @param array  $instance    Array of settings for the current widget.
					 * @param array  $recent_post Array of Post for the recent post
					 */
					do_action(
						'custom_post_type_widgets/recent_posts/widget/append',
						$this->id,
						$posttype,
						$instance,
						$recent_post
					);
					?>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php
			/**
			 * Actions the arguments for the Recent Posts widget.
			 *
			 * Action hook: custom_post_type_widgets/recent_posts/widget/after
			 *
			 * @since 1.2.0
			 *
			 * @param string $this->id Widget id.
			 * @param string $posttype Post type.
			 * @param array  $instance Array of settings for the current widget.
			 */
			do_action(
				'custom_post_type_widgets/recent_posts/widget/after',
				$this->id,
				$posttype,
				$instance
			);

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $args['after_widget'];
		}
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
		$instance              = $old_instance;
		$instance['title']     = empty( $new_instance['title'] ) ? '' : sanitize_text_field( $new_instance['title'] );
		$instance['posttype']  = wp_strip_all_tags( $new_instance['posttype'] );
		$instance['number']    = absint( $new_instance['number'] );
		$instance['show_date'] = ! empty( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;

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
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = ! empty( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Title:', 'custom-post-type-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="<?php echo $this->get_field_name( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<?php
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

		$post_types = get_post_types( array( 'public' => true ), 'objects' );

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
		?>

		<p><label for="<?php echo $this->get_field_id( 'number' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Number of posts to show:', 'custom-post-type-widgets' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="<?php echo $this->get_field_name( 'number' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" type="text" value="<?php echo esc_attr( $number ); /* @phpstan-ignore-line */ ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="<?php echo $this->get_field_name( 'show_date' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Display post date?', 'custom-post-type-widgets' ); ?></label></p>
		<?php
	}
}
