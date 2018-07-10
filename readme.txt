=== Adapter Post Preview ===
Contributors: ryankienstra
Donate link: http://jdrf.org/get-involved/ways-to-donate/
Tags: widgets, post, Bootstrap, mobile, responsive,
Requires at least: 3.8
Tested up to: 4.9
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 5.4

Show your best posts in any widget area. Creates a widget with a post preview, or a carousel of the most recent posts.

== Description ==

* Creates a widget with the post's featured image, headline, excerpt, and link.
* To see the carousel of posts, you must have Bootstrap 3 or later and Glyphicons.
* Hides the widget if the post is a single post on the page. For example, if you are on the "Hello World" page, you won't see the widget with a preview of "Hello World."
* The carousel won't show posts that don't have an image.

[youtube http://www.youtube.com/watch?v=mXSKjlVrh7I]

== Installation ==

1. Upload the adapter-post-preview directory to your /wp-content/plugins directory.
1. In the "Plugins" menu, find "Adapter Post Preview," and click "Activate."
1. Add a "Post Preview" widget by going to the admin menu and clicking "Appearance" > "Widgets"
1. Select the post you want. You must have Bootstrap to use the carousel.

== Frequently Asked Questions ==

= What does this require? =

The carousel of recent posts requires Bootstrap 3 or later and Glyphicons.

= How can I change the text in the post link? =

Put the following in your functions.php file:
`add_filter( 'appw_link_text', function( $text ) { return 'Keep reading'; } ) // Or your own text.`

== Screenshots ==

1. A "Post Preview" widget in the sidebar.
2. A carousel of recent posts, which requires Bootstrap 3 or later.

== Changelog ==

= 1.1 =
* Add PHPUnit testing to ensure stability. See [#2](https://github.com/kienstra/adapter-post-preview/issues/2).
* Fix excerpt output when outside the loop. See [#10](https://github.com/kienstra/adapter-post-preview/issues/10).
* Refactor plugin bootstrapping. See [#4](https://github.com/kienstra/adapter-post-preview/issues/4).
* Refactor carousel class to mainly use a template, add PHPUnit tests. See [#9](https://github.com/kienstra/adapter-post-preview/pull/9).
* Add wp-dev-lib as a submodule, with configuration files. See [#1](https://github.com/kienstra/adapter-post-preview/issues/1).

See the [v1.1 project](https://github.com/kienstra/adapter-post-preview/projects/1).

= 1.0.2 =
* Fixed height in mobile display.

= 1.0.1 =
* Fixed a bug in Internet Explorer display of the carousel.

= 1.0.0 =
* First version.

== Upgrade Notice ==

= 1.0.2 =
Upgrade if you use the carousel. It now has enough height on mobile devices.

= 1.0.1 =
No need to update unless you use the carousel. This version fixes its display in Internet Explorer.
