<?php
/**
 * Custom Post Type Recent Comments widget class
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Recent_Comments extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_recent_comments', 'description' => __( 'Your site’s most recent comments.', 'custom-post-type-widgets' ) );
		parent::__construct( 'custom-post-type-recent-comments', __( 'Recent Comments (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
		$this->alt_option_name = 'widget_custom_post_type_recent_comments';

		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'wp_head', array( &$this, 'recent_comments_style' ) );
		}

		add_action( 'comment_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'edit_comment', array( &$this, 'flush_widget_cache' ) );
		add_action( 'transition_comment_status', array( &$this, 'flush_widget_cache' ) );
	}

	public function recent_comments_style() {
		if ( ! current_theme_supports( 'widgets' ) // Temp hack #14876
			|| ! apply_filters( 'show_recent_comments_widget_style', true, $this->id_base ) ) {
			return;
		}
		?>
	<style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>
<?php
	}

	public function widget( $args, $instance ) {
		global $comments, $comment;

		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_custom_post_type_recent_comments', 'widget' );
		}

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

		$output = '';

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Recent Comments', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );
		$posttype = $instance['posttype'];
		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
			$number = 5;
		}

		$comments = get_comments( array(
			'post_type' => $posttype,
			'number' => $number,
			'status' => 'approve',
			'post_status' => 'publish',
		) );

		$output .= $args['before_widget'];
		if ( $title ) {
			$output .= $args['before_title'] . $title . $args['after_title'];
		}

		$output .= '<ul>';
		if ( $comments ) {
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

			foreach ( (array) $comments as $comment ) {
				$output .= '<li class="recentcomments">';
				/* translators: comments widget: 1: comment author, 2: post link */
				$output .= sprintf( _x( '%1$s on %2$s', 'widgets' ),
					'<span class="comment-author-link">' . get_comment_author_link() . '</span>',
					'<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a>'
				);
				$output .= '</li>';
			}
		}
		$output .= '</ul>';
		$output .= $args['after_widget'];

		echo $output;

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = $output;
			wp_cache_set( 'widget_custom_post_type_recent_comments', $cache, 'widget' );
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posttype'] = strip_tags( $new_instance['posttype'] );
		$instance['number'] = absint( $new_instance['number'] );

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_custom_post_type_recent_comments'] ) ) {
			delete_option( 'widget_custom_post_type_recent_comments' );
		}

		return $instance;
	}

	public function flush_widget_cache() {
		wp_cache_delete( 'widget_custom_post_type_recent_comments', 'widget' );
	}

	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		$posttype = isset( $instance['posttype'] ) ? $instance['posttype']: 'post';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'custom-post-type-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'posttype' ); ?>"><?php _e( 'Post Type:', 'custom-post-type-widgets' ); ?></label>
		<select name="<?php echo $this->get_field_name( 'posttype' ); ?>" id="<?php echo $this->get_field_id( 'posttype' ); ?>">
		<?php
			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			foreach ( $post_types as $post_type => $value ) {
				if ( 'attachment' == $post_type ) {
					continue;
				}
		?>
				<option value="<?php echo $post_type; ?>"<?php selected( $post_type, $posttype ); ?>><?php _e( $value->label, 'custom-post-type-widgets' ); ?></option>
		<?php } ?>
		</select>
		</p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of comments to show:', 'custom-post-type-widgets' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}
