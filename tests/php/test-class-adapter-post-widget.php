<?php
/**
 * Tests for class Adapter_Post_Widget.
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

/**
 * Tests for class Adapter_Post_Widget.
 */
class Test_Adapter_Post_Widget extends \WP_UnitTestCase {

	/**
	 * Instance of widget.
	 *
	 * @var Adapter_Post_Widget
	 */
	public $widget;

	/**
	 * Mock post IDs for testing.
	 *
	 * @var array
	 */
	public $mock_post_ids = array();

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		$this->widget = new Adapter_Post_Widget();
	}

	/**
	 * Test construct.
	 *
	 * @covers Adapter_Post_Widget::__construct()
	 */
	public function test_construct() {
		$this->assertEquals(
			array(
				'classname'                   => Plugin::SLUG,
				'customize_selective_refresh' => true,
				'description'                 => 'Show a carousel of recent posts, or a selected one',
			),
			$this->widget->widget_options
		);
		$this->assertEquals( Adapter_Post_Widget::ID_BASE, $this->widget->id_base );
		$this->assertEquals( 'Adapter Post Preview', $this->widget->name );
	}

	/**
	 * Test form.
	 *
	 * @covers Adapter_Post_Widget::form()
	 */
	public function test_form() {
		ob_start();
		$this->widget->form( array() );
		$this->assertContains( 'There are no posts on your site. Please create one.', ob_get_clean() );

		$this->create_mock_posts();
		$selected_post_id = reset( $this->mock_post_ids );
		ob_start();
		$this->widget->form( array( Adapter_Post_Widget::SELECTED_POST => $selected_post_id ) );
		$output = ob_get_clean();
		foreach ( $this->mock_post_ids as $post_id ) {
			$this->assertContains( '<option value="' . $post_id, $output );
		}
		$this->assertContains( '<option value="' . $selected_post_id . '"  selected=\'selected\'', $output );
		$this->assertContains( 'Post to display:', $output );

		ob_start();
		$this->widget->form( array( Adapter_Post_Widget::SELECTED_POST => Adapter_Post_Widget::DISPLAY_CAROUSEL ) );
		$output = ob_get_clean();
		$this->assertContains( '<option value="' . Adapter_Post_Widget::DISPLAY_CAROUSEL . '"  selected=\'selected\'>', $output );
	}

	/**
	 * Test update.
	 *
	 * @covers Adapter_Post_Widget::update()
	 */
	public function test_update() {
		// The key and value are invalid, and shouldn't be updated.
		$invalid_value = 'Foo';
		$new_instance  = $this->widget->update(
			array(
				'unexpected_key' => $invalid_value,
			),
			array()
		);
		$this->assertEquals( array(), $new_instance );

		// The key is correct, but its value is invalid.
		$new_instance = $this->widget->update(
			array(
				Adapter_Post_Widget::SELECTED_POST => $invalid_value,
			),
			array()
		);
		$this->assertEquals( array(), $new_instance );

		// The key and value are correct, and should be updated.
		$valid_value          = 145;
		$new_instance         = array(
			Adapter_Post_Widget::SELECTED_POST => $valid_value,
		);
		$updated_new_instance = $this->widget->update(
			$new_instance,
			array()
		);
		$this->assertEquals( $new_instance, $updated_new_instance );
	}

	/**
	 * Test widget.
	 *
	 * @covers Adapter_Post_Widget::widget()
	 */
	public function test_widget() {
		global $post;
		$post          = $this->factory()->post->create_and_get(); // WPCS: global override ok.
		$before_widget = '<section>';
		$after_widget  = '</section>';
		$args          = compact( 'before_widget', 'after_widget' );
		$instance      = array(
			Adapter_Post_Widget::SELECTED_POST => $post->ID,
		);
		$this->create_mock_posts();

		// This should display a carousel.
		$instance = array( Adapter_Post_Widget::SELECTED_POST => Adapter_Post_Widget::DISPLAY_CAROUSEL );
		ob_start();
		$this->widget->widget( $args, $instance );
		$output = ob_get_clean();
		$this->assertContains( $before_widget, $output );
		$this->assertContains( $after_widget, $output );
		$this->assertContains( '<div class=\'carousel-inner\'>', $output );

		// This should display a single post preview.
		$mock_post_id = reset( $this->mock_post_ids );
		$instance     = array( Adapter_Post_Widget::SELECTED_POST => $mock_post_id );
		ob_start();
		$this->widget->widget( $args, $instance );
		$output = ob_get_clean();
		$this->assertContains( get_the_title( $mock_post_id ), $output );
		$this->assertContains( get_the_permalink( $mock_post_id ), $output );
	}

	/**
	 * Test get_carousel_markup.
	 *
	 * @covers Adapter_Post_Widget::get_carousel_markup()
	 */
	public function test_get_carousel_markup() {
		$this->create_mock_posts();
		$post_previews   = $this->widget->get_all_post_preview_markup( $this->widget->get_post_ids_for_carousel() );
		$carousel_markup = $this->widget->get_carousel_markup();
		foreach ( $post_previews as $post_preview ) {
			$this->assertContains( $post_preview, $carousel_markup );
		}
	}

	/**
	 * Test post_ids_for_carousel.
	 *
	 * @covers Adapter_Post_Widget::post_ids_for_carousel()
	 */
	public function test_get_post_ids_for_carousel() {
		$this->create_mock_posts();
		$post_ids = $this->widget->get_post_ids_for_carousel();
		$this->assertEmpty( array_diff( $post_ids, $this->mock_post_ids ) );

		// The post preview shouldn't show on the URL for that post.
		$first_post_id = reset( $post_ids );
		$this->go_to( get_permalink( $first_post_id ) );
		$this->assertFalse( in_array( $first_post_id, $this->widget->get_post_ids_for_carousel(), true ) );
	}

	/**
	 * Test get_all_post_preview_markup.
	 *
	 * @covers Adapter_Post_Widget::get_all_post_preview_markup()
	 */
	public function test_get_all_post_preview_markup() {
		$this->create_mock_posts();
		$all_markup = $this->widget->get_all_post_preview_markup( $this->mock_post_ids );
		foreach ( $this->mock_post_ids as $mock_post_id ) {
			$this->assertTrue( in_array( $this->widget->get_single_post_preview_markup( get_post( $mock_post_id ) ), $all_markup, true ) );
		}

		// When passing an int that isn't and ID of a post, it shouldn't add anything to the post markup.
		$mock_post_ids_with_invalid_id       = array_merge( $this->mock_post_ids, array( PHP_INT_MAX ) );
		$all_markup_after_passing_invalid_id = $this->widget->get_all_post_preview_markup( $mock_post_ids_with_invalid_id );
		$this->assertEquals( $all_markup, $all_markup_after_passing_invalid_id );
	}

	/**
	 * Test get_single_post_preview_markup.
	 *
	 * @covers Adapter_Post_Widget::get_single_post_preview_markup()
	 */
	public function test_get_single_post_preview_markup() {
		$this->create_mock_posts();
		$mock_post_id = reset( $this->mock_post_ids );
		$mock_post    = get_post( $mock_post_id );
		$markup       = $this->widget->get_single_post_preview_markup( $mock_post );

		$expected_thumbnail = get_the_post_thumbnail( $mock_post_id, 'medium', array( 'class' => 'img-rounded img-responsive' ) );
		$this->assertContains( $expected_thumbnail, $markup );
		$this->assertContains( '<div class="post-title"><h2>', $markup );
		$this->assertContains( wp_trim_words( get_the_excerpt( $mock_post_id ), 30, '...' ), $markup );
		$this->assertContains( get_permalink( $mock_post_id ), $markup );
		$this->assertContains( '<a class="btn btn-primary btn-med"', $markup );
	}

	/**
	 * Create mock posts for testing.
	 *
	 * @covers Adapter_Post_Widget::create_mock_posts()
	 */
	public function create_mock_posts() {
		$post_count = 10;
		for ( $i = 0; $i < $post_count; $i++ ) {
			$post_id               = $this->factory()->post->create();
			$this->mock_post_ids[] = $post_id;
			update_post_meta( $post_id, '_thumbnail_id', $i );
		}
	}
}
