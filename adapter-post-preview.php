<?php
/**
 * Plugin bootstrap file.
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

/*
Plugin Name: Adapter Post Preview
Plugin URI: www.ryankienstra.com/plugins/adapter-post-preview
Description: Create a widget with a post's featured image, headline, excerpt, and link. If you have Bootstrap 3, make widget with a carousel of recent posts.

Version: 1.0.3
Author: Ryan Kienstra
Author URI: www.ryankienstra.com
License: GPL2
*/

require_once dirname( __FILE__ ) . '/php/class-adapter-post-preview-plugin.php';

global $adapter_post_preview_plugin;
$adapter_post_preview_plugin = new Adapter_Post_Preview_Plugin();
