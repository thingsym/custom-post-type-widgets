=== Custom Post Type Widgets ===

Contributors: thingsym
Donate link: 
Link: https://github.com/thingsym/custom-post-type-widgets
Tags: widget, custom post type, taxonomy
Requires at least: 3.4
Tested up to: 4.2
Stable tag: 1.0.3
License: GPL2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This WordPress plugin adds default custom post type widgets.

== Description ==

This WordPress plugin adds default custom post type widgets.
You can filter by registered Custom Post Type or Taxonomy on widgets.

= Descriptions of Widgets =

= Recent Posts (Custom Post Type) =

display a list of the most recent custom posts.

* **Title** - description that appears over the list of recent custom posts.
* **Post Type** - if selected, filter by a custom post type. (e.g. post).
* **Number of posts to show (at most 15)** - enter the number of posts to display.
* **Display post date?** - if checked, display post date.
* **Class Name** - widget_recent_entries

= Archives (Custom Post Type) =

display a list of archive links for each month that has custom posts.

* **Title** - description that appears over the list of archive links.
* **Post Type** - if selected, filter by a custom post type. (e.g. post).
* **Display as dropdown** - if checked, this box causes the archives to be displayed in a drop-down box.
* **Show post counts** - if checked, this box causes a count of the number of posts for each archive period.
* **Class Name** - widget_archive

= Categories (Custom Post Type) =

display a list of categories that has custom posts.

* **Title** - description that appears over the list of categories.
* **Taxonomy** - if selected, filter a custom taxonomy (e.g. category).
* **Display as dropdown** - if checked, this box causes the categories to be displayed in a dropdown box.
* **Show post counts** - if checked, this box causes the count of the number of posts to display with each category.
* **Show hierarchy** - if checked, shows parent/child relationships in an indented manner.
* **Class Name** - widget_categories

= Calendar (Custom Post Type) =

display a calendar of the current month.

* **Title** - description that appears over the calendar.
* **Post Type** - if selected, filter by a custom post type. (e.g. post).
* **Class Name** - widget_calendar

= Recent Comments (Custom Post Type) =

display a list of the most recent comments.

* **Title** - description that appears over the list of recent comments.
* **Post Type** - if selected, filter by a custom post type. (e.g. post).
* **Number of comments to show (at most 15)** - enter the number of comments to be displayed.
* **Class Name** - widget_recent_comments

= Tag Cloud (Custom Post Type) =

display a list of the top 45 that has used in a tag cloud.

* **Title** - description that appears over the tag cloud.
* **Taxonomy** - if selected, filter a custom taxonomy (e.g. post_tag).
* **Class Name** - widget_tag_cloud

== Screenshots ==

1. Recent Posts (Custom Post Type)
2. Archives (Custom Post Type)
3. Categories (Custom Post Type)
4. Calendar (Custom Post Type)
5. Recent Comments (Custom Post Type)
6. Tag Cloud (Custom Post Type)

== Installation ==

1. Download and unzip files. Or install Custom Post Type Widgets plugin using the WordPress plugin installer. In that case, skip 2.
2. Upload "custom-post-type-widgets" to the "/wp-content/plugins/" directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Adds widgets to a widget area and configure settings through the 'Widgets' menu in WordPress.
5. Have fun!

**IMPORTANT**: By default, WordPress will not work Date-based permalinks of custom post type. Recommend that you install the plugin in order to edit the permalink, if you are using a Date-based permalinks.

== Changelog ==

= 1.0.2 =
* fix $cat_args['show_option_none']
= 1.0.1 =
* fix the 'name' param of get_terms()
= 1.0.0 =
* Initial release
