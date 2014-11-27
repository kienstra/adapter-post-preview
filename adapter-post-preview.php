<?php

/*
Plugin Name: Adapter Post Preview
Plugin URI: www.ryankienstra.com/plugins/adapter-post-preview
Description: Create a widget with a post's featured image, headline, excerpt, and link. If you have Bootstrap 3, make widget with a carousel of recent posts.

Version: 1.0.2
Author: Ryan Kienstra
Author URI: www.ryankienstra.com
License: GPL2
*/

define( 'APPW_PLUGIN_SLUG' , 'adapter-post_preview' );
define( 'APPW_PLUGIN_VERSION' , '1.0.2' );

load_plugin_textdomain( 'adapter-post-preview' , false , basename( dirname( __FILE__ ) ) . '/languages' );

add_action( 'plugins_loaded' , 'appw_get_included_files' );
function appw_get_included_files() {
	include_once( plugin_dir_path( __FILE__ ) . 'includes/class-app-carousel.php' );
	include_once( plugin_dir_path( __FILE__ ) . 'includes/class-adapter-post-widget.php' );
}

add_action( 'widgets_init' , 'appw_register_widget' );
function appw_register_widget() {
	register_widget( 'Adapter_Post_Widget' );
}

add_action( 'wp_enqueue_scripts' , 'appw_enqueue_stylesheet' );
function appw_enqueue_stylesheet() {
	wp_enqueue_style( APPW_PLUGIN_SLUG . '-style', plugins_url( '/css/app-style.css' , __FILE__ ) , array() , APPW_PLUGIN_VERSION );
}