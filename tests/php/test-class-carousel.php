<?php
/**
 * Tests for class Carousel.
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

/**
 * Tests for class Carousel.
 */
class Test_Carousel extends \WP_UnitTestCase {

	/**
	 * Instance of Carousel.
	 *
	 * @var Carousel
	 */
	public $instance;

	/**
	 * Mock post previews.
	 *
	 * @var string[]
	 */
	public $mock_post_previews = array( 'foo', 'bar' );

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->instance = new Carousel( $this->mock_post_previews );
	}

	/**
	 * Test __construct.
	 *
	 * @covers Carousel::__construct()
	 */
	public function test___construct() {
		$this->assertEquals( __NAMESPACE__ . '\\Carousel', get_class( $this->instance ) );
		$this->assertEquals( 'appw-carousel-' . strval( Carousel::$instance_id - 1 ), $this->instance->carousel_id );
	}

	/**
	 * Test get.
	 *
	 * @covers Carousel::get()
	 */
	public function test_get() {
		$carousel = $this->instance->get();
		$this->assertContains( '<ol class="carousel-indicators">', $carousel );
		$this->assertContains( $this->instance->carousel_id, $carousel );
		foreach ( $this->mock_post_previews as $post_preview ) {
			$this->assertContains( $post_preview, $carousel );
		}
		$this->assertContains( '<a class="left carousel-control"', $carousel );

		$single_post_preview = 'Example post';
		$this->instance      = new Carousel( array( $single_post_preview ) );
		$carousel            = $this->instance->get();

		// This only has one post to preview, so it should not have indicators or controls.
		$this->assertNotContains( '<ol class="carousel-indicators">', $carousel );
		$this->assertNotContains( '<a class="left carousel-control"', $carousel );
		$this->assertContains( $single_post_preview, $carousel );
	}
}
