<?php

// Builds and echoes a modal carousel for each gallery
class APP_Carousel {

	private $carousel_id;
	private $carousel_inner_items;
	private $number_of_inner_items;
	private $carousel_indicators;
	private $slide_to_index;
	private $post_markup;
	private static $instance_id = 1;

	public function __construct() {
		$this->carousel_id = 'appw-carousel-' . self::$instance_id;
		$this->carousel_inner_items = '';
		$this->number_of_inner_items = 0;
		$this->carousel_indicators = '';
		$this->slide_to_index = 0;
		self::$instance_id++;
	}

	public function add_post_markup( $post_markup ) {
		$this->append_post_markup_to_inner_items( $post_markup );
		$this->append_to_carousel_indicators();
		$this->number_of_inner_items++;
	}

	private function append_post_markup_to_inner_items( $post_markup ) {
		$is_active = (0 == $this->slide_to_index ) ? 'active' : '';
		$this->carousel_inner_items .=
		"<div class='item {$is_active}'>
			{$post_markup}
		</div> \n";
	}

	private function append_to_carousel_indicators(){
		$is_active = (0 == $this->slide_to_index ) ? 'active' : '';

		$this->carousel_indicators .=
		"<li class='{$is_active}' data-target='#{$this->carousel_id}' data-slide-to='{$this->slide_to_index}' ></li>";
		$this->slide_to_index++;
	}

	private function maybe_get_controls() {
		if ( $this->number_of_inner_items > 1 ) {
			return "<a class='left carousel-control' href='#{$this->carousel_id}' data-slide='prev'><span class='glyphicon glyphicon-chevron-left'></span></a>
				<a class='right carousel-control' href='#{$this->carousel_id}' data-slide='next'><span class='glyphicon glyphicon-chevron-right'></span></a>";
		}
	}

	private function maybe_get_indicators() {
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
/* end class APP_Carousel */