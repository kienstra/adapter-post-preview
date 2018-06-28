<?php
/**
 * Tests for class Plugin.
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

/**
 * Tests for class Plugin.
 */
class Test_Plugin extends \WP_UnitTestCase {

	/**
	 * Instance of plugin.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function setUp() {
		parent::setUp();
		wp_maybe_load_widgets();
		$this->plugin = Plugin::get_instance();
	}

	/**
	 * Test get_instance().
	 *
	 * @covers Plugin::get_instance().
	 */
	public function test_get_instance() {
		$this->assertEquals( Plugin::get_instance(), $this->plugin );
		$this->assertEquals( __NAMESPACE__ . '\Plugin', get_class( Plugin::get_instance() ) );
		$this->assertEquals( plugins_url( Plugin::SLUG ), $this->plugin->location );
	}

	/**
	 * Test init().
	 *
	 * @covers Plugin::init().
	 */
	public function test_init() {
		$this->plugin->init();
		$this->assertTrue( class_exists( __NAMESPACE__ . '\\' . Plugin::WIDGET_CLASS ) );
	}

	/**
	 * Test load_files().
	 *
	 * @covers Plugin::load_files().
	 */
	public function test_load_files() {
		$classes = array(
			'Adapter_Post_Widget',
			'APP_Carousel',
		);

		foreach ( $classes as $class ) {
			$this->assertTrue( class_exists( __NAMESPACE__ . '\\' . $class ) );
		}
	}

	/**
	 * Test add_actions().
	 *
	 * @covers Plugin::add_actions().
	 */
	public function test_add_actions() {
		$this->plugin->add_actions();
		$this->assertEquals( 10, has_action( 'init', array( $this->plugin, 'textdomain' ) ) );
		$this->assertEquals( 10, has_action( 'widgets_init', array( $this->plugin, 'register_widget' ) ) );
		$this->assertEquals( 10, has_action( 'wp_enqueue_scripts', array( $this->plugin, 'enqueue_style' ) ) );
	}

	/**
	 * Test textdomain().
	 *
	 * @covers Plugin::textdomain().
	 */
	public function test_textdomain() {
		$this->plugin->textdomain();
		$this->assertNotEquals( false, did_action( 'load_textdomain' ) );
	}

	/**
	 * Test register_widget().
	 *
	 * @covers Plugin::register_widget().
	 */
	public function test_register_widget() {
		global $wp_widget_factory;
		$this->plugin->register_widget();
		$this->assertTrue( isset( $wp_widget_factory->widgets[ __NAMESPACE__ . '\\' . Plugin::WIDGET_CLASS ] ) );
	}

	/**
	 * Test enqueue_style().
	 *
	 * @covers Plugin::enqueue_style().
	 */
	public function test_enqueue_style() {
		$this->plugin->enqueue_style();
		$style = wp_styles()->registered[ Plugin::STYLE ];
		$this->assertEquals( '_WP_Dependency', get_class( $style ) );
		$this->assertEquals( array(), $style->deps );
		$this->assertEquals( Plugin::STYLE, $style->handle );
		$this->assertEquals( $this->plugin->location . '/css/app-style.css', $style->src );
		$this->assertEquals( Plugin::VERSION, $style->ver );
	}

}
