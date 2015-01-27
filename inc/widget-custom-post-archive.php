<?php
/**
 * Custom Post Type Archives widget class
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Archives extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_archive', 'description' => __( 'A monthly archive of your site&#8217;s Posts.', 'custom-post-type-widgets' ) );
		parent::__construct( 'custom-post-type-archives', __( 'Archives (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	public function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_custom_post_type_archive', 'widget' );

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

		$posttype = $instance['posttype'];
		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$d = ! empty( $instance['dropdown'] ) ? '1' : '0';
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Archives', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( $d ) {
?>
			<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'>
				<option value=""><?php echo esc_attr( __( 'Select Month', 'custom-post-type-widgets' ) ); ?></option>
				<?php
				$this->get_custom_post_type_archives( apply_filters( 'widget_archives_dropdown_args', array(
					'posttype' => $posttype,
					'type' => 'monthly',
					'format' => 'option',
					'show_post_count' => $c,
				) ) );
				?>
			</select>
<?php
		}
		else {
?>
			<ul>
			<?php
			$this->get_custom_post_type_archives( apply_filters( 'widget_archives_args', array(
					'posttype' => $posttype,
					'type' => 'monthly',
					'show_post_count' => $c,
				) ) );
			?>
			</ul>
<?php
		}

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'posttype' => 'post', 'count' => 0, 'dropdown' => '' ) );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posttype'] = strip_tags( $new_instance['posttype'] );
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['dropdown'] = $new_instance['dropdown'] ? 1 : 0;

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_custom_post_type_archive'] ) ) {
			delete_option( 'widget_custom_post_type_archive' );
		}

		return $instance;
	}

	public function flush_widget_cache() {
		wp_cache_delete( 'widget_custom_post_type_recent_posts', 'widget' );
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'posttype' => 'post', 'count' => 0, 'dropdown' => '' ) );
		$title = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		$posttype = $instance['posttype'] ? $instance['posttype'] : 'post';
		$count = $instance['count'] ? 'checked="checked"' : '';
		$dropdown = $instance['dropdown'] ? 'checked="checked"' : '';
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'custom-post-type-widgets' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

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

		<p><input class="checkbox" type="checkbox" <?php echo $dropdown; ?> id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" /> <label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as dropdown', 'custom-post-type-widgets' ); ?></label><br>
		<input class="checkbox" type="checkbox" <?php echo $count; ?> id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" /> <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts' , 'custom-post-type-widgets' ); ?></label></p>
<?php
	}

	/**
	 * function that extend the wp_get_archives
	 * @see wp-includes/general-template.php
	 *
	 * @since 1.0.0
	 */
	public function get_custom_post_type_archives( $args = '' ) {
		global $wpdb, $wp_locale;

		$defaults = array(
			'posttype' => 'post',
			'type' => 'monthly', 'limit' => '',
			'format' => 'html', 'before' => '',
			'after' => '', 'show_post_count' => false,
			'echo' => 1, 'order' => 'DESC',
		);

		$r = wp_parse_args( $args, $defaults );

		if ( '' == $r['posttype'] ) {
			$r['posttype'] = 'post';
		}
		$posttype = $r['posttype'];

		if ( '' == $r['type'] ) {
			$r['type'] = 'monthly';
		}

		if ( ! empty( $r['limit'] ) ) {
			$r['limit'] = absint( $r['limit'] );
			$r['limit'] = ' LIMIT ' . $r['limit'];
		}

		$order = strtoupper( $r['order'] );
		if ( 'ASC' !== $order ) {
			$order = 'DESC';
		}

		// this is what will separate dates on weekly archive links
		$archive_week_separator = '&#8211;';

		// over-ride general date format ? 0 = no: use the date format set in Options, 1 = yes: over-ride
		$archive_date_format_over_ride = 0;

		// options for daily archive (only if you over-ride the general date format)
		$archive_day_date_format = 'Y/m/d';

		// options for weekly archive (only if you over-ride the general date format)
		$archive_week_start_date_format = 'Y/m/d';
		$archive_week_end_date_format	= 'Y/m/d';

		if ( ! $archive_date_format_over_ride ) {
			$archive_day_date_format = get_option( 'date_format' );
			$archive_week_start_date_format = get_option( 'date_format' );
			$archive_week_end_date_format = get_option( 'date_format' );
		}

		/**
		 * Filter the SQL WHERE clause for retrieving archives.
		 *
		 * @since 2.2.0
		 *
		 * @param string $sql_where Portion of SQL query containing the WHERE clause.
		 * @param array  $r         An array of default arguments.
		 */
		$where = apply_filters( 'getarchives_where', "WHERE post_type = '$posttype' AND post_status = 'publish'", $r );

		/**
		 * Filter the SQL JOIN clause for retrieving archives.
		 *
		 * @since 2.2.0
		 *
		 * @param string $sql_join Portion of SQL query containing JOIN clause.
		 * @param array  $r        An array of default arguments.
		 */
		$join = apply_filters( 'getarchives_join', '', $r );

		$output = '';

		$last_changed = wp_cache_get( 'last_changed', 'posts' );
		if ( ! $last_changed ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, 'posts' );
		}

		$limit = $r['limit'];

		if ( 'monthly' == $r['type'] ) {
			$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts FROM $wpdb->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date $order $limit";
			$key = md5( $query );
			$key = "get_custom_post_type_archives:$key:$last_changed";
			if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
				$results = $wpdb->get_results( $query );
				wp_cache_set( $key, $results, 'posts' );
			}
			if ( $results ) {
				$after = $r['after'];
				foreach ( (array) $results as $result ) {
					$url = $this->get_custom_post_type_month_link( $posttype, $result->year, $result->month );
					/* translators: 1: month name, 2: 4-digit year */
					$text = sprintf( __( '%1$s %2$d', 'custom-post-type-widgets' ), $wp_locale->get_month( $result->month ), $result->year );
					if ( $r['show_post_count'] ) {
						$r['after'] = '&nbsp;(' . $result->posts . ')' . $after;
					}
					$output .= get_archives_link( $url, $text, $r['format'], $r['before'], $r['after'] );
				}
			}
		}

		if ( $r['echo'] ) {
			echo $output;
		} else {
			return $output;
		}
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

		/**
		 * Filter the month archive permalink.
		 *
		 * @since 1.5.0
		 *
		 * @param string $monthlink Permalink for the month archive.
		 * @param int    $year      Year for the archive.
		 * @param int    $month     The month for the archive.
		 */
		return apply_filters( 'month_link', $monthlink, $year, $month );
	}
}
