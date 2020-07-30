<?php
/**
 * Custom Post Type Archives widget class
 *
 * @since 1.0.0
 * @package Custom Post Type Widgets
 */

/**
 * Core class WP_Custom_Post_Type_Widgets_Archives
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Archives extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_archive',
			'description'                 => __( 'A monthly archive of your site&#8217;s Posts.', 'custom-post-type-widgets' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'custom-post-type-archives', __( 'Archives (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
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
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Archives', 'custom-post-type-widgets' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$posttype = ! empty( $instance['posttype'] ) ? $instance['posttype'] : 'post';
		$c        = ! empty( $instance['count'] ) ? (bool) $instance['count'] : false;
		$d        = ! empty( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;

		add_filter( 'month_link', array( $this, 'get_month_link_custom_post_type' ), 10, 3 );
		add_filter( 'get_archives_link', array( $this, 'trim_post_type' ), 10, 1 );

		echo $args['before_widget']; // WPCS: XSS ok.
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title']; // WPCS: XSS ok.
		}

		if ( $d ) {
			?>
			<label class="screen-reader-text"><?php echo $title; // WPCS: XSS ok. ?></label>
			<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'>
				<?php
				/**
				 * Filters the arguments for the Archives widget drop-down.
				 *
				 * Filter hook: custom_post_type_widgets/archive/widget_archives_dropdown_args
				 *
				 * @since 2.8.0
				 * @since 4.9.0 Added the `$instance` parameter.
				 *
				 * @see wp_get_archives()
				 *
				 * @param array  $args     An array of Archives widget drop-down arguments.
				 * @param array  $instance Settings for the current Archives widget instance.
				 * @param string $this->id Widget id.
				 * @param string $posttype Post type.
				 */
				$dropdown_args = apply_filters(
					'custom_post_type_widgets/archive/widget_archives_dropdown_args',
					array(
						'post_type'       => $posttype,
						'type'            => 'monthly',
						'format'          => 'option',
						'show_post_count' => $c,
					),
					$instance,
					$this->id,
					$posttype
				);

				switch ( $dropdown_args['type'] ) {
					case 'yearly':
						$label = __( 'Select Year', 'custom-post-type-widgets' );
						break;
					case 'monthly':
						$label = __( 'Select Month', 'custom-post-type-widgets' );
						break;
					case 'daily':
						$label = __( 'Select Day', 'custom-post-type-widgets' );
						break;
					case 'weekly':
						$label = __( 'Select Week', 'custom-post-type-widgets' );
						break;
					default:
						$label = __( 'Select Post', 'custom-post-type-widgets' );
						break;
				}
				?>

				<option value=""><?php echo esc_attr( $label ); ?></option>
				<?php wp_get_archives( $dropdown_args ); ?>
			</select>

		<?php } else { ?>
			<ul>
			<?php
			/**
			 * Filters the arguments for the Archives widget.
			 *
			 * Filter hook: custom_post_type_widgets/archive/widget_archives_args
			 *
			 * @since 2.8.0
			 * @since 4.9.0 Added the `$instance` parameter.
			 *
			 * @see wp_get_archives()
			 *
			 * @param array  $args     An array of Archives option arguments.
			 * @param array  $instance Array of settings for the current widget.
			 * @param string $this->id Widget id.
			 * @param string $posttype Post type.
			 */
			wp_get_archives(
				apply_filters(
					'custom_post_type_widgets/archive/widget_archives_args',
					array(
						'post_type'       => $posttype,
						'type'            => 'monthly',
						'show_post_count' => $c,
					),
					$instance,
					$this->id,
					$posttype
				)
			);
			?>
		</ul>
			<?php
		}

		remove_filter( 'month_link', array( $this, 'get_month_link_custom_post_type' ) );
		remove_filter( 'get_archives_link', array( $this, 'trim_post_type' ) );

		echo $args['after_widget']; // WPCS: XSS ok.
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
		$instance['count']    = $new_instance['count'] ? (bool) $new_instance['count'] : false;
		$instance['dropdown'] = $new_instance['dropdown'] ? (bool) $new_instance['dropdown'] : false;

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
		$title        = isset( $instance['title'] ) ? $instance['title'] : '';
		$posttype     = isset( $instance['posttype'] ) ? $instance['posttype'] : 'post';
		$dropdown     = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
		$count        = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); // WPCS: XSS ok. ?>"><?php esc_html_e( 'Title:', 'custom-post-type-widgets' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); // WPCS: XSS ok. ?>" name="<?php echo $this->get_field_name( 'title' ); // WPCS: XSS ok. ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<?php
		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		printf(
			'<p><label for="%1$s">%2$s</label>' .
			'<select class="widefat" id="%1$s" name="%3$s">',
			$this->get_field_id( 'posttype' ),
			__( 'Post Type:', 'custom-post-type-widgets' ),
			$this->get_field_name( 'posttype' )
		); // WPCS: XSS ok.

		foreach ( $post_types as $post_type => $value ) {
			if ( 'attachment' === $post_type || 'page' === $post_type ) {
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

		<p><input class="checkbox" type="checkbox"<?php checked( $dropdown ); ?> id="<?php echo $this->get_field_id( 'dropdown' ); // WPCS: XSS ok. ?>" name="<?php echo $this->get_field_name( 'dropdown' ); // WPCS: XSS ok. ?>" /> <label for="<?php echo $this->get_field_id( 'dropdown' ); // WPCS: XSS ok. ?>"><?php esc_html_e( 'Display as dropdown', 'custom-post-type-widgets' ); ?></label><br>
		<input class="checkbox" type="checkbox"<?php checked( $count ); ?> id="<?php echo $this->get_field_id( 'count' ); // WPCS: XSS ok. ?>" name="<?php echo $this->get_field_name( 'count' ); // WPCS: XSS ok. ?>" /> <label for="<?php echo $this->get_field_id( 'count' ); // WPCS: XSS ok. ?>"><?php esc_html_e( 'Show post counts', 'custom-post-type-widgets' ); ?></label></p>
		<?php
	}

	/**
	 * Gets the month link for custom post type.
	 *
	 * Hooks to month_link
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param string $monthlink
	 * @param string $year
	 * @param string $month
	 *
	 * @return string $monthlink
	 */
	public function get_month_link_custom_post_type( $monthlink, $year, $month ) {
		global $wp_rewrite;

		$options  = get_option( $this->option_name );
		$posttype = ! empty( $options[ $this->number ]['posttype'] ) ? $options[ $this->number ]['posttype'] : 'post';

		if ( ! $year ) {
			$year = current_time( 'Y' );
		}
		if ( ! $month ) {
			$month = current_time( 'm' );
		}

		$monthlink = $wp_rewrite->get_month_permastruct();

		if ( ! empty( $monthlink ) ) {
			$front = preg_replace( '/\/$/', '', $wp_rewrite->front );

			$monthlink = str_replace( '%year%', $year, $monthlink );
			$monthlink = str_replace( '%monthnum%', zeroise( intval( $month ), 2 ), $monthlink );

			if ( 'post' === $posttype ) {
				$monthlink = home_url( user_trailingslashit( $monthlink, 'month' ) );
			}
			else {
				$type_obj     = get_post_type_object( $posttype );
				$archive_name = ! empty( $type_obj->rewrite['slug'] ) ? $type_obj->rewrite['slug'] : $posttype;
				if ( $front ) {
					$new_front = $type_obj->rewrite['with_front'] ? $front : '';
					$monthlink = str_replace( $front, $new_front . '/' . $archive_name, $monthlink );
					$monthlink = home_url( user_trailingslashit( $monthlink, 'month' ) );
				}
				else {
					$monthlink = home_url( user_trailingslashit( $archive_name . $monthlink, 'month' ) );
				}
			}
		}
		else {
			$monthlink = home_url( '?post_type=' . $posttype . '&m=' . $year . zeroise( $month, 2 ) );
		}

		return $monthlink;
	}

	/**
	 * Trim the post_type url query from get_archives_link.
	 *
	 * Hooks to get_archives_link
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param string $link_html
	 *
	 * @return string $link_html
	 */
	public function trim_post_type( $link_html ) {
		global $wp_rewrite;

		if ( ! $wp_rewrite->permalink_structure ) {
			return $link_html;
		}

		$options  = get_option( $this->option_name );
		$posttype = ! empty( $options[ $this->number ]['posttype'] ) ? $options[ $this->number ]['posttype'] : '';

		$link_html = str_replace( '?post_type=' . $posttype, '', $link_html );

		return $link_html;
	}
}
