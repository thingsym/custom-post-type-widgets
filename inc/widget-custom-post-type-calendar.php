<?php
/**
 * Custom Post Type Calendar widget class
 *
 * @since 1.0.0
 * @package Custom Post Type Widgets
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

	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_calendar', 'description' => __( 'A calendar of your site&#8217;s Posts.', 'custom-post-type-widgets' ) );
		parent::__construct( 'custom-post-type-calendar', __( 'Calendar (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Calendar', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );
		$posttype = $instance['posttype'];

		add_filter( 'get_calendar', array( $this, 'get_custom_post_type_calendar' ), 10, 3 );
		add_filter( 'month_link', array( $this, 'get_month_link_custom_post_type' ), 10, 3 );
		add_filter( 'day_link', array( $this, 'get_day_link_custom_post_type' ), 10, 4 );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if ( 0 === self::$instance ) {
			echo '<div id="calendar_wrap" class="calendar_wrap">';
		} else {
			echo '<div class="calendar_wrap">';
		}
		get_calendar();
		echo '</div>';
		echo $args['after_widget'];

		self::$instance++;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['posttype'] = strip_tags( $new_instance['posttype'] );
		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = isset( $instance['title'] ) ? sanitize_text_field( $instance['title'] ) : '';
		$posttype = isset( $instance['posttype'] ) ? $instance['posttype']: 'post';
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'custom-post-type-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

<?php
		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		printf(
			'<p><label for="%1$s">%2$s</label>' .
			'<select class="widefat" id="%1$s" name="%3$s">',
			$this->get_field_id( 'posttype' ),
			__( 'Post Type:', 'custom-post-type-widgets' ),
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
				__( $value->label, 'custom-post-type-widgets' )
			);

		}
		echo '</select></p>';
	}

	/**
	 * function that extend the get_calendar
	 * @see wp-includes/general-template.php
	 *
	 * @since 1.0.0
	 *
	 * @param string $calendar_output
	 * @param boolean $initial
	 * @param boolean $echo
	 */
	public function get_custom_post_type_calendar( $calendar_output, $initial = true, $echo = true ) {
		global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

		$options = get_option( $this->option_name );
		$posttype = $options[$this->number]['posttype'];

		// Quick check. If we have no posts at all, abort!
		if ( ! $posts ) {
			$gotsome = $wpdb->get_var( "SELECT 1 as test FROM $wpdb->posts WHERE post_type = '$posttype' AND post_status = 'publish' LIMIT 1" );
		}

		if ( isset( $_GET['w'] ) ) {
			$w = (int) $_GET['w'];
		}

		// week_begins = 0 stands for Sunday
		$week_begins = (int) get_option( 'start_of_week' );
		$ts = current_time( 'timestamp' );

		// Let's figure out when we are
		if ( ! empty( $monthnum ) && ! empty( $year ) ) {
			$thismonth = zeroise( intval( $monthnum ), 2 );
			$thisyear = (int) $year;
		}
		elseif ( ! empty( $w ) ) {
			// We need to get the month from MySQL
			$thisyear = (int) substr( $m, 0, 4 );
			// it seems MySQL's weeks disagree with PHP's
			$d = ( ( $w - 1 ) * 7 ) + 6;
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
			$thisyear = gmdate( 'Y', $ts );
			$thismonth = gmdate( 'm', $ts );
		}

		$unixmonth = mktime( 0, 0 , 0, $thismonth, 1, $thisyear );
		$last_day = date( 't', $unixmonth );

		// Get the next and previous month and year with at least one post
		$previous = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
			FROM $wpdb->posts
			WHERE post_date < '$thisyear-$thismonth-01'
			AND post_type = '$posttype' AND post_status = 'publish'
				ORDER BY post_date DESC
				LIMIT 1");
		$next = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
			FROM $wpdb->posts
			WHERE post_date > '$thisyear-$thismonth-{$last_day} 23:59:59'
			AND post_type = '$posttype' AND post_status = 'publish'
				ORDER BY post_date ASC
				LIMIT 1");

		/* translators: Calendar caption: 1: month name, 2: 4-digit year */
		$calendar_caption = _x( '%1$s %2$s', 'calendar caption' );
		$calendar_output = '<table id="wp-calendar">
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
			$day_name = $initial ? $wp_locale->get_weekday_initial( $wd ) : $wp_locale->get_weekday_abbrev( $wd );
			$wd = esc_attr( $wd );
			$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
		}

		$calendar_output .= '
		</tr>
		</thead>

		<tfoot>
		<tr>';

		if ( $previous ) {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="prev"><a href="' . get_month_link( $previous->year, $previous->month ) . '">&laquo; ' .
				$wp_locale->get_month_abbrev( $wp_locale->get_month( $previous->month ) ) .
			 '</a></td>';
		}
		else {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
		}

		$calendar_output .= "\n\t\t".'<td class="pad">&nbsp;</td>';

		if ( $next ) {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="next"><a href="' . get_month_link( $next->year, $next->month ) . '">' .
				$wp_locale->get_month_abbrev( $wp_locale->get_month( $next->month ) ) .
			' &raquo;</a></td>';
		}
		else {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
		}

		$calendar_output .= '
		</tr>
		</tfoot>

		<tbody>
		<tr>';

		$daywithpost = array();

		// Get days with posts
		$dayswithposts = $wpdb->get_results( "SELECT DISTINCT DAYOFMONTH(post_date)
			FROM $wpdb->posts WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00'
			AND post_type = '$posttype' AND post_status = 'publish'
			AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59'", ARRAY_N );
		if ( $dayswithposts ) {
			foreach ( (array) $dayswithposts as $daywith ) {
				$daywithpost[] = $daywith[0];
			}
		}

		// See how much we should pad in the beginning
		$pad = calendar_week_mod( date( 'w', $unixmonth ) - $week_begins );
		if ( 0 != $pad ) {
			$calendar_output .= "\n\t\t".'<td colspan="'. esc_attr( $pad ) .'" class="pad">&nbsp;</td>';
		}

		$newrow = false;
		$daysinmonth = (int) date( 't', $unixmonth );

		for ( $day = 1; $day <= $daysinmonth; ++$day ) {
			if ( isset( $newrow ) && $newrow ) {
				$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
			}
			$newrow = false;

			if ( $day == gmdate( 'j', current_time( 'timestamp' ) ) &&
				$thismonth == gmdate( 'm', current_time( 'timestamp' ) ) &&
				$thisyear == gmdate( 'Y', current_time( 'timestamp' ) ) ) {
				$calendar_output .= '<td id="today">';
			}
			else {
				$calendar_output .= '<td>';
			}

			if ( in_array( $day, $daywithpost ) ) {
				// any posts today?
				$date_format = date( _x( 'F j, Y', 'daily archives date format' ), strtotime( "{$thisyear}-{$thismonth}-{$day}" ) );
				$label = sprintf( __( 'Posts published on %s' ), $date_format );
				$calendar_output .= sprintf(
					'<a href="%s" aria-label="%s">%s</a>',
					get_day_link( $thisyear, $thismonth, $day ),
					// $this->get_custom_post_type_day_link( $posttype, $thisyear, $thismonth, $day ),
					esc_attr( $label ),
					$day
				);
			}
			else {
				$calendar_output .= $day;
			}
			$calendar_output .= '</td>';

			if ( 6 == calendar_week_mod( date( 'w', mktime( 0, 0 , 0, $thismonth, $day, $thisyear ) ) - $week_begins ) ) {
				$newrow = true;
			}
		}

		$pad = 7 - calendar_week_mod( date( 'w', mktime( 0, 0 , 0, $thismonth, $day, $thisyear ) ) - $week_begins );
		if ( 0 != $pad && 7 != $pad ) {
			$calendar_output .= "\n\t\t".'<td class="pad" colspan="'. esc_attr( $pad ) .'">&nbsp;</td>';
		}

		$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";

		if ( $echo ) {
			echo $calendar_output;
		}
		else {
			return $calendar_output;
		}
	}

	public function get_day_link_custom_post_type( $daylink, $year, $month, $day ) {
		global $wp_rewrite;

		$options = get_option($this->option_name);
		$posttype = $options[$this->number]['posttype'];

		if ( ! $year ) {
			$year = gmdate( 'Y', current_time( 'timestamp' ) );
		}
		if ( ! $month ) {
			$month = gmdate( 'm', current_time( 'timestamp' ) );
		}
		if ( ! $day ) {
			$day = gmdate( 'j', current_time( 'timestamp' ) );
		}

		$daylink = $wp_rewrite->get_day_permastruct();

		if ( ! empty( $daylink ) ) {
			$front = preg_replace( '/\/$/', '', $wp_rewrite->front );

			$daylink = str_replace( '%year%', $year, $daylink );
			$daylink = str_replace( '%monthnum%', zeroise( intval( $month ), 2 ), $daylink );
			$daylink = str_replace( '%day%', zeroise( intval( $day ), 2 ), $daylink );

			if ( 'post' == $posttype ) {
				$daylink = home_url( user_trailingslashit( $daylink, 'day' ) );
			}
			else {
				$type_obj = get_post_type_object( $posttype );
				$archive_name = $type_obj ->rewrite['slug'] ? $type_obj ->rewrite['slug'] : $posttype ;
				if ( $front ) {
					$new_front = $type_obj->rewrite['with_front'] ? $front : '' ;
					$daylink = str_replace( $front, $new_front . '/' . $archive_name , $daylink );
					$daylink = home_url( user_trailingslashit( $daylink, 'day' ) );
				}
				else {
					$daylink = home_url( user_trailingslashit( $archive_name . $daylink, 'day' ) );
				}
			}
		}
		else {
			$daylink = home_url( '?post_type=' . $posttype . '&m=' . $year . zeroise( $month, 2 ) . zeroise( $day, 2 ) );
		}

		return $daylink;
	}

	public function get_month_link_custom_post_type( $monthlink, $year, $month ) {
		global $wp_rewrite;

		$options = get_option($this->option_name);
		$posttype = $options[$this->number]['posttype'];

		if ( ! $year ) {
			$year = gmdate( 'Y', current_time( 'timestamp' ) );
		}
		if ( ! $month ) {
			$month = gmdate( 'm', current_time( 'timestamp' ) );
		}

		$monthlink = $wp_rewrite->get_month_permastruct();

		if ( ! empty( $monthlink ) ) {
			$front = preg_replace( '/\/$/', '', $wp_rewrite->front );

			$monthlink = str_replace( '%year%', $year, $monthlink );
			$monthlink = str_replace( '%monthnum%', zeroise( intval( $month ), 2 ), $monthlink );

			if ( 'post' == $posttype ) {
				$monthlink = home_url( user_trailingslashit( $monthlink, 'month' ) );
			}
			else {
				$type_obj = get_post_type_object( $posttype );
				$archive_name = $type_obj ->rewrite['slug'] ? $type_obj ->rewrite['slug'] : $posttype ;
				if ( $front ) {
					$new_front = $type_obj->rewrite['with_front'] ? $front : '' ;
					$monthlink = str_replace( $front, $new_front . '/' . $archive_name , $monthlink );
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

}
