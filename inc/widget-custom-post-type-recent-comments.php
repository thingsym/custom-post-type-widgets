<?php
/**
 * Custom Post Type Recent Comments widget class
 *
 * @since 1.0.0
 * @package Custom Post Type Widgets
 */

/**
 * Core class WP_Custom_Post_Type_Widgets_Recent_Comments
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Recent_Comments extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_recent_comments',
			'description'                 => __( 'Your site’s most recent comments.', 'custom-post-type-widgets' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'custom-post-type-recent-comments', __( 'Recent Comments (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
		$this->alt_option_name = 'widget_custom_post_type_recent_comments';

		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action( 'wp_head', array( $this, 'recent_comments_style' ) );
		}
	}

	/**
	 * Outputs comments style.
	 *
	 * Hooks to wp_head
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function recent_comments_style() {
		/**
		 * Filters the Recent Comments default widget styles.
		 *
		 * @since 3.1.0
		 *
		 * @param bool   $active  Whether the widget is active. Default true.
		 * @param string $id_base The widget ID.
		 */
		if ( ! current_theme_supports( 'widgets' ) // Temp hack #14876.
			|| ! apply_filters( 'show_recent_comments_widget_style', true, $this->id_base ) ) {
			return;
		}
		?>
		<style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>
		<?php
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

		$output = '';

		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Comments', 'custom-post-type-widgets' );

		$posttype = ! empty( $instance['posttype'] ) ? $instance['posttype'] : '';
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}

		/**
		 * Filters the arguments for the Recent Comments widget.
		 *
		 * Filter hook: custom_post_type_widgets/recent_comments/widget_comments_args
		 *
		 * @since 3.4.0
		 * @since 4.9.0 Added the `$instance` parameter.
		 *
		 * @see WP_Comment_Query::query() for information on accepted arguments.
		 *
		 * @param array  $comment_args An array of arguments used to retrieve the recent comments.
		 * @param array  $instance     Array of settings for the current widget.
		 * @param string $id     Widget id.
		 * @param string $posttype     Post type.
		 */
		$comments = get_comments(
			apply_filters(
				'custom_post_type_widgets/recent_comments/widget_comments_args',
				array(
					'post_type'   => $posttype,
					'number'      => $number,
					'status'      => 'approve',
					'post_status' => 'publish',
				),
				$instance,
				$this->id,
				$posttype
			)
		);

		$output .= '<ul id="recentcomments">';
		if ( is_array( $comments ) && $comments ) {
			// Prime cache for associated posts. Prime post term cache if we need it for permalinks.
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

			foreach ( (array) $comments as $comment ) {
				$output .= '<li class="recentcomments">';
				$output .= sprintf(
					/* translators: comments widget: 1: comment author, 2: post link */
					_x( '%1$s on %2$s', 'widgets', 'custom-post-type-widgets' ),
					'<span class="comment-author-link">' . get_comment_author_link( $comment ) . '</span>',
					'<a href="' . esc_url( get_comment_link( $comment ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a>'
				);
				$output .= '</li>';
			}
		}
		$output .= '</ul>';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['before_widget'];
		if ( $title ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $args['before_title'] . $title . $args['after_title'];
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $output;
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
		$instance['title']    = empty( $new_instance['title'] ) ? '' : sanitize_text_field( $new_instance['title'] );
		$instance['posttype'] = wp_strip_all_tags( $new_instance['posttype'] );
		$instance['number']   = absint( $new_instance['number'] );

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
		$posttype = isset( $instance['posttype'] ) ? $instance['posttype'] : '';
		$number   = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Title:', 'custom-post-type-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="<?php echo $this->get_field_name( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

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
		?>

		<p><label for="<?php echo $this->get_field_id( 'number' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Number of comments to show:', 'custom-post-type-widgets' ); ?></label>
		<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="<?php echo $this->get_field_name( 'number' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $number ); /* @phpstan-ignore-line */ ?>" size="3" /></p>
		<?php
	}
}
