<?php

class Adapter_Post_Widget extends WP_Widget {

	function __construct() {
		$options = array( 'classname' => 'adapter-post-preview' ,
				  'description' => __( 'Show a carousel of recent posts, or a selected one' , 'adapter-post-preview' ) ,
		);
		$this->WP_Widget( 'adapter_post_preview' , __( 'Adapter Post Preview' , 'adapter-post-preview' ) , $options );
	}

	function form( $instance ) {
		$selected_post = isset( $instance[ 'selected_post' ] ) ? $instance[ 'selected_post' ] : "";
		$selected_post_field_name = $this->get_field_name( 'selected_post' );
		$selected_post_field_id = $this->get_field_id( 'selected_post' );
		$posts = get_posts( array( 'type' => 'post' ,
					   'orderby' => 'date' ,
					   'posts_per_page' => '100' ,
		) );
		?>
		<p>
			<label for="<?php echo $selected_post_field_id; ?>">
				 Post to display:
			</label>
		<?php
		if ( $posts ) :
			?>
			<select name="<?php echo $selected_post_field_name; ?>" id="<?php echo $selected_post_field_id; ?>" class="widefat appw-post-selector">
				<option value="appw_carousel_recent" <?php selected( $selected_post , 'appw_carousel_recent' , true ); ?>>
					<?php _e( 'Carousel of recent posts' , 'adapter-post-preview' ); ?>
				</option>
				<?php foreach( $posts as $post ) : ?>
					<option value="<?php echo $post->ID ?>" <?php selected( $selected_post , $post->ID , true ); ?>>
						<?php echo $post->post_title; ?>
					</option>
				<?php endforeach; ?>
			</select>
		<?php else :
			_e( 'No posts on your site. Please write one.' , 'adapter-post-preview' );
		endif;
		?>
		</p>
		<?php
	}

	function update( $new_instance , $previous_instance ) {
		$instance = $previous_instance;
		$selected_post = isset( $new_instance[ 'selected_post' ] ) ? $new_instance[ 'selected_post' ] : "";
		if ( appw_is_valid_value( $selected_post ) ) {
			$instance[ 'selected_post' ] = $selected_post;
		}
		return $instance;
	}

	function widget( $args , $instance ) {	// todo : remove extract
		extract( $args );
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

		echo $before_widget . $markup . $after_widget;
	}

	private function get_carousel_markup() {
		$post_preview_ids = $this->get_post_ids_for_carousel();
		$post_preview_container = $this->get_all_post_preview_markup( $post_preview_ids );

		$post_carousel = new APP_Carousel();
		foreach( $post_preview_container as $post_preview ) {
			$post_carousel->add_post_markup( $post_preview );
		}
		$markup = $post_carousel->get();
		return $markup;
	}

	private function get_post_ids_for_carousel() {
		$posts_per_page = apply_filters( 'bwp_number_of_posts_in_carousel' , 5 );
		global $post;
		$posts_for_carousel = get_posts( array(
					'type' => 'post' ,
					'orderby' => 'date' ,
					'posts_per_page' => $posts_per_page ,
					'exclude' => isset( $post ) ? $post->ID : "" ,
		) );

		$post_ids = array();
		foreach( $posts_for_carousel as $post_for_carousel ) {
			if ( has_post_thumbnail( $post_for_carousel->ID ) ) {
			array_push( $post_ids , $post_for_carousel->ID );
			}
		}
		return $post_ids;
	}

	private function get_all_post_preview_markup( $post_ids ) {
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

	private function get_markup_for_single_post( $post_id ) {
		$post = get_post( $post_id );
		setup_postdata( $post );
		$post_markup =	appw_get_single_post_preview_markup( $post );
		wp_reset_postdata();
		return $post_markup;
	}

	private function get_single_post_preview_without_carousel( $post_id ) {
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
	$thumbnail = get_the_post_thumbnail( $post->ID , 'medium' , array( 'class' => 'img-rounded img-responsive'	) );
	$title = '<div class="post-title"><h2>' . get_the_title( $post->ID ) . '</h2></div>';

	$raw_excerpt = get_the_excerpt();
	$excerpt_length = apply_filters( 'appw_excerpt_length' , 30 );
	$filtered_excerpt = '<p>' . wp_trim_words( $raw_excerpt , $excerpt_length , '...' ) . '</p>';

	$permalink = get_permalink( $post->ID );
	$link_text = apply_filters( 'appw_link_text' , __( 'Read more' , 'adapter-post-preview' ) );
	$button = "<a class='btn btn-primary btn-med' href='{$permalink}'>{$link_text}</a>";

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