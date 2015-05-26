<?php
/**
 * Custom Post Type Categories widget class
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Categories extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_categories', 'description' => __( 'A list or dropdown of categories.', 'custom-post-type-widgets' ) );
		parent::__construct( 'custom-post-type-categories', __( 'Categories (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	public function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_custom_post_type_categories', 'widget' );

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

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Categories', 'custom-post-type-widgets' ) : $instance['title'], $instance, $this->id_base );
		$taxonomy = $instance['taxonomy'] ? $instance['taxonomy'] : 'category';
		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$d = ! empty( $instance['dropdown'] ) ? '1' : '0';

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$cat_args = array( 'orderby' => 'name', 'taxonomy' => $taxonomy, 'show_count' => $c, 'hierarchical' => $h );

		if ( $d ) {
			$cat_args['show_option_none'] = __( 'Select Category', 'custom-post-type-widgets' );

			if ( 'category' == $taxonomy ) {
?>
<form action="<?php bloginfo( 'url' ); ?>" method="get">
<?php
				wp_dropdown_categories( apply_filters( 'widget_categories_dropdown_args', $cat_args ) );
?>
<script>
/* <![CDATA[ */
	var dropdown = document.getElementById("cat");
	function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
			location.href = "<?php echo esc_attr( home_url() ); ?>/?cat="+dropdown.options[dropdown.selectedIndex].value;
		}
	}
	dropdown.onchange = onCatChange;
/* ]]> */
</script>
</form>
<?php
			}
			else {
?>
<form action="<?php bloginfo( 'url' ); ?>" method="get">
				<?php
				$this->wp_custom_post_type_dropdown_categories( apply_filters( 'widget_categories_dropdown_args', $cat_args ) );
				?>
<script>
/* <![CDATA[ */
	function onCatChange(dropdown) {
		if ( dropdown.options[dropdown.selectedIndex].value ) {
			return dropdown.form.submit();
		}
	}
/* ]]> */
</script>
</form>
<?php
			}
		}
		else {
?>
		<ul>
<?php
		$cat_args['title_li'] = '';

		/**
		 * Filter the arguments for the Categories widget.
		 *
		 * @since 2.8.0
		 *
		 * @param array $cat_args An array of Categories widget options.
		 */
		wp_list_categories( apply_filters( 'widget_categories_args', $cat_args ) );
?>
		</ul>
<?php
		}

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['taxonomy'] = stripslashes( $new_instance['taxonomy'] );
		$instance['count'] = ! empty( $new_instance['count'] ) ? 1 : 0;
		$instance['hierarchical'] = ! empty( $new_instance['hierarchical'] ) ? 1 : 0;
		$instance['dropdown'] = ! empty( $new_instance['dropdown'] ) ? 1 : 0;

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_custom_post_type_categories'] ) ) {
			delete_option( 'widget_custom_post_type_categories' );
		}

		return $instance;
	}

	public function flush_widget_cache() {
		wp_cache_delete( 'widget_custom_post_type_categories', 'widget' );
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		$taxonomy = isset( $instance['taxonomy'] ) ? $instance['taxonomy']: '';
		$count = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'custom-post-type-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy:', 'custom-post-type-widgets' ); ?></label>
		<select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>">
		<?php
		$taxonomies = get_taxonomies( '', 'objects' );
		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxobjects => $value ) {
				if ( ! $value->hierarchical ) {
					continue;
				}
				if ( 'nav_menu' == $taxobjects || 'link_category' == $taxobjects || 'post_format' == $taxobjects ) {
					continue;
				}
			?>
			<option value="<?php echo esc_attr( $taxobjects ); ?>"<?php selected( $taxobjects, $taxonomy ); ?>><?php _e( $value->label, 'custom-post-type-widgets' ); ?> (<?php echo $taxobjects; ?>)</option>
			<?php } ?>
		</select>
		</p>
		<?php } ?>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>"<?php checked( $dropdown ); ?> />
		<label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as dropdown', 'custom-post-type-widgets' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Show post counts', 'custom-post-type-widgets' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>"><?php _e( 'Show hierarchy', 'custom-post-type-widgets' ); ?></label></p>
<?php
	}

	/**
	 * function that extend the wp_dropdown_categories
	 * @see wp-includes/category-template.php
	 *
	 * @since 1.0.0
	 */
	public function wp_custom_post_type_dropdown_categories( $args = '' ) {
		$defaults = array(
			'show_option_all' => '', 'show_option_none' => '',
			'orderby' => 'id', 'order' => 'ASC',
			'show_count' => 0,
			'hide_empty' => 1, 'child_of' => 0,
			'exclude' => '', 'echo' => 1,
			'selected' => 0, 'hierarchical' => 0,
			'name' => 'cat', 'id' => '',
			'class' => 'postform', 'depth' => 0,
			'tab_index' => 0, 'taxonomy' => 'category',
			'hide_if_empty' => false, 'option_none_value' => -1,
			'value_field' => 'term_id',
		);

		$defaults['selected'] = ( taxonomy_exists( $args['taxonomy'] ) ) ? get_query_var( $args['taxonomy'] ) : 0;

		// Back compat.
		if ( isset( $args['type'] ) && 'link' == $args['type'] ) {
			_deprecated_argument( __FUNCTION__, '3.0', '' );
			$args['taxonomy'] = 'link_category';
		}

		$r = wp_parse_args( $args, $defaults );
		$option_none_value = $r['option_none_value'];

		if ( ! isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] ) {
			$r['pad_counts'] = true;
		}

		$tab_index = $r['tab_index'];

		$tab_index_attribute = '';
		if ( (int) $tab_index > 0 ) {
			$tab_index_attribute = " tabindex=\"$tab_index\"";
		}

		// Avoid clashes with the 'name' param of get_terms().
		$get_terms_args = $r;
		unset( $get_terms_args['name'] );
		$taxonomies = get_terms( array( $r['taxonomy'] ), $get_terms_args );

		$taxonomy = $r['taxonomy'];
		$name = esc_attr( $r['name'] );
		$class = esc_attr( $r['class'] );
		$id = $r['id'] ? esc_attr( $r['id'] ) : $name;

		if ( ! $r['hide_if_empty'] || ! empty( $taxonomies ) ) {
			$output = "<select name='$taxonomy' id='$id' class='$class' $tab_index_attribute onchange='onCatChange(this)'>\n";
		}
		else {
			$output = '';
		}

		if ( empty( $taxonomies ) && ! $r['hide_if_empty'] && ! empty( $r['show_option_none'] ) ) {
			$show_option_none = apply_filters( 'wp_list_categories', $r['show_option_none'] );
			$output .= "\t<option value='" . esc_attr( $option_none_value ) . "'  selected='selected'>$show_option_none</option>\n";
		}

		if ( ! empty( $taxonomies ) ) {

			if ( $r['show_option_all'] ) {
				$show_option_all = apply_filters( 'wp_list_categories', $r['show_option_all'] );
				$selected = ( '0' === strval( $r['selected'] ) ) ? " selected='selected'" : '';
				$output .= "\t<option value=''$selected>$show_option_all</option>\n";
			}

			if ( $r['show_option_none'] ) {
				$show_option_none = apply_filters( 'wp_list_categories', $r['show_option_none'] );
				$selected = selected( $option_none_value, $r['selected'], false );
				$output .= "\t<option value=''$selected>$show_option_none</option>\n";
			}

			if ( $r['hierarchical'] ) {
				$depth = $r['depth']; // Walk the full depth.
			}
			else {
				$depth = -1; // Flat.
			}

			$output .= $this->walk_custom_post_type_category_dropdown_tree( $taxonomies, $depth, $r );
		}
		if ( ! $r['hide_if_empty'] || ! empty( $taxonomies ) ) {
			$output .= "</select>\n";
		}

		/**
		 * Filter the taxonomy drop-down output.
		 *
		 * @since 2.1.0
		 *
		 * @param string $output HTML output.
		 * @param array  $r      Arguments used to build the drop-down.
		 */
		$output = apply_filters( 'wp_dropdown_cats', $output, $r );

		if ( $r['echo'] ) {
			echo $output;
		}

		return $output;
	}

	/**
	 * function that extend the walk_category_dropdown_tree
	 * @see wp-includes/category-template.php
	 *
	 * @since 1.0.0
	 */
	public function walk_custom_post_type_category_dropdown_tree() {
		$args = func_get_args();
		// the user's options are the third parameter
		if ( empty( $args[2]['walker'] ) || ! is_a( $args[2]['walker'], 'Walker' ) ) {
			$walker = new Custom_Post_Type_CategoryDropdown;
		}
		else {
			$walker = $args[2]['walker'];
		}

		return call_user_func_array( array( &$walker, 'walk' ), $args );
	}
}
