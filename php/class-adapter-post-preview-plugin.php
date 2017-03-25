<?php
/**
 * Class file for Adapter_Post_Preview_Plugin
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

/**
 * Class Adapter_Post_Preview_Plugin
 */
class Adapter_Post_Preview_Plugin {

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	public $slug = 'adapter-post_preview';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.0.2';

	/**
	 * Construct the class.
	 */
	public function __construct() {
		require_once dirname( __FILE__ ) . '/class-app-carousel.php';
		require_once dirname( __FILE__ ) . '/class-adapter-post-widget.php';
		add_action( 'init' , array( $this, 'plugin_localization' ) );
		add_action( 'widgets_init' , array( $this, 'register_widget' ) );
		add_action( 'wp_enqueue_scripts' , 'enqueue_styles' );
	}

	/**
	 * Load the textdomain for the plugin, enabling translation.
	 *
	 * @return void.
	 */
	public function plugin_localization() {
		load_plugin_textdomain( 'adapter-post-preview' , false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Register the Adapter Post Preview widget.
	 *
	 * @return void.
	 */
	public function register_widget() {
		register_widget( 'AdapterPostPreview\Adapter_Post_Widget' );
	}

	/**
	 * Enqueue styles for the widget.
	 *
	 * @return void.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->slug . '-style', plugins_url( '/css/app-style.css' , __FILE__ ) , array() , $this->version );
	}

}
