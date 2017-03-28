<?php
/**
 * Class file for Adapter_Post_Widget
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

/**
 * Class Adapter_Post_Widget
 */
class Adapter_Post_Widget extends \WP_Widget {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	public $plugin_slug = 'adapter-post-preview';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $plugin_version = '1.0.3';

	/**
	 * Default number of posts to display in the carousel.
	 *
	 * @var number
	 */
	public $default_number_of_posts_in_carousel = 5;

	/**
	 * Default word length of the excerpt.
	 *
	 * @var number
	 */
	public $default_excerpt_length = 30;

	/**
	 * Instantiate the widget class.
	 */
	public function __construct() {
		$options = array(
			'classname' => 'adapter-post-preview',
			'description' => __( 'Show a carousel of recent posts, or a selected one.' , 'adapter-post-preview' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'adapter_post_preview' , __( 'Adapter Post Preview' , 'adapter-post-preview' ) , $options );

		// Only enqueue the styling if the widget appears in a sidebar, or if it's in the Customizer.
		// Props Weston Ruter.
		if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Enqueue widget styling.
	 *
	 * @return void.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( $this->plugin_slug . '-style', plugins_url( $this->plugin_slug . '/css/app-style.css' ), array(), $this->plugin_version );
	}

	/**
	 * Output the widget form.
	 *
	 * @param array $instance Widget data.
	 * @return void.
	 */
	public function form( $instance ) {
		$selected_post = isset( $instance['selected_post'] ) ? $instance['selected_post'] : '';
		$selected_post_field_name = $this->get_field_name( 'selected_post' );
		$selected_post_field_id = $this->get_field_id( 'selected_post' );
		$query = new \WP_Query( array(
			'post_type' => 'post',
			'orderby' => 'date',
			'posts_per_page' => '100',
			'no_found_rows' => true,
			'update_post_term_cache' => false,
		) );

		?>
		<p>
			<label for="<?php echo esc_attr( $selected_post_field_id ); ?>">
				Post to display:
			</label>
		<?php if ( $query->have_posts() ) { ?>
			<select name="<?php echo esc_attr( $selected_post_field_name ); ?>"
				id="<?php echo esc_attr( $selected_post_field_id ); ?>" class="widefat appw-post-selector">
				<option value="appw_carousel_recent" <?php selected( $selected_post, 'appw_carousel_recent', true ); ?>>
					<?php esc_html_e( 'Carousel of recent posts', 'adapter-post-preview' ); ?>
				</option>
				<?php while ( $query->have_posts() ) {
					$query->the_post();
					?>
					<option value="<?php echo esc_attr( get_the_id() ); ?>" <?php selected( $selected_post, get_the_id(), true ); ?>>
						<?php echo esc_html( get_the_title() ); ?>
					</option>
				<?php } ?>
			</select>
			<?php wp_reset_postdata();
			} else {
				esc_html_e( 'There are no posts on your site. Please write one.', 'adapter-post-preview' );
			}
			?>
		</p>
		<?php
	}

	/**
	 * Update the widget instance, based on the form submission.
	 *
	 * @param array $new_instance New widget data, updated from form.
	 * @param array $previous_instance Widget data, before being updated from form.
	 * @return array $instance Widget data, updated based on form submission.
	 */
	public function update( $new_instance, $previous_instance ) {
		$instance = $previous_instance;
		$selected_post = isset( $new_instance['selected_post'] ) ? $new_instance['selected_post'] : '';
		if ( $this->is_valid_value( $selected_post ) ) {
			$instance['selected_post'] = $selected_post;
		}
		return $instance;
	}

	/**
	 * Echo the markup of the widget.
	 *
	 * @param array $args Widget display data.
	 * @param array $instance Data for widget.
	 * @return void.
	 */
	public function widget( $args, $instance ) {
		$selected_post = isset( $instance['selected_post'] ) ? $instance['selected_post'] : '';
		if ( ! $selected_post ) {
			return;
		} elseif ( 'appw_carousel_recent' === $selected_post ) {
			$markup = $this->get_carousel_markup();
		} else {
			$markup = $this->get_single_post_preview_without_carousel( $selected_post );
		}

		echo wp_kses_post( $args['before_widget'] ) . $markup . wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Get the full markup for a Bootstrap carousel of posts.
	 *
	 * @return string $markup Post carousel markup.
	 */
	public function get_carousel_markup() {
		$post_preview_ids = $this->get_post_ids_for_carousel();
		$post_preview_container = $this->get_all_post_preview_markup( $post_preview_ids );
		$post_carousel = new APP_Carousel();
		$post_carousel->add_post_markup( $post_preview_container );

		return $post_carousel->get();
	}

	/**
	 * Get post IDs to output in the Bootstrap carousel.
	 *
	 * @return array $post_ids To output in the carousel.
	 */
	public function get_post_ids_for_carousel() {

		/**
		 * Filter the number of posts to display in the carousel.
		 *
		 * @param number $default_posts_per_page Initial number of posts on each page.
		 */
		$posts_in_carousel = apply_filters( 'bwp_number_of_posts_in_carousel' , $this->default_number_of_posts_in_carousel );

		$current_post = get_post();
		$excluded_post_id = isset( $current_post ) ? $current_post->ID : false;
		$query = new \WP_Query( array(
			'post_type' => 'post',
			'orderby' => 'date',
			'posts_per_page' => absint( $posts_in_carousel ),
			'no_found_rows' => true,
		) );
		$appw_post_ids = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				if ( has_post_thumbnail() && ( get_the_id() !== $excluded_post_id ) ) {
					array_push( $appw_post_ids , absint( get_the_id() ) );
				}
			}
			wp_reset_postdata();
		}
		return $appw_post_ids;
	}

	/**
	 * Get markup for all of the posts in the post preview.
	 *
	 * @param array $post_ids Posts to output in markup.
	 * @return string $post_preview_container Markup of all post previews.
	 */
	public function get_all_post_preview_markup( $post_ids ) {
		global $post;
		if ( isset( $post ) ) {
			$post_currently_on_page = $post;
		}
		$post_preview_container = array();
		foreach ( $post_ids as $post_id ) {
			$post_markup = $this->get_markup_for_single_post( $post_id );
			array_push( $post_preview_container , $post_markup );
		}
		if ( isset( $post_currently_on_page ) ) {
			$post = $post_currently_on_page;
		}
		return $post_preview_container;
	}

	/**
	 * Get markup for a single post preview.
	 *
	 * @param number $post_id The post with which to create the markup.
	 * @return string $post_markup Post preview markup for the post ID.
	 */
	public function get_markup_for_single_post( $post_id ) {
		$post_to_get_markup = get_post( $post_id );
		setup_postdata( $post_to_get_markup );
		$post_markup = $this->get_single_post_preview_markup( $post_to_get_markup );
		wp_reset_postdata();
		return $post_markup;
	}

	/**
	 * Get markup for a plain single post preview, without a carousel.
	 *
	 * @param number $post_id The post with which to create the markup.
	 * @return string $single_post_markup Post preview markup.
	 */
	public function get_single_post_preview_without_carousel( $post_id ) {
		if ( get_the_ID() === $post_id ) {
			// The post is already showing on the page, so there's no need for a preview of it.
			return '';
		}
		$markup = $this->get_all_post_preview_markup( array( $post_id ) );
		$single_post_markup = reset( $markup );
		return $single_post_markup;
	}

	/**
	 * Get the markup for a single post preview.
	 *
	 * @param WP_Post object $post The post for which to get the markup.
	 * @return string $markup Single post preview markup.
	 */
	public function get_single_post_preview_markup( $post ) {
		$thumbnail = get_the_post_thumbnail( $post->ID , 'medium' , array( 'class' => 'img-rounded img-responsive' ) );
		$title = '<div class="post-title"><h2>' . esc_html( get_the_title( $post->ID ) ) . '</h2></div>';
		$raw_excerpt = get_the_excerpt();

		/**
		 * Filter the length of the excerpt in words.
		 *
		 * @param number $default_excerpt_length Initial number of words in the excerpt.
		 */
		$excerpt_length = apply_filters( 'appw_excerpt_length' , $this->default_excerpt_length );
		$filtered_excerpt = '<p>' . wp_trim_words( $raw_excerpt , $excerpt_length , '...' ) . '</p>';
		$permalink = get_permalink( $post->ID );

		/**
		 * Filter the text in the button to view the full post page.
		 */
		$link_text = apply_filters( 'appw_link_text' , __( 'Read more' , 'adapter-post-preview' ) );
		$button = '<a class="btn btn-primary btn-med" href="' . esc_url( $permalink ) . '">' . esc_html( $link_text ) . '</a>';

		return '<div class="post-preview">'
					. wp_kses_post( $thumbnail )
					. wp_kses_post( $title )
					. '<div class="center-block excerpt-and-link">'
						. wp_kses_post( $filtered_excerpt )
						. wp_kses_post( $button )
					. '</div>
				</div>';
	}

	/**
	 * Whether the value input to the form is valid.
	 *
	 * @param string $input The value submitted in the widget form, via the select element.
	 * @return boolean $is_valid Whether the input value is valid, and should be saved in the database.
	 */
	public function is_valid_value( $input ) {
		return ( is_numeric( $input ) || ( 'appw_carousel_recent' === $input ) );
	}

}
