<?php
/**
 * Class file for APP_Carousel
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

/**
 * Class APP_Carousel.
 *
 * Builds and echoes a modal carousel for each gallery.
 */
class APP_Carousel {

	/**
	 * ID of the instance of this class.
	 *
	 * @var number
	 */
	public static $instance_id = 1;

	/**
	 * ID of the carousel.
	 *
	 * @var string
	 */
	public $carousel_id;

	/**
	 * Markup of the posts inside the carousel.
	 *
	 * @var string
	 */
	public $carousel_inner_items;

	/**
	 * Number of posts in the carousel.
	 *
	 * @var number
	 */
	public $number_of_inner_items;

	/**
	 * Markup of the carousel indicators.
	 *
	 * @var string
	 */
	public $carousel_indicators;

	/**
	 * Index of the post in the carousel.
	 *
	 * @var number
	 */
	public $slide_to_index;

	/**
	 * Markup of the post.
	 *
	 * @var string
	 */
	protected $post_markup;

	/**
	 * Instantiate the Bootstrap carousel.
	 */
	public function __construct() {
		$this->carousel_id = 'appw-carousel-' . self::$instance_id;
		self::$instance_id++;
		$this->carousel_inner_items = '';
		$this->number_of_inner_items = 0;
		$this->carousel_indicators = '';
		$this->slide_to_index = 0;
	}

	/**
	 * Add the post markup to the carousel container.
	 *
	 * @param string $post_preview_container Markup for the post preview.
	 * @return void.
	 */
	public function add_post_markup( $post_preview_container ) {
		foreach ( $post_preview_container as $post_preview ) {
			$this->append_post_markup_to_inner_items( $post_preview );
			$this->append_to_carousel_indicators();
			$this->number_of_inner_items++;
		}
	}

	/**
	 * Add Bootstrap inner items markup for each post.
	 *
	 * @param string $post_markup Markup to append to the carousel inner items.
	 * @return void.
	 */
	public function append_post_markup_to_inner_items( $post_markup ) {
		$is_active = ( 0 === $this->slide_to_index ) ? 'active' : '';
		$this->carousel_inner_items .=
		'<div class="item ' . esc_attr( $is_active ) . '">'
			. wp_kses_post( $post_markup )
		. '</div>';
	}

	/**
	 * Add markup to carousel indicators for each post.
	 *
	 * @return void.
	 */
	public function append_to_carousel_indicators() {
		$is_active = ( 0 === $this->slide_to_index ) ? 'active' : '';
		$list_item = '<li class="' . esc_attr( $is_active ) . '" data-target="#' . esc_attr( $this->carousel_id ) . '" data-slide-to="' . esc_attr( $this->slide_to_index ) . '" ></li>';
		$this->carousel_indicators .= $list_item;
		$this->slide_to_index++;
	}

	/**
	 * Conditionally get the carousel controls.
	 *
	 * @return string $control_markup Bootstrap carousel control markup.
	 */
	public function maybe_get_controls() {
		if ( $this->has_more_than_one_post() ) {
			return '<a class="left carousel-control" href="#' . esc_attr( $this->carousel_id ) . '" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left"></span>
					</a>
					<a class="right carousel-control" href="#' . esc_attr( $this->carousel_id ) . '" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right"></span>
					</a>';
		} else {
			return '';
		}
	}

	/**
	 * Conditionally return Bootstrap indicator markup.
	 *
	 * @return string $indicator_markup Bootstrap carousel indicator markup.
	 */
	public function maybe_get_indicators() {
		if ( $this->has_more_than_one_post() ) {
			return '<ol class="carousel-indicators">'
						. $this->carousel_indicators
				. '</ol>';
		} else {
			return '';
		}
	}

	/**
	 * Get the full markup of the Bootstrap carousel.
	 *
	 * @return string $markup Full Bootstrap carousel markup, with the posts.
	 */
	public function get() {
		return '<div id="' . esc_attr( $this->carousel_id ) . '" class="carousel slide">'
					. $this->maybe_get_indicators()
				. '<div class="carousel-inner">'
					. $this->carousel_inner_items
				. '</div>'
				. $this->maybe_get_controls()
			. '</div>';
	}

	/**
	 * Whether the carousel has more than a single post.
	 *
	 * @return boolean $has_more_than_one Whether the Bootstrap carousel has more than one post.
	 */
	public function has_more_than_one_post() {
		return ( 1 < $this->number_of_inner_items );
	}

}
