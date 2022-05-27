<?php
/**
 * Custom Post Type Calendar widget class
 *
 * @since 1.0.0
 * @package Custom Post Type Widgets
 */

/**
 * Core class WP_Custom_Post_Type_Widgets_Calendar
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Calendar extends WP_Widget {
	/**
	 * Ensure that the ID attribute only appears in the markup once
	 *
	 * @since 4.4.0
	 *
	 * @static
	 * @access private
	 * @var int
	 */
	private static $instance = 0;

	/**
	 * Sets up a new widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_calendar',
			'description'                 => __( 'A calendar of your site&#8217;s Posts.', 'custom-post-type-widgets' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'custom-post-type-calendar', __( 'Calendar (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
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
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Calendar', 'custom-post-type-widgets' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$disable_get_links = 0;
		if ( defined( 'CUSTOM_POST_TYPE_WIDGETS_DISABLE_LINKS_CALENDAR' ) ) {
			if ( CUSTOM_POST_TYPE_WIDGETS_DISABLE_LINKS_CALENDAR ) {
				$disable_get_links = 1;
			}
		}

		if ( ! $disable_get_links ) {
			add_filter( 'month_link', array( $this, 'get_month_link_custom_post_type' ), 10, 3 );
			add_filter( 'day_link', array( $this, 'get_day_link_custom_post_type' ), 10, 4 );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['before_widget'];
		if ( $title ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if ( 0 === self::$instance ) {
			echo '<div class="calendar_wrap">';
		} else {
			echo '<div class="calendar_wrap">';
		}
		$this->get_custom_post_type_calendar();
		echo '</div>';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['after_widget'];

		if ( ! $disable_get_links ) {
			remove_filter( 'month_link', array( $this, 'get_month_link_custom_post_type' ) );
			remove_filter( 'day_link', array( $this, 'get_day_link_custom_post_type' ) );
		}

		self::$instance++;
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
		$posttype = isset( $instance['posttype'] ) ? $instance['posttype'] : 'post';
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
	}

	/**
	 * Extend the get_calendar for custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $initial
	 * @param boolean $echo
	 */
	public function get_custom_post_type_calendar( $initial = true, $echo = true ) {
		global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

		$options  = get_option( $this->option_name );
		$posttype = ! empty( $options[ $this->number ]['posttype'] ) ? $options[ $this->number ]['posttype'] : 'post';

		$key   = md5( $posttype . $m . $monthnum . $year );
		$cache = wp_cache_get( 'get_custom_post_type_calendar', 'calendar' );

		if ( $cache && is_array( $cache ) && isset( $cache[ $key ] ) ) {
			/**
			* Filters the HTML calendar output.
			*
			* @since 1.3.0
			*
			* @param string $calendar_output HTML output of the calendar.
			*/
			$output = apply_filters( 'custom_post_type_widgets/calendar/get_custom_post_type_calendar', $cache[ $key ] );

			if ( $echo ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo $output;
				return;
			}

			return $output;
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		// Quick check. If we have no posts at all, abort!
		if ( ! $posts ) {
			$gotsome = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT 1 as test FROM $wpdb->posts WHERE post_type = %s AND post_status = 'publish' LIMIT 1",
					array( $posttype )
				)
			);
			if ( ! $gotsome ) {
				$cache[ $key ] = '';
				wp_cache_set( 'get_custom_post_type_calendar', $cache, 'calendar' );
				return;
			}
		}

		if ( isset( $_GET['w'] ) ) {
			$w = (int) $_GET['w'];
		}

		// week_begins = 0 stands for Sunday.
		$week_begins = (int) get_option( 'start_of_week' );

		// Let's figure out when we are.
		if ( ! empty( $monthnum ) && ! empty( $year ) ) {
			$thismonth = zeroise( intval( $monthnum ), 2 );
			$thisyear  = (int) $year;
		}
		elseif ( ! empty( $w ) ) {
			// We need to get the month from MySQL.
			$thisyear = (int) substr( $m, 0, 4 );
			// it seems MySQL's weeks disagree with PHP's.
			$d         = ( ( $w - 1 ) * 7 ) + 6;
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$thismonth = $wpdb->get_var( "SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL $d DAY) ), '%m')" );
		}
		elseif ( ! empty( $m ) ) {
			$thisyear = (int) substr( $m, 0, 4 );
			if ( strlen( $m ) < 6 ) {
				$thismonth = '01';
			}
			else {
				$thismonth = zeroise( (int) substr( $m, 4, 2 ), 2 );
			}
		}
		else {
			$thisyear  = current_time( 'Y' );
			$thismonth = current_time( 'm' );
		}

		$unixmonth = mktime( 0, 0, 0, $thismonth, 1, $thisyear );
		$last_day  = date( 't', $unixmonth );

		// Get the next and previous month and year with at least one post.
		$previous = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
				FROM $wpdb->posts
				WHERE post_date < %s
				AND post_type = %s AND post_status = 'publish'
					ORDER BY post_date DESC
					LIMIT 1",
				array(
					"$thisyear-$thismonth-01",
					$posttype,
				)
			)
		);
		$next     = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
				FROM $wpdb->posts
				WHERE post_date > %s
				AND post_type = %s AND post_status = 'publish'
					ORDER BY post_date ASC
					LIMIT 1",
				array(
					"$thisyear-$thismonth-{$last_day} 23:59:59",
					$posttype,
				)
			)
		);

		/* translators: Calendar caption: 1: month name, 2: 4-digit year */
		$calendar_caption = _x( '%1$s %2$s', 'calendar caption', 'custom-post-type-widgets' );
		$calendar_output  = '<table class="wp-calendar wp-calendar-table">
		<caption>' . sprintf(
			$calendar_caption,
			$wp_locale->get_month( $thismonth ),
			date( 'Y', $unixmonth )
		) . '</caption>
		<thead>
		<tr>';

		$myweek = array();

		for ( $wdcount = 0; $wdcount <= 6; $wdcount++ ) {
			$myweek[] = $wp_locale->get_weekday( ( $wdcount + $week_begins ) % 7 );
		}

		foreach ( $myweek as $wd ) {
			$day_name         = $initial ? $wp_locale->get_weekday_initial( $wd ) : $wp_locale->get_weekday_abbrev( $wd );
			$wd               = esc_attr( $wd );
			$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
		}

		$calendar_output .= '
		</tr>
		</thead>
		<tbody>
		<tr>';

		$daywithpost = array();

		// Get days with posts.
		$dayswithposts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT DAYOFMONTH(post_date)
				FROM $wpdb->posts WHERE post_date >= %s
				AND post_type = %s AND post_status = 'publish'
				AND post_date <= %s",
				array(
					"{$thisyear}-{$thismonth}-01 00:00:00",
					$posttype,
					"{$thisyear}-{$thismonth}-{$last_day} 23:59:59",
				)
			),
			ARRAY_N
		);
		if ( $dayswithposts ) {
			foreach ( (array) $dayswithposts as $daywith ) {
				$daywithpost[] = $daywith[0];
			}
		}

		// See how much we should pad in the beginning.
		/* @phpstan-ignore-next-line */
		$pad = calendar_week_mod( date( 'w', $unixmonth ) - $week_begins );
		if ( 0 != $pad ) {
			/* @phpstan-ignore-next-line */
			$calendar_output .= "\n\t\t" . '<td colspan="' . esc_attr( $pad ) . '" class="pad">&nbsp;</td>';
		}

		$newrow      = false;
		$daysinmonth = (int) date( 't', $unixmonth );

		for ( $day = 1; $day <= $daysinmonth; ++$day ) {
			if ( $newrow ) {
				$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
			}
			$newrow = false;

			if ( current_time( 'j' ) == $day &&
				current_time( 'm' ) == $thismonth &&
				current_time( 'Y' ) == $thisyear ) {
				$calendar_output .= '<td class="today">';
			}
			else {
				$calendar_output .= '<td>';
			}

			if ( in_array( $day, $daywithpost ) ) {
				// any posts today?
				$date_format      = date( _x( 'F j, Y', 'daily archives date format', 'custom-post-type-widgets' ), strtotime( "{$thisyear}-{$thismonth}-{$day}" ) );
				/* translators: label: 1: date format */
				$label            = sprintf( __( 'Posts published on %s', 'custom-post-type-widgets' ), $date_format );
				$calendar_output .= sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					get_day_link( $thisyear, $thismonth, $day ),
					esc_attr( $label ),
					$day
				);
			}
			else {
				$calendar_output .= $day;
			}
			$calendar_output .= '</td>';

			/* @phpstan-ignore-next-line */
			if ( 6 == calendar_week_mod( date( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins ) ) {
				$newrow = true;
			}
		}

		/* @phpstan-ignore-next-line */
		$pad = 7 - calendar_week_mod( date( 'w', mktime( 0, 0, 0, $thismonth, $day, $thisyear ) ) - $week_begins );
		if ( 0 != $pad && 7 != $pad ) {
			/* @phpstan-ignore-next-line */
			$calendar_output .= "\n\t\t" . '<td class="pad" colspan="' . esc_attr( $pad ) . '">&nbsp;</td>';
		}

		$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

		$calendar_output .= '<nav aria-label="' . __( 'Previous and next months', 'custom-post-type-widgets' ) . '" class="wp-calendar-nav">';

		if ( $previous ) {
			$calendar_output .= "\n\t\t" . '<span class="wp-calendar-nav-prev"><a href="' . get_month_link( $previous->year, $previous->month ) . '">&laquo; ' .
				$wp_locale->get_month_abbrev( $wp_locale->get_month( $previous->month ) ) .
			'</a></span>';
		} else {
			$calendar_output .= "\n\t\t" . '<span class="wp-calendar-nav-prev">&nbsp;</span>';
		}

		$calendar_output .= "\n\t\t" . '<span class="pad">&nbsp;</span>';

		if ( $next ) {
			$calendar_output .= "\n\t\t" . '<span class="wp-calendar-nav-next"><a href="' . get_month_link( $next->year, $next->month ) . '">' .
				$wp_locale->get_month_abbrev( $wp_locale->get_month( $next->month ) ) .
			' &raquo;</a></span>';
		} else {
			$calendar_output .= "\n\t\t" . '<span class="wp-calendar-nav-next">&nbsp;</span>';
		}

		$calendar_output .= '
		</nav>';

		$cache[ $key ] = $calendar_output;
		wp_cache_set( 'get_custom_post_type_calendar', $cache, 'calendar' );

		$output = apply_filters( 'custom_post_type_widgets/calendar/get_custom_post_type_calendar', $calendar_output );

		if ( $echo ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $calendar_output;
			return;
		}
		else {
			return $calendar_output;
		}
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
	 * @param string $daylink
	 * @param string $year
	 * @param string $month
	 * @param string $day
	 *
	 * @return string $daylink
	 */
	public function get_day_link_custom_post_type( $daylink, $year, $month, $day ) {
		global $wp_rewrite;

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

		$daylink = $wp_rewrite->get_day_permastruct();

		if ( ! empty( $daylink ) ) {
			$front = preg_replace( '/\/$/', '', $wp_rewrite->front );

			$daylink = str_replace( '%year%', $year, $daylink );
			$daylink = str_replace( '%monthnum%', zeroise( intval( $month ), 2 ), $daylink );
			$daylink = str_replace( '%day%', zeroise( intval( $day ), 2 ), $daylink );

			if ( 'post' === $posttype ) {
				$daylink = home_url( user_trailingslashit( $daylink, 'day' ) );
			}
			else {
				$type_obj     = get_post_type_object( $posttype );

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
					$daylink   = str_replace( $front, $new_front . '/' . $archive_name, $daylink );
					$daylink   = home_url( user_trailingslashit( $daylink, 'day' ) );
				}
				else {
					$daylink = home_url( user_trailingslashit( $archive_name . $daylink, 'day' ) );
				}
			}
		}
		else {
			$daylink = home_url( '?post_type=' . $posttype . '&m=' . $year . zeroise( $month, 2 ) . zeroise( $day, 2 ) );
		}

		/**
		 * Filter a daylink.
		 *
		 * @since 1.4.0
		 *
		 * @param string $daylink
		 * @param string $year
		 * @param string $month
		 * @param string $day
		 */
		return apply_filters( 'custom_post_type_widgets/calendar/get_day_link_custom_post_type', $daylink, $year, $month, $day );
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
		$options  = get_option( $this->option_name );
		$posttype = ! empty( $options[ $this->number ]['posttype'] ) ? $options[ $this->number ]['posttype'] : 'post';

		if ( ! $year ) {
			$year = current_time( 'Y' );
		}
		if ( ! $month ) {
			$month = current_time( 'm' );
		}

		global $wp_rewrite;
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

		/**
		 * Filter a monthlink.
		 *
		 * @since 1.4.0
		 *
		 * @param string $monthlink
		 * @param string $year
		 * @param string $month
		 */
		return apply_filters( 'custom_post_type_widgets/calendar/get_month_link_custom_post_type', $monthlink, $year, $month );
	}

}
