<?php
/**
 * Creates a Bootstrap carousel.
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

/**
 * Creates a Bootstrap carousel for the widget.
 */
class Carousel {

	/**
	 * The ID of the Carousel instance, incremented each time this is instantiated.
	 *
	 * @var int
	 */
	public static $instance_id = 1;

	/**
	 * The ID of the carousel.
	 *
	 * @var string
	 */
	public $carousel_id;

	/**
	 * The post preview markup to add to the carousel.
	 *
	 * @var string[]
	 */
	public $post_previews;

	/**
	 * Carousel constructor.
	 *
	 * @param string[] $post_previews The post preview markup.
	 */
	public function __construct( $post_previews ) {
		$this->carousel_id   = 'appw-carousel-' . strval( self::$instance_id++ );
		$this->post_previews = $post_previews;
	}

	/**
	 * Gets the carousel markup.
	 *
	 * @return string
	 */
	public function get() {
		ob_start();
		include __DIR__ . '/templates/carousel.php';
		return ob_get_clean();
	}
}
