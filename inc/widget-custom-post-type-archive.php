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

		$posttype     = ! empty( $instance['posttype'] ) ? $instance['posttype'] : 'post';
		$archive_type = ! empty( $instance['archive_type'] ) ? $instance['archive_type'] : 'monthly';
		$count        = ! empty( $instance['count'] ) ? (bool) $instance['count'] : false;
		$dropdown     = ! empty( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
		$order        = ! empty( $instance['order'] ) ? $instance['order'] : 'DESC';

		$disable_get_links = 0;
		if ( defined( 'CUSTOM_POST_TYPE_WIDGETS_DISABLE_LINKS_ARCHIVE' ) ) {
			if ( CUSTOM_POST_TYPE_WIDGETS_DISABLE_LINKS_ARCHIVE ) {
				$disable_get_links = 1;
			}
		}

		if ( ! $disable_get_links ) {
			add_filter( 'year_link', array( $this, 'get_year_link_custom_post_type' ), 10, 2 );
			add_filter( 'month_link', array( $this, 'get_month_link_custom_post_type' ), 10, 3 );
			add_filter( 'day_link', array( $this, 'get_day_link_custom_post_type' ), 10, 4 );
			add_filter( 'get_archives_link', array( $this, 'trim_post_type' ), 10, 1 );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['before_widget'];
		if ( $title ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( $dropdown ) {
			?>
			<label class="screen-reader-text"><?php echo $title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
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
				 * @param string $id Widget id.
				 * @param string $posttype Post type.
				 */
				$dropdown_args = apply_filters(
					'custom_post_type_widgets/archive/widget_archives_dropdown_args',
					array(
						'post_type'       => $posttype,
						'type'            => $archive_type,
						'format'          => 'option',
						'show_post_count' => $count,
						'order'           => $order,
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
						'type'            => $archive_type,
						'format'          => 'html',
						'show_post_count' => $count,
						'order'           => $order,
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

		if ( ! $disable_get_links ) {
			remove_filter( 'year_link', array( $this, 'get_year_link_custom_post_type' ) );
			remove_filter( 'month_link', array( $this, 'get_month_link_custom_post_type' ) );
			remove_filter( 'day_link', array( $this, 'get_day_link_custom_post_type' ) );
			remove_filter( 'get_archives_link', array( $this, 'trim_post_type' ) );
		}

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
		$instance['archive_type'] = wp_strip_all_tags( $new_instance['archive_type'] );
		$instance['count']    = $new_instance['count'] ? (bool) $new_instance['count'] : false;
		$instance['dropdown'] = $new_instance['dropdown'] ? (bool) $new_instance['dropdown'] : false;
		$instance['order']    = wp_strip_all_tags( $new_instance['order'] );

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
		$archive_type = isset( $instance['archive_type'] ) ? $instance['archive_type'] : 'monthly';
		$dropdown     = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
		$count        = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		$order        = isset( $instance['order'] ) ? $instance['order'] : 'DESC';
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Title:', 'custom-post-type-widgets' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

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

		printf(
			'<p><label for="%1$s">%2$s</label>' .
			'<select class="widefat" id="%1$s" name="%3$s">',
			/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
			$this->get_field_id( 'archive_type' ),
			/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
			__( 'Archive Type:', 'custom-post-type-widgets' ),
			/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
			$this->get_field_name( 'archive_type' )
		);

		$archive_types = array(
			'yearly'    => __( 'Yearly', 'custom-post-type-widgets' ),
			'monthly'   => __( 'Monthly', 'custom-post-type-widgets' ),
			'weekly'    => __( 'Weekly', 'custom-post-type-widgets' ),
			'daily'     => __( 'Daily', 'custom-post-type-widgets' ),
		);

		foreach ( $archive_types as $type => $label ) {
			// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $type ),
				selected( $type, $archive_type, false ),
				esc_html( $label )
			);
		}
		echo '</select></p>';
		?>

		<p><input class="checkbox" type="checkbox"<?php checked( $dropdown ); ?> id="<?php echo $this->get_field_id( 'dropdown' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="<?php echo $this->get_field_name( 'dropdown' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" /> <label for="<?php echo $this->get_field_id( 'dropdown' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Display as dropdown', 'custom-post-type-widgets' ); ?></label><br>
		<input class="checkbox" type="checkbox"<?php checked( $count ); ?> id="<?php echo $this->get_field_id( 'count' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" name="<?php echo $this->get_field_name( 'count' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" /> <label for="<?php echo $this->get_field_id( 'count' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php esc_html_e( 'Show post counts', 'custom-post-type-widgets' ); ?></label></p>
		<?php

		printf(
			'<p><label for="%1$s">%2$s</label>' .
			'<select class="widefat" id="%1$s" name="%3$s">',
			/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
			$this->get_field_id( 'order' ),
			/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
			__( 'Order:', 'custom-post-type-widgets' ),
			/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
			$this->get_field_name( 'order' )
		);

		$order_types = array(
			'DESC'  => __( 'DESC', 'custom-post-type-widgets' ),
			'ASC'   => __( 'ASC', 'custom-post-type-widgets' ),
		);

		foreach ( $order_types as $type => $label ) {
			// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $type ),
				selected( $type, $order, false ),
				esc_html( $label )
			);
		}
		echo '</select></p>';
	}

	/**
	 * Gets the year link for custom post type.
	 *
	 * Hooks to year_link
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param string $old_yearlink
	 * @param string $year
	 *
	 * @return string $new_yearlink
	 */
	public function get_year_link_custom_post_type( $old_yearlink, $year ) {
		$options  = get_option( $this->option_name );
		$posttype = ! empty( $options[ $this->number ]['posttype'] ) ? $options[ $this->number ]['posttype'] : 'post';

		if ( ! $year ) {
			$year = current_time( 'Y' );
		}

		global $wp_rewrite;
		$new_yearlink = $wp_rewrite->get_year_permastruct();

		if ( ! empty( $new_yearlink ) ) {
			$front = preg_replace( '/\/$/', '', $wp_rewrite->front );

			$new_yearlink = str_replace( '%year%', $year, $new_yearlink );

			if ( 'post' === $posttype ) {
				$new_yearlink = home_url( user_trailingslashit( $new_yearlink, 'year' ) );
			}
			else {
				$type_obj = get_post_type_object( $posttype );

				# The priority of the rewrite rule: has_archive < rewrite
				# See https://developer.wordpress.org/reference/functions/register_post_type/
				$archive_name = $posttype;
				if ( is_string( $type_obj->has_archive ) ) {
					$archive_name = $type_obj->has_archive;
				}
				if ( is_bool( $type_obj->rewrite ) && $type_obj->rewrite === true ) {
					$archive_name = $posttype;
				}
				else if ( is_array( $type_obj->rewrite ) ) {
					if ( ! empty( $type_obj->rewrite['slug'] ) ) {
						$archive_name = $type_obj->rewrite['slug'];
					}
				}

				if ( $front ) {
					$new_front = $type_obj->rewrite['with_front'] ? $front : '';
					$new_yearlink = str_replace( $front, $new_front . '/' . $archive_name, $new_yearlink );
					$new_yearlink = home_url( user_trailingslashit( $new_yearlink, 'month' ) );
				}
				else {
					$new_yearlink = home_url( user_trailingslashit( $archive_name . $new_yearlink, 'year' ) );
				}
			}
		}
		else {
			$new_yearlink = home_url( '?post_type=' . $posttype . '&m=' . $year );
		}

		/**
		 * Filter a yearlink.
		 *
		 * @since 1.4.0
		 *
		 * @param string $new_yearlink
		 * @param string $year
		 * @param string $old_yearlink
		 */
		return apply_filters( 'custom_post_type_widgets/archive/get_year_link_custom_post_type', $new_yearlink, $year, $old_yearlink );
	}

	/**
	 * Gets the day link for custom post type.
	 *
	 * Hooks to day_link
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param string $old_daylink
	 * @param string $year
	 * @param string $month
	 * @param string $day
	 *
	 * @return string $new_daylink
	 */
	public function get_day_link_custom_post_type( $old_daylink, $year, $month, $day ) {
		$options  = get_option( $this->option_name );
		$posttype = ! empty( $options[ $this->number ]['posttype'] ) ? $options[ $this->number ]['posttype'] : 'post';

		if ( ! $year ) {
			$year = current_time( 'Y' );
		}
		if ( ! $month ) {
			$month = current_time( 'm' );
		}
		if ( ! $day ) {
			$day = current_time( 'j' );
		}

		global $wp_rewrite;
		$new_daylink = $wp_rewrite->get_day_permastruct();

		if ( ! empty( $new_daylink ) ) {
			$front = preg_replace( '/\/$/', '', $wp_rewrite->front );

			$new_daylink = str_replace( '%year%', $year, $new_daylink );
			$new_daylink = str_replace( '%monthnum%', zeroise( intval( $month ), 2 ), $new_daylink );
			$new_daylink = str_replace( '%day%', zeroise( intval( $day ), 2 ), $new_daylink );

			if ( 'post' === $posttype ) {
				$new_daylink = home_url( user_trailingslashit( $new_daylink, 'day' ) );
			}
			else {
				$type_obj = get_post_type_object( $posttype );

				# The priority of the rewrite rule: has_archive < rewrite
				# See https://developer.wordpress.org/reference/functions/register_post_type/
				$archive_name = $posttype;
				if ( is_string( $type_obj->has_archive ) ) {
					$archive_name = $type_obj->has_archive;
				}
				if ( is_bool( $type_obj->rewrite ) && $type_obj->rewrite === true ) {
					$archive_name = $posttype;
				}
				else if ( is_array( $type_obj->rewrite ) ) {
					if ( ! empty( $type_obj->rewrite['slug'] ) ) {
						$archive_name = $type_obj->rewrite['slug'];
					}
				}

				if ( $front ) {
					$new_front = $type_obj->rewrite['with_front'] ? $front : '';
					$new_daylink   = str_replace( $front, $new_front . '/' . $archive_name, $new_daylink );
					$new_daylink   = home_url( user_trailingslashit( $new_daylink, 'day' ) );
				}
				else {
					$new_daylink = home_url( user_trailingslashit( $archive_name . $new_daylink, 'day' ) );
				}
			}
		}
		else {
			$new_daylink = home_url( '?post_type=' . $posttype . '&m=' . $year . zeroise( $month, 2 ) . zeroise( $day, 2 ) );
		}

		/**
		 * Filter a daylink.
		 *
		 * @since 1.4.0
		 *
		 * @param string $new_daylink
		 * @param string $year
		 * @param string $month
		 * @param string $day
		 * @param string $old_daylink
		 */
		return apply_filters( 'custom_post_type_widgets/archive/get_day_link_custom_post_type', $new_daylink, $year, $month, $day, $old_daylink );
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
	 * @param string $old_monthlink
	 * @param string $year
	 * @param string $month
	 *
	 * @return string $new_monthlink
	 */
	public function get_month_link_custom_post_type( $old_monthlink, $year, $month ) {
		$options  = get_option( $this->option_name );
		$posttype = ! empty( $options[ $this->number ]['posttype'] ) ? $options[ $this->number ]['posttype'] : 'post';

		if ( ! $year ) {
			$year = current_time( 'Y' );
		}
		if ( ! $month ) {
			$month = current_time( 'm' );
		}

		global $wp_rewrite;
		$new_monthlink = $wp_rewrite->get_month_permastruct();

		if ( ! empty( $new_monthlink ) ) {
			$front = preg_replace( '/\/$/', '', $wp_rewrite->front );

			$new_monthlink = str_replace( '%year%', $year, $new_monthlink );
			$new_monthlink = str_replace( '%monthnum%', zeroise( intval( $month ), 2 ), $new_monthlink );

			if ( 'post' === $posttype ) {
				$new_monthlink = home_url( user_trailingslashit( $new_monthlink, 'month' ) );
			}
			else {
				$type_obj = get_post_type_object( $posttype );

				# The priority of the rewrite rule: has_archive < rewrite
				# See https://developer.wordpress.org/reference/functions/register_post_type/
				$archive_name = $posttype;
				if ( is_string( $type_obj->has_archive ) ) {
					$archive_name = $type_obj->has_archive;
				}
				if ( is_bool( $type_obj->rewrite ) && $type_obj->rewrite === true ) {
					$archive_name = $posttype;
				}
				else if ( is_array( $type_obj->rewrite ) ) {
					if ( ! empty( $type_obj->rewrite['slug'] ) ) {
						$archive_name = $type_obj->rewrite['slug'];
					}
				}

				if ( $front ) {
					$new_front = $type_obj->rewrite['with_front'] ? $front : '';
					$new_monthlink = str_replace( $front, $new_front . '/' . $archive_name, $new_monthlink );
					$new_monthlink = home_url( user_trailingslashit( $new_monthlink, 'month' ) );
				}
				else {
					$new_monthlink = home_url( user_trailingslashit( $archive_name . $new_monthlink, 'month' ) );
				}
			}
		}
		else {
			$new_monthlink = home_url( '?post_type=' . $posttype . '&m=' . $year . zeroise( $month, 2 ) );
		}

		/**
		 * Filter a monthlink.
		 *
		 * @since 1.4.0
		 *
		 * @param string $new_monthlink
		 * @param string $year
		 * @param string $month
		 * @param string $old_monthlink
		 */

		return apply_filters( 'custom_post_type_widgets/archive/get_month_link_custom_post_type', $new_monthlink, $year, $month, $old_monthlink );
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
	 * @param string $old_link_html
	 *
	 * @return string $link_html
	 */
	public function trim_post_type( $old_link_html ) {
		global $wp_rewrite;

		if ( ! $wp_rewrite->permalink_structure ) {
			return $old_link_html;
		}

		$options  = get_option( $this->option_name );
		$posttype = ! empty( $options[ $this->number ]['posttype'] ) ? $options[ $this->number ]['posttype'] : '';

		$new_link_html = str_replace( '?post_type=' . $posttype, '', $old_link_html );

		/**
		 * Filter a trimed link_html.
		 *
		 * @since 1.4.0
		 *
		 * @param string $new_link_html  trimed link_html
		 * @param string $old_link_html  original link_html
		 * @param string $posttype
		 */
		return apply_filters( 'custom_post_type_widgets/archive/trim_post_type', $new_link_html, $old_link_html, $posttype );
	}
}
