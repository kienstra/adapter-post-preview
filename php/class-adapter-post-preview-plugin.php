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
	public $plugin_slug = 'adapter-post-preview';

	/**
	 * Construct the class.
	 */
	public function __construct() {
		require_once dirname( __FILE__ ) . '/class-app-carousel.php';
		require_once dirname( __FILE__ ) . '/class-adapter-post-widget.php';
		add_action( 'init' , array( $this, 'plugin_localization' ) );
		add_action( 'widgets_init' , array( $this, 'register_widget' ) );
	}

	/**
	 * Load the textdomain for the plugin, enabling translation.
	 *
	 * @return void.
	 */
	public function plugin_localization() {
		load_plugin_textdomain( $this->plugin_slug, false, $this->plugin_slug . '/languages' );
	}

	/**
	 * Register the Adapter Post Preview widget.
	 *
	 * @return void.
	 */
	public function register_widget() {
		register_widget( 'AdapterPostPreview\Adapter_Post_Widget' );
	}

}
