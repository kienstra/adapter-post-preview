<?php
/**
 * Main class for the Adapter Post Preview plugin.
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

/**
 * Main plugin class.
 */
class Plugin {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.1';

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	const SLUG = 'adapter-post-preview';

	/**
	 * The slug of the stylesheet.
	 *
	 * @var string
	 */
	const STYLE = 'app-style';

	/**
	 * The class of the widget.
	 *
	 * @var string
	 */
	const WIDGET_CLASS = 'Adapter_Post_Widget';

	/**
	 * The URL of the plugin.
	 *
	 * @var string
	 */
	public $location;

	/**
	 * Instantiated plugin classes.
	 *
	 * @var \stdClass
	 */
	public $components;

	/**
	 * The PHP classes.
	 *
	 * @var array
	 */
	public $classes = array( 'adapter-post-widget', 'carousel' );

	/**
	 * The instance of this class.
	 *
	 * @var Plugin
	 */
	public static $instance;

	/**
	 * Gets the instance of this plugin.
	 *
	 * @return Plugin $instance The plugin instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance instanceof Plugin ) {
			self::$instance = new Plugin();
		}
		return self::$instance;
	}

	/**
	 * Initiates this plugin.
	 *
	 * Load the files, instantiate the classes, and call their init() methods.
	 * And register the main plugin actions.
	 *
	 * @return void
	 */
	public function init() {
		$this->location = plugins_url( self::SLUG );
		$this->load_files();
		$this->add_actions();
	}

	/**
	 * Loads the plugin files.
	 *
	 * @return void
	 */
	public function load_files() {
		foreach ( $this->classes as $class ) {
			include_once __DIR__ . "/class-{$class}.php";
		}
	}

	/**
	 * Adds the plugin actions.
	 *
	 * @return void
	 */
	public function add_actions() {
		add_action( 'init', array( $this, 'textdomain' ) );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ) );
	}

	/**
	 * Loads the plugin's textdomain.
	 *
	 * @return void
	 */
	public function textdomain() {
		load_plugin_textdomain( self::SLUG );
	}

	/**
	 * Register the plugin's widget.
	 *
	 * @return void
	 */
	public function register_widget() {
		register_widget( __NAMESPACE__ . '\\' . self::WIDGET_CLASS );
	}

	/**
	 * Enqueue the widget stylesheet.
	 *
	 * @return void
	 */
	public function enqueue_style() {
		wp_enqueue_style(
			self::STYLE,
			$this->location . '/css/' . self::STYLE . '.css',
			array(),
			self::VERSION
		);
	}

}
