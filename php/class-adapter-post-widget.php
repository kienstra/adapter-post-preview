<?php
/**
 * The Adapter Post widget.
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

/**
 * The plugin's widget.
 */
class Adapter_Post_Widget extends \WP_Widget {

	/**
	 * The ID base of the widget.
	 *
	 * @var string
	 */
	const ID_BASE = 'adapter_post_preview';

	/**
	 * The key of the selected post value.
	 *
	 * @var string
	 */
	const SELECTED_POST = 'selected_post';

	/**
	 * The value of the <option> to display a carousel.
	 *
	 * @var string
	 */
	const DISPLAY_CAROUSEL = 'appw_carousel_recent';

	/**
	 * The default number of posts in a carousel.
	 *
	 * @var int
	 */
	const NUMBER_OF_POSTS_IN_CAROUSEL = 5;

	/**
	 * The default excerpt length.
	 *
	 * @var int
	 */
	const DEFAULT_EXCERPT_LENGTH = 30;

	/**
	 * Adapter_Post_Widget constructor.
	 */
	public function __construct() {
		$options = array(
			'classname'                   => Plugin::SLUG,
			'description'                 => __( 'Show a carousel of recent posts, or a selected one', 'adapter-post-preview' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( self::ID_BASE, __( 'Adapter Post Preview', 'adapter-post-preview' ), $options );
	}

	/**
	 * Outputs the widget form.
	 *
	 * @param array $instance The widget instance values.
	 * @return void
	 */
	public function form( $instance ) {
		$selected_post          = isset( $instance[ self::SELECTED_POST ] ) ? $instance[ self::SELECTED_POST ] : '';
		$selected_post_field_id = $this->get_field_id( self::SELECTED_POST );
		$query                  = new \WP_Query( array(
			'post_type'              => 'post',
			'posts_per_page'         => '100',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		) );

		?>
		<p>
			<label for="<?php echo esc_attr( $selected_post_field_id ); ?>">
				<?php esc_html_e( 'Post to display:', 'adapter-post-preview' ); ?>
			</label>
			<?php if ( $query->have_posts() ) : ?>
				<select name="<?php echo esc_attr( $this->get_field_name( self::SELECTED_POST ) ); ?>" id="<?php echo esc_attr( $selected_post_field_id ); ?>" class="widefat appw-post-selector">
					<option value="<?php echo esc_attr( self::DISPLAY_CAROUSEL ); ?>" <?php selected( $selected_post, self::DISPLAY_CAROUSEL ); ?>>
						<?php esc_html_e( 'Carousel of recent posts', 'adapter-post-preview' ); ?>
					</option>
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						?>
						<option value="<?php the_ID(); ?>" <?php selected( $selected_post, get_the_id() ); ?>>
							<?php the_title(); ?>
						</option>
					<?php endwhile; ?>
				</select>
				<?php
				wp_reset_postdata();
			else :
				esc_html_e( 'There are no posts on your site. Please create one.', 'adapter-post-preview' );
			endif;
			?>
		</p>
		<?php
	}

	/**
	 * Updates the widget instance after an edit.
	 *
	 * @param array $new_instance The instance after the change(s).
	 * @param array $previous_instance The instance before the change(s).
	 * @return array $processed_instance The instance, having been processed.
	 */
	public function update( $new_instance, $previous_instance ) {
		$instance      = $previous_instance;
		$selected_post = isset( $new_instance[ self::SELECTED_POST ] ) ? $new_instance[ self::SELECTED_POST ] : '';
		if ( is_numeric( $selected_post ) || ( self::DISPLAY_CAROUSEL === $selected_post ) ) {
			$instance[ self::SELECTED_POST ] = $selected_post;
		}
		return $instance;
	}

	/**
	 * Outputs the widget markup: either a carousel or a single post preview.
	 *
	 * @param array $args The widget arguments, as an associative array.
	 * @param array $instance The widget instance, as an associative array.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$selected_post = isset( $instance[ self::SELECTED_POST ] ) ? $instance[ self::SELECTED_POST ] : '';

		if ( self::DISPLAY_CAROUSEL === $selected_post ) {
			$widget_markup = $this->get_carousel_markup();
		} elseif ( is_numeric( $selected_post ) ) {
			$widget_markup = $this->get_single_post_preview_markup( get_post( $selected_post ) );
		} else {
			$widget_markup = '';
		}

		echo $args['before_widget'] . $widget_markup . $args['after_widget']; // WPCS: XSS ok.
	}

	/**
	 * Gets the carousel markup.
	 *
	 * @return string $markup The carousel markup.
	 */
	public function get_carousel_markup() {
		$post_previews = $this->get_all_post_preview_markup( $this->get_post_ids_for_carousel() );
		$post_carousel = new Carousel( $post_previews );
		return $post_carousel->get();
	}

	/**
	 * Gets the post IDs to show in the carousel.
	 *
	 * @return array $post_ids The post IDs to display in the carousel.
	 */
	public function get_post_ids_for_carousel() {

		/**
		 * The number of posts in a carousel.
		 *
		 * @param int $number_posts The number of posts to display in the carousel.
		 */
		$posts_per_page = apply_filters( 'bwp_number_of_posts_in_carousel', self::NUMBER_OF_POSTS_IN_CAROUSEL );
		$query          = new \WP_Query( array(
			'post_type'      => 'post',
			'orderby'        => 'date',
			'posts_per_page' => absint( $posts_per_page ),
			'no_found_rows'  => true,
			'fields'         => 'ids',
		) );
		$appw_post_ids  = array();
		foreach ( $query->posts as $post_for_carousel ) {
			if ( has_post_thumbnail( $post_for_carousel ) && get_the_ID() !== $post_for_carousel ) {
				$appw_post_ids[] = $post_for_carousel;
			}
		}
		return $appw_post_ids;
	}

	/**
	 * Gets the post preview markup for all of the given IDs.
	 *
	 * @param array $post_ids The post IDs for which to get the preview markup.
	 * @return array The post preview markup
	 */
	public function get_all_post_preview_markup( $post_ids ) {
		$post_preview_container = array();
		foreach ( $post_ids as $post_id ) {
			$markup = $this->get_single_post_preview_markup( get_post( $post_id ) );
			if ( $markup ) {
				$post_preview_container[] = $markup;
			}
		}
		return $post_preview_container;
	}

	/**
	 * Gets the markup for a single post preview.
	 *
	 * @param \WP_Post $post The post for which to get the markup.
	 * @return string $markup The markup for a single post preview, or an empty string.
	 */
	public function get_single_post_preview_markup( $post ) {

		// If post is already showing on the page, there's no need for a preview of it.
		if ( get_post() === $post || 'WP_Post' !== get_class( $post ) ) {
			return '';
		}

		/**
		 * The character length of the excerpt at the bottom of the single post preview.
		 *
		 * @param int
		 */
		$excerpt_length = apply_filters( 'appw_excerpt_length', self::DEFAULT_EXCERPT_LENGTH );

		/**
		 * The text for the single post preview link, which leads to the URL of the post.
		 *
		 * @param string
		 */
		$link_text = apply_filters( 'appw_link_text', __( 'Read more', 'adapter-post-preview' ) );

		$text = strip_shortcodes( $post->post_content );

		/** This filter is documented in wp-includes/post-template.php */
		$text = apply_filters( 'the_content', $text );
		$text = str_replace( ']]>', ']]&gt;', $text );

		/** This filter is documented in wp-includes/formatting.php */
		$excerpt_more = apply_filters( 'excerpt_more', ' [&hellip;]' );
		$raw_excerpt  = wp_trim_words( $text, $excerpt_length, $excerpt_more );

		ob_start();
		?>
		<div class="post-preview">
			<?php echo get_the_post_thumbnail( $post->ID, 'medium', array( 'class' => 'img-rounded img-responsive' ) ); ?>
			<div class="post-title"><h2><?php echo esc_html( get_the_title( $post->ID ) ); ?></h2></div>
			<div class='center-block excerpt-and-link'>
				<p><?php echo wp_kses_post( wp_trim_words( $raw_excerpt, $excerpt_length, '...' ) ); ?></p>
				<a class="btn btn-primary btn-med" href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"><?php echo esc_html( $link_text ); ?></a>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
