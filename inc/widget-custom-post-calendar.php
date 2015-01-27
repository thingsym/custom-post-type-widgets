<?php
/**
 * Custom Post Type Calendar widget class
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Calendar extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_calendar', 'description' => __( 'A calendar of your site&#8217;s Posts.', 'custom-post-type-widgets' ) );
		parent::__construct( 'custom-post-type-calendar', __( 'Calendar (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );

		add_action( 'save_post', array( &$this, 'delete_custom_post_type_calendar_cache' ) );
		add_action( 'delete_post', array( &$this, 'delete_custom_post_type_calendar_cache' ) );
		add_action( 'update_option_start_of_week', array( &$this, 'delete_custom_post_type_calendar_cache' ) );
		add_action( 'update_option_gmt_offset', array( &$this, 'delete_custom_post_type_calendar_cache' ) );
	}

	public function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_custom_post_type_calendar', 'widget' );

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

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Calendar', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );
		$posttype = $instance['posttype'];

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo '<div id="calendar_wrap">';
		$this->get_custom_post_type_calendar( $posttype );
		echo '</div>';
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posttype'] = strip_tags( $new_instance['posttype'] );

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_custom_post_type_calendar'] ) ) {
			delete_option( 'widget_custom_post_type_calendar' );
		}

		return $instance;
	}

	public function flush_widget_cache() {
		wp_cache_delete( 'widget_custom_post_type_calendar', 'widget' );
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		$posttype = isset( $instance['posttype'] ) ? $instance['posttype']: 'post';
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'custom-post-type-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'posttype' ); ?>"><?php _e( 'Post Type:', 'custom-post-type-widgets' ); ?></label>
		<select name="<?php echo $this->get_field_name( 'posttype' ); ?>" id="<?php echo $this->get_field_id( 'posttype' ); ?>">
		<?php
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			foreach ( $post_types as $post_type => $value ) {
				if ( 'attachment' == $post_type || 'page' == $post_type ) {
					continue;
				}
		?>
				<option value="<?php echo esc_attr( $post_type ); ?>"<?php selected( $post_type, $posttype ); ?>><?php _e( $value->label, 'custom-post-type-widgets' ); ?></option>
		<?php } ?>
		</select>
		</p>
<?php
	}

	/**
	 * function that extend the get_calendar
	 * @see wp-includes/general-template.php
	 *
	 * @since 1.0.0
	 */
	public function get_custom_post_type_calendar( $posttype, $initial = true, $echo = true ) {
		global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

		$key = md5( $m . $monthnum . $year );
		if ( $cache = wp_cache_get( 'get_custom_post_type_calendar', 'calendar' ) ) {
			if ( is_array( $cache ) && isset( $cache[ $key ] ) ) {
				if ( $echo ) {
					echo apply_filters( 'get_custom_post_type_calendar', $cache[ $key ] );
					return;
				}
				else {
					return apply_filters( 'get_custom_post_type_calendar', $cache[ $key ] );
				}
			}
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		// Quick check. If we have no posts at all, abort!
		if ( ! $posts ) {
			$gotsome = $wpdb->get_var( "SELECT 1 as test FROM $wpdb->posts WHERE post_type = '$posttype' AND post_status = 'publish' LIMIT 1" );
			if ( ! $gotsome ) {
				$cache[ $key ] = '';
				wp_cache_set( 'get_custom_post_type_calendar', $cache, 'calendar' );
				return;
			}
		}

		if ( isset( $_GET['w'] ) ) {
			$w = '' . intval( $_GET['w'] );
		}

		// week_begins = 0 stands for Sunday
		$week_begins = intval( get_option( 'start_of_week' ) );

		// Let's figure out when we are
		if ( ! empty( $monthnum ) && ! empty( $year ) ) {
			$thismonth = '' . zeroise( intval( $monthnum ), 2 );
			$thisyear = '' . intval( $year );
		}
		elseif ( ! empty( $w ) ) {
			// We need to get the month from MySQL
			$thisyear = '' . intval( substr( $m, 0, 4 ) );
			$d = ( ( $w - 1 ) * 7 ) + 6; //it seems MySQL's weeks disagree with PHP's
			$thismonth = $wpdb->get_var( "SELECT DATE_FORMAT((DATE_ADD('{$thisyear}0101', INTERVAL $d DAY) ), '%m')" );
		}
		elseif ( ! empty( $m ) ) {
			$thisyear = '' . intval( substr( $m, 0, 4 ) );
			if ( strlen( $m ) < 6 ) {
				$thismonth = '01';
			}
			else {
				$thismonth = '' . zeroise( intval( substr( $m, 4, 2 ) ), 2 );
			}
		}
		else {
			$thisyear = gmdate( 'Y', current_time( 'timestamp' ) );
			$thismonth = gmdate( 'm', current_time( 'timestamp' ) );
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
		<caption>' . sprintf( $calendar_caption, $wp_locale->get_month( $thismonth ), date( 'Y', $unixmonth ) ) . '</caption>
		<thead>
		<tr>';

		$myweek = array();

		for ( $wdcount = 0; $wdcount <= 6; $wdcount++ ) {
			$myweek[] = $wp_locale->get_weekday( ( $wdcount + $week_begins ) % 7 );
		}

		foreach ( $myweek as $wd ) {
			$day_name = ( true == $initial ) ? $wp_locale->get_weekday_initial( $wd ) : $wp_locale->get_weekday_abbrev( $wd );
			$wd = esc_attr( $wd );
			$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
		}

		$calendar_output .= '
		</tr>
		</thead>

		<tfoot>
		<tr>';

		if ( $previous ) {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="prev"><a href="' . $this->get_custom_post_type_month_link( $posttype, $previous->year, $previous->month ) . '">&laquo; ' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $previous->month ) ) . '</a></td>';
		}
		else {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
		}

		$calendar_output .= "\n\t\t".'<td class="pad">&nbsp;</td>';

		if ( $next ) {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="next"><a href="' . $this->get_custom_post_type_month_link( $posttype, $next->year, $next->month ) . '">' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $next->month ) ) . ' &raquo;</a></td>';
		}
		else {
			$calendar_output .= "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
		}

		$calendar_output .= '
		</tr>
		</tfoot>

		<tbody>
		<tr>';

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
		else {
			$daywithpost = array();
		}

		if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE' ) || false !== stripos( $_SERVER['HTTP_USER_AGENT'], 'camino' ) || false !== stripos( $_SERVER['HTTP_USER_AGENT'], 'safari' ) ) {
			$ak_title_separator = "\n";
		}
		else {
			$ak_title_separator = ', ';
		}

		$ak_titles_for_day = array();
		$ak_post_titles = $wpdb->get_results( 'SELECT ID, post_title, DAYOFMONTH(post_date) as dom '
			. "FROM $wpdb->posts "
			. "WHERE post_date >= '{$thisyear}-{$thismonth}-01 00:00:00' "
			. "AND post_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59' "
			. "AND post_type = '$posttype' AND post_status = 'publish'"
		);
		if ( $ak_post_titles ) {
			foreach ( (array) $ak_post_titles as $ak_post_title ) {

				$post_title = esc_attr( apply_filters( 'the_title', $ak_post_title->post_title, $ak_post_title->ID ) );

				if ( empty( $ak_titles_for_day[ 'day_'.$ak_post_title->dom ] ) ) {
					$ak_titles_for_day[ 'day_' . $ak_post_title->dom ] = '';
				}
				if ( empty( $ak_titles_for_day[ "$ak_post_title->dom" ] ) ) { // first one
					$ak_titles_for_day[ "$ak_post_title->dom" ] = $post_title;
				}
				else {
					$ak_titles_for_day[ "$ak_post_title->dom" ] .= $ak_title_separator . $post_title;
				}
			}
		}

		// See how much we should pad in the beginning
		$pad = calendar_week_mod( date( 'w', $unixmonth ) - $week_begins );
		if ( 0 != $pad ) {
			$calendar_output .= "\n\t\t".'<td colspan="'. esc_attr( $pad ) .'" class="pad">&nbsp;</td>';
		}

		$daysinmonth = intval( date( 't', $unixmonth ) );
		for ( $day = 1; $day <= $daysinmonth; ++$day ) {
			if ( isset( $newrow ) && $newrow ) {
				$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
			}
			$newrow = false;

			if ( $day == gmdate( 'j', current_time( 'timestamp' ) ) && $thismonth == gmdate( 'm', current_time( 'timestamp' ) ) && $thisyear == gmdate( 'Y', current_time( 'timestamp' ) ) ) {
				$calendar_output .= '<td id="today">';
			}
			else {
				$calendar_output .= '<td>';
			}

			if ( in_array( $day, $daywithpost ) ) {
				// any posts today?
				$calendar_output .= '<a href="' . $this->get_custom_post_type_day_link( $posttype, $thisyear, $thismonth, $day ) . '" title="' . esc_attr( $ak_titles_for_day[ $day ] ) . "\">$day</a>";
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

		$cache[ $key ] = $calendar_output;
		wp_cache_set( 'get_custom_post_type_calendar', $cache, 'calendar' );

		if ( $echo ) {
			echo apply_filters( 'get_custom_post_type_calendar', $calendar_output );
		}
		else {
			return apply_filters( 'get_custom_post_type_calendar', $calendar_output );
		}
	}

	/**
	 * function that extend the get_day_link
	 * @see wp-includes/link-template.php
	 *
	 * @since 1.0.0
	 */
	public function get_custom_post_type_day_link( $posttype, $year, $month, $day ) {
		global $wp_rewrite;

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
				if ( $front ) {
					$new_front = get_post_type_object( $posttype )->rewrite['with_front'] ? $front : '' ;
					$daylink = str_replace( $front, $new_front . '/' . $posttype , $daylink );
					$daylink = home_url( user_trailingslashit( $daylink, 'day' ) );
				}
				else {
					$daylink = home_url( user_trailingslashit( $posttype . $daylink, 'day' ) );
				}
			}
		}
		else {
			$daylink = home_url( '?post_type=' . $posttype . '&m=' . $year . zeroise( $month, 2 ) . zeroise( $day, 2 ) );
		}

		return apply_filters( 'day_link', $daylink, $year, $month, $day );
	}

	/**
	 * function that extend the get_month_link
	 * @see wp-includes/link-template.php
	 *
	 * @since 1.0.0
	 */
	public function get_custom_post_type_month_link( $posttype, $year, $month ) {
		global $wp_rewrite;

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
				if ( $front ) {
					$new_front = get_post_type_object( $posttype )->rewrite['with_front'] ? $front : '' ;
					$monthlink = str_replace( $front, $new_front . '/' . $posttype , $monthlink );
					$monthlink = home_url( user_trailingslashit( $monthlink, 'month' ) );
				}
				else {
					$monthlink = home_url( user_trailingslashit( $posttype . $monthlink, 'month' ) );
				}
			}
		}
		else {
			$monthlink = home_url( '?post_type=' . $posttype . '&m=' . $year . zeroise( $month, 2 ) );
		}
		return apply_filters( 'month_link', $monthlink, $year, $month );
	}

	/**
	 * function that extend the delete_get_calendar_cache
	 * @see wp-includes/general-template.php
	 *
	 * @since 1.0.0
	 */
	public function delete_custom_post_type_calendar_cache() {
		wp_cache_delete( 'get_custom_post_type_calendar', 'calendar' );
	}

}
