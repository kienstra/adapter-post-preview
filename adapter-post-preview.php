<?php
/**
 * Instantiates the Adapter Post Preview plugin.
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

/*
Plugin Name: Adapter Post Preview
Plugin URI: http://ryankienstra.com/plugins/adapter-post-preview
Description: Create a widget with a post's featured image, headline, excerpt, and link. If you have at least Bootstrap 3, you can display a carousel of recent posts.

Version: 1.1
Author: Ryan Kienstra
Author URI: www.ryankienstra.com
License: GPL2
*/

require_once dirname( __FILE__ ) . '/php/class-plugin.php';
$plugin = Plugin::get_instance();
$plugin->init();
