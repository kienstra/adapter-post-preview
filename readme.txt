=== Adapter Post Preview ===
Contributors: ryankienstra
Donate link: http://jdrf.org/get-involved/ways-to-donate/
Tags: widgets, post, Bootstrap, mobile, responsive, 
Requires at least: 3.8
Tested up to: 4.0 
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A widget with a post preview, or a carousel of the most recent posts.

== Description ==

* Creates a widget with the post's featured image, headline, excerpt, and link.
* To see the carousel of posts, you must have Twitter Bootstrap 3 and Glyphicons.
* Hides the widget if the post is a single post on the page. For example, if you are on the "Hello World" page, you won't see the widget with a preview of "Hello World."
* The carousel won't show posts that don't have an image.

== Installation ==

1. Upload the adapter-post-preview directory to your /wp-content/plugins directory.
1. In the "Plugins" menu, find "Adapter Post Preview," and click "Activate."
1. Add a "Post Preview" widget by going to the admin menu and clicking "Appearance" > "Widgets"
1. Select the post you want. You must have Bootstrap to use the carousel.

== Frequently Asked Questions ==

= What does this require? =

The carousel of recent posts requires Twitter Bootstrap 3 with Glyphicons. 

= How can I change the text in the post link? =

Put the following in your functions.php file:
`apply_filters( 'appw_link_text' , 'my_appw_link_text' ) ;
 function my_appw_link_text( $text ) {
   return 'Keep reading' ; // or your own text
 }`
 
== Screenshots ==

1. A "Post Preview" widget in the sidebar.
2. A carousel of recent posts, which requires Bootstrap 3.

== Changelog ==

= 1.0.0 =
* First version

== Upgrade Notice ==

* N/A, first version
