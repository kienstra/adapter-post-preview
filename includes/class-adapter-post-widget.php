<?php

class Adapter_Post_Widget extends WP_Widget {

	public function __construct() {
		$options = array( 'classname' => 'adapter-post-preview' ,
				  'description' => __( 'Show a carousel of recent posts, or a selected one' , 'adapter-post-preview' ) ,
		);
		$this->WP_Widget( 'adapter_post_preview' , __( 'Adapter Post Preview' , 'adapter-post-preview' ) , $options );
	}

	public function form( $instance ) {
		$selected_post = isset( $instance[ 'selected_post' ] ) ? $instance[ 'selected_post' ] : "";
		$selected_post_field_name = $this->get_field_name( 'selected_post' );
		$selected_post_field_id = $this->get_field_id( 'selected_post' );
		$query = new WP_Query( array( 'post_type'              => 'post' , 
					      'orderby'	    	       => 'date' ,
					      'posts_per_page' 	       => '100' ,
					      'no_found_rows'          => true ,
					      'update_post_term_cache' => false ,
		) );
		?>
		<p>
			<label for="<?php echo esc_attr( $selected_post_field_id ); ?>">
				Post to display:
			</label>
		<?php if ( $query->have_posts() ) : ?>
			<select name="<?php echo esc_attr( $selected_post_field_name ); ?>" id="<?php echo esc_attr( $selected_post_field_id ); ?>" class="widefat appw-post-selector">
				<option value="appw_carousel_recent" <?php selected( $selected_post , 'appw_carousel_recent' , true ); ?>>
					<?php esc_html_e( 'Carousel of recent posts' , 'adapter-post-preview' ); ?>
				</option>
				<?php while ( $query->have_posts() ) : 
					$query->the_post();
					?>
						<option value="<?php echo esc_attr( get_the_id() ); ?>" <?php selected( $selected_post , get_the_id() , true ); ?>>
							<?php echo esc_html( get_the_title() ); ?>
						</option>
				<?php endwhile; ?>
			</select>
			<?php wp_reset_postdata();
		else :
			esc_html_e( 'No posts on your site. Please write one.' , 'adapter-post-preview' );
		endif; 
		?>
		</p>
		<?php
	}

	public function update( $new_instance , $previous_instance ) {
		$instance = $previous_instance;
		$selected_post = isset( $new_instance[ 'selected_post' ] ) ? $new_instance[ 'selected_post' ] : "";
		if ( appw_is_valid_value( $selected_post ) ) {
			$instance[ 'selected_post' ] = $selected_post;
		}
		return $instance;
	}

	public function widget( $args , $instance ) {	// todo : remove extract
		$selected_post	= isset( $instance[ 'selected_post' ] ) ? $instance[ 'selected_post' ] : "";
		if ( ! $selected_post ) {
			return;
		}
		else if ( 'appw_carousel_recent' == $selected_post ) {
			$markup = $this->get_carousel_markup();
		}
		else {
			$markup = $this->get_single_post_preview_without_carousel( $selected_post );
		}
		
		echo $args[ 'before_widget' ] . $markup . $args[ 'after_widget' ];
	}

	protected function get_carousel_markup() {
		$post_preview_ids = $this->get_post_ids_for_carousel();
		$post_preview_container = $this->get_all_post_preview_markup( $post_preview_ids );

		$post_carousel = new APP_Carousel();
		foreach( $post_preview_container as $post_preview ) {
			$post_carousel->add_post_markup( $post_preview );
		}
		$markup = $post_carousel->get();
		return $markup;
	}

	protected function get_post_ids_for_carousel() {
		$posts_per_page = apply_filters( 'bwp_number_of_posts_in_carousel' , 5 );
		global $post;
		$excluded_post_id = isset( $post ) ? $post->ID : false;
		$query = new WP_Query( array(
					'post_type' => 'post' ,
					'orderby' => 'date' ,
					'posts_per_page' => absint( $posts_per_page ) ,
					'no_found_rows' => true ,						  ) );
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

	protected function get_all_post_preview_markup( $post_ids ) {
		global $post;
		if ( isset( $post ) ) {
			$post_currently_on_page = $post;
		}
		$post_preview_container = array();
		foreach( $post_ids as $post_id ) {
			$post_markup =	$this->get_markup_for_single_post( $post_id );
			array_push( $post_preview_container , $post_markup ) ;
		}
		if ( isset( $post_currently_on_page ) ) {
			$post = $post_currently_on_page;
		}
		return $post_preview_container;
	}

	protected function get_markup_for_single_post( $post_id ) {
		$post = get_post( $post_id );
		setup_postdata( $post );
		$post_markup =	appw_get_single_post_preview_markup( $post );
		wp_reset_postdata();
		return $post_markup;
	}

	protected function get_single_post_preview_without_carousel( $post_id ) {
		global $post;
		if ( $post->ID == $post_id ) {
			return ''; //the post is already showing on the page, so no need for a preview of it
		}
		$markup = $this->get_all_post_preview_markup( array( $post_id ) );
		$single_post_markup = reset( $markup );
		return $single_post_markup;
	}

}
/* end class Adapter_Post_Widget */


function appw_get_single_post_preview_markup( $post ) {
	$thumbnail = get_the_post_thumbnail( $post->ID , 'medium' , array( 'class' => 'img-rounded img-responsive' ) );
	$title = '<div class="post-title"><h2>' . esc_html( get_the_title( $post->ID ) ) . '</h2></div>';

	$raw_excerpt = get_the_excerpt();
	$excerpt_length = apply_filters( 'appw_excerpt_length' , 30 );
	$filtered_excerpt = '<p>' . wp_trim_words( $raw_excerpt , $excerpt_length , '...' ) . '</p>';

	$permalink = get_permalink( $post->ID );
	$link_text = apply_filters( 'appw_link_text' , __( 'Read more' , 'adapter-post-preview' ) );
	$button = '<a class="btn btn-primary btn-med" href="' . esc_url( $permalink ) . '">' . esc_html( $link_text ) . '</a>';

	$markup = "<div class='post-preview'>
			{$thumbnail}
			{$title}
			<div class='center-block excerpt-and-link'>
			     {$filtered_excerpt}
			     {$button}
			</div>
		   </div>\n";

	return $markup;
}

function appw_is_valid_value( $input ) {
	return ( is_numeric( $input ) || ( 'appw_carousel_recent' == $input ) );
}