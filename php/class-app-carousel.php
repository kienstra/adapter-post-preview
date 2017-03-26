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

	public function __construct() {
		$this->carousel_id = 'appw-carousel-' . self::$instance_id;
		self::$instance_id++;
		$this->carousel_inner_items = '';
		$this->number_of_inner_items = 0;
		$this->carousel_indicators = '';
		$this->slide_to_index = 0;
	}

	public function add_post_markup( $post_preview_container ) {
		foreach ( $post_preview_container as $post_preview ) {
			$this->append_post_markup_to_inner_items( $post_preview );
			$this->append_to_carousel_indicators();
			$this->number_of_inner_items++;
		}
	}

	public function append_post_markup_to_inner_items( $post_markup ) {
		$is_active = ( 0 === $this->slide_to_index ) ? 'active' : '';
		$this->carousel_inner_items .=
		"<div class='item {$is_active}'>
			{$post_markup}
		</div> \n";
	}

	public function append_to_carousel_indicators() {
		$is_active = ( 0 === $this->slide_to_index ) ? 'active' : '';

		$this->carousel_indicators .=
		"<li class='{$is_active}' data-target='#{$this->carousel_id}' data-slide-to='{$this->slide_to_index}' ></li>";
		$this->slide_to_index++;
	}

	public function maybe_get_controls() {
		if ( $this->number_of_inner_items > 1 ) {
			return "<a class='left carousel-control' href='#{$this->carousel_id}' data-slide='prev'><span class='glyphicon glyphicon-chevron-left'></span></a>
				<a class='right carousel-control' href='#{$this->carousel_id}' data-slide='next'><span class='glyphicon glyphicon-chevron-right'></span></a>";
		}
	}

	public function maybe_get_indicators() {
		if ( $this->number_of_inner_items > 1 ) {
			return "<ol class='carousel-indicators'>
					{$this->carousel_indicators}
				</ol>\n";
		}
	}

	public function get() {
		$controls = $this->maybe_get_controls();
		$indicators = $this->maybe_get_indicators();
		return "<div id='{$this->carousel_id}' class='carousel slide'>
				{$indicators}
				<!-- Posts -->
				<div class='carousel-inner'>
					{$this->carousel_inner_items}
				</div>
				{$controls}
			</div><!-- .carousel --> \n";
	}
}
