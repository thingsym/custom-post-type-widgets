<?php
/**
 * Custom Post Type Recent Posts widget class
 *
 * @since 1.0.0
 * @package Custom Post Type Widgets
 */

class WP_Custom_Post_Type_Widgets_Recent_Posts extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_recent_entries', 'description' => __( 'Your site’s most recent custom Posts.', 'custom-post-type-widgets' ) );
		parent::__construct( 'custom-post-type-recent-posts', __( 'Recent Posts (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
		$this->alt_option_name = 'widget_custom_post_type_recent_posts';
	}

	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Recent Posts', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );
		$posttype = $instance['posttype'];
		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
			$number = 5;
		}
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		if ( array_key_exists( $posttype, (array) $post_types ) ) {
			$r = new WP_Query( apply_filters( 'widget_posts_args', array(
				'post_type' => $posttype,
				'posts_per_page' => $number,
				'no_found_rows' => true,
				'post_status' => 'publish',
				'ignore_sticky_posts' => true,
			) ) );

			if ( $r->have_posts() ) : ?>
				<?php echo $args['before_widget']; ?>
				<?php if ( $title ) {
					echo $args['before_title'] . $title . $args['after_title'];
				} ?>
				<ul>
				<?php while ( $r->have_posts() ) : $r->the_post(); ?>
					<li><a href="<?php the_permalink() ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
					<?php if ( $show_date ) : ?>
						<span class="post-date"><?php echo get_the_date(); ?></span>
					<?php endif; ?>
					</li>
				<?php endwhile; ?>
				</ul>
				<?php echo $args['after_widget']; ?>
				<?php
				wp_reset_postdata();
			endif;
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posttype'] = strip_tags( $new_instance['posttype'] );
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
		return $instance;
	}

	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$posttype = isset( $instance['posttype'] ) ? $instance['posttype']: 'post';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'custom-post-type-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

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
				if ( 'attachment' === $post_type ) {
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

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:', 'custom-post-type-widgets' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?', 'custom-post-type-widgets' ); ?></label></p>
<?php
	}
}
