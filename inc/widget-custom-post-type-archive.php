<?php
/**
 * Custom Post Type Archives widget class
 *
 * @since 1.0.0
 * @package Custom Post Type Widgets
 */

class WP_Custom_Post_Type_Widgets_Archives extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_archive', 'description' => __( 'A monthly archive of your site&#8217;s Posts.', 'custom-post-type-widgets' ) );
		parent::__construct( 'custom-post-type-archives', __( 'Archives (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
	}

	public function widget( $args, $instance ) {
		$posttype = ! empty( $instance['posttype'] ) ? $instance['posttype'] : 'post';
		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$d = ! empty( $instance['dropdown'] ) ? '1' : '0';
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Archives', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );

		add_filter( 'month_link', array( $this, 'get_month_link_custom_post_type' ), 10, 3 );
		add_filter( 'get_archives_link', array( $this, 'trim_post_type' ), 10, 1 );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( $d ) {
?>
			<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'>
				<option value=""><?php echo esc_attr( __( 'Select Month', 'custom-post-type-widgets' ) ); ?></option>
				<?php
				wp_get_archives( apply_filters( 'widget_archives_dropdown_args', array(
					'post_type' => $posttype,
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
			wp_get_archives( apply_filters( 'widget_archives_args', array(
				'post_type' => $posttype,
				'type' => 'monthly',
				'show_post_count' => $c,
			) ) );
			?>
			</ul>
<?php
		}

		remove_filter( 'month_link', array( $this, 'get_month_link_custom_post_type' ) );
		remove_filter( 'get_archives_link', array( $this, 'trim_post_type' ) );

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'posttype' => 'post', 'count' => 0, 'dropdown' => '' ) );
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['posttype'] = strip_tags( $new_instance['posttype'] );
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['dropdown'] = $new_instance['dropdown'] ? 1 : 0;
		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'posttype' => 'post', 'count' => 0, 'dropdown' => '' ) );
		$title = isset( $instance['title'] ) ? sanitize_text_field( $instance['title'] ) : '';
		$posttype = $instance['posttype'] ? $instance['posttype'] : 'post';
		$count = $instance['count'] ? 'checked="checked"' : '';
		$dropdown = $instance['dropdown'] ? 'checked="checked"' : '';
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'custom-post-type-widgets' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

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
		?>

		<p><input class="checkbox" type="checkbox" <?php echo $dropdown; ?> id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" /> <label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as dropdown', 'custom-post-type-widgets' ); ?></label><br>
		<input class="checkbox" type="checkbox" <?php echo $count; ?> id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" /> <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts' , 'custom-post-type-widgets' ); ?></label></p>
<?php
	}

	public function get_month_link_custom_post_type( $monthlink, $year, $month ) {
		global $wp_rewrite;

		$options = get_option( $this->option_name );
		$posttype = ! empty( $options[$this->number]['posttype'] ) ? $options[$this->number]['posttype'] : '';

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

			if ( 'post' === $posttype ) {
				$monthlink = home_url( user_trailingslashit( $monthlink, 'month' ) );
			}
			else {
				$type_obj = get_post_type_object( $posttype );
				$archive_name = ! empty( $type_obj->rewrite['slug'] ) ? $type_obj->rewrite['slug'] : $posttype ;
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

	public function trim_post_type( $link_html ) {
		global $wp_rewrite;

		if ( ! $wp_rewrite->permalink_structure ) {
			return $link_html;
		}

		$options = get_option( $this->option_name );
		$posttype = ! empty( $options[$this->number]['posttype'] ) ? $options[$this->number]['posttype'] : '';

		$link_html = str_replace( '?post_type=' . $posttype, '', $link_html );

		return $link_html;
	}
}
