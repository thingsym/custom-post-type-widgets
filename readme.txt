=== Custom Post Type Widgets ===

Contributors: thingsym
Link: https://github.com/thingsym/custom-post-type-widgets
Donate link: https://github.com/sponsors/thingsym
Tags: widget, widgets, custom post type, taxonomy
Stable tag: 1.5.2
Tested up to: 6.2.0
Requires at least: 4.9
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom Post Type Widgets plugin adds default custom post type widgets.

== Description ==

Custom Post Type Widgets plugin adds default custom post type widgets.
You can filter by registered Custom Post Type or Taxonomy on widgets.

= Descriptions of Widgets =

= Recent Posts (Custom Post Type) =

display a list of the most recent custom posts.

* **Title** - description that appears over the list of recent custom posts.
* **Post Type** - if selected, filter by a custom post type. (e.g. post).
* **Number of posts to show (at most 15)** - enter the number of posts to display.
* **Display post date?** - if checked, display post date.

= Archives (Custom Post Type) =

display a list of archive links for each month that has custom posts.

* **Title** - description that appears over the list of archive links.
* **Post Type** - if selected, filter by a custom post type. (e.g. post).
* **Archive Type**
* **Display as dropdown** - if checked, this box causes the archives to be displayed in a drop-down box.
* **Show post counts** - if checked, this box causes a count of the number of posts for each archive period.
* **Order**

= Categories (Custom Post Type) =

display a list of categories that has custom posts.

* **Title** - description that appears over the list of categories.
* **Taxonomy** - if selected, filter a custom taxonomy (e.g. category).
* **Display as dropdown** - if checked, this box causes the categories to be displayed in a dropdown box.
* **Show post counts** - if checked, this box causes the count of the number of posts to display with each category.
* **Show hierarchy** - if checked, shows parent/child relationships in an indented manner.

= Calendar (Custom Post Type) =

display a calendar of the current month.

* **Title** - description that appears over the calendar.
* **Post Type** - if selected, filter by a custom post type. (e.g. post).

= Recent Comments (Custom Post Type) =

display a list of the most recent comments.

* **Title** - description that appears over the list of recent comments.
* **Post Type** - if selected, filter by a custom post type. (e.g. post).
* **Number of comments to show (at most 15)** - enter the number of comments to be displayed.

= Tag Cloud (Custom Post Type) =

display a list of the top 45 that has used in a tag cloud.

* **Title** - description that appears over the tag cloud.
* **Taxonomy** - if selected, filter a custom taxonomy (e.g. post_tag).
* **Show tag counts** - if checked, this box causes the count of the number of tags to display with each tag.

= Search (Custom Post Type) =

A search form for your site.

* **Title** - description that appears over the search.
* **Post Type** - if selected, filter by a custom post type. (e.g. post).

= Hooks =

Custom Post Type Widgets has its own hooks. See the reference for details.

Reference: [https://github.com/thingsym/custom-post-type-widgets#hooks](https://github.com/thingsym/custom-post-type-widgets#hooks)

= Support =

If you have any trouble, you can use the forums or report bugs.

* Forum: [https://wordpress.org/support/plugin/custom-post-type-widgets/](https://wordpress.org/support/plugin/custom-post-type-widgets/)
* Issues: [https://github.com/thingsym/custom-post-type-widgets/issues](https://github.com/thingsym/custom-post-type-widgets/issues)

= Contribution =

Small patches and bug reports can be submitted a issue tracker in Github. Forking on Github is another good way. You can send a pull request.

Translating a plugin takes a lot of time, effort, and patience. I really appreciate the hard work from these contributors.

If you have created or updated your own language pack, you can send gettext PO and MO files to author. I can bundle it into plugin.

* [VCS - GitHub](https://github.com/thingsym/custom-post-type-widgets)
* [Homepage - WordPress Plugin](https://wordpress.org/plugins/custom-post-type-widgets/)
* [Translate Custom Post Type Widgets into your language.](https://translate.wordpress.org/projects/wp-plugins/custom-post-type-widgets)

You can also contribute by answering issues on the forums.

* Forum: [https://wordpress.org/support/plugin/custom-post-type-widgets/](https://wordpress.org/support/plugin/custom-post-type-widgets/)
* Issues: [https://github.com/thingsym/custom-post-type-widgets/issues](https://github.com/thingsym/custom-post-type-widgets/issues)

= Patches and Bug Fixes =

Forking on Github is another good way. You can send a pull request.

1. Fork [Custom Post Type Widgets](https://github.com/thingsym/custom-post-type-widgets) from GitHub repository
2. Create a feature branch: git checkout -b my-new-feature
3. Commit your changes: git commit -am 'Add some feature'
4. Push to the branch: git push origin my-new-feature
5. Create new Pull Request

= Contribute guidlines =

If you would like to contribute, here are some notes and guidlines.

* All development happens on the **develop** branch, so it is always the most up-to-date
* The **master** branch only contains tagged releases
* If you are going to be submitting a pull request, please submit your pull request to the **develop** branch
* See about [forking](https://help.github.com/articles/fork-a-repo/) and [pull requests](https://help.github.com/articles/using-pull-requests/)

= Test Matrix =

For operation compatibility between PHP version and WordPress version, see below [Github Actions](https://github.com/thingsym/custom-post-type-widgets/actions).

== Frequently Asked Questions ==

= 404 error when clicking month link. =

You may need to edit the permalink of custom post type.

By default, WordPress will not work Date-based permalinks of custom post type.

For example, a month link generates a link in a format like `/<custom post type name>/date/YYYY/MM/`, if you set `Numeric` in Common Settings in Permalink Settings.

The month link has the following two patterns depending on the Common Settings.
But a link like below will not work.

* `/<custom post type name>/YYYY/MM/` (Day and name, Month and name, Post name)
* `/<custom post type name>/date/YYYY/MM/` (Numeric)

Recommend that you install the plugin in order to edit the permalink, if you are using a Date-based permalinks by the Widget.

And try the following:

Custom Post Type Rewrite
[https://wordpress.org/plugins/custom-post-type-rewrite/](https://wordpress.org/plugins/custom-post-type-rewrite/)

If you installed multiple plugins that can edit permalinks, the rewrite rules or permalinks may interfere.

In that case, you can disable the generation of permalinks by setting the following two constants in wp-config.php or the theme's function.php.

* `CUSTOM_POST_TYPE_WIDGETS_DISABLE_LINKS_ARCHIVE`
* `CUSTOM_POST_TYPE_WIDGETS_DISABLE_LINKS_CALENDAR`

`
define( 'CUSTOM_POST_TYPE_WIDGETS_DISABLE_LINKS_ARCHIVE', true );
define( 'CUSTOM_POST_TYPE_WIDGETS_DISABLE_LINKS_CALENDAR', true );
`

= Taxonomy select of Categories or Tags do not appear. =

Check the setting of the **hierarchical** argument of the register_taxonomy function.

> hierarchical  
> (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.  
> Default: false

Reference: [https://codex.wordpress.org/Function_Reference/register_taxonomy](https://codex.wordpress.org/Function_Reference/register_taxonomy)

By hierarchical option,

If false, use "Tag Cloud (Custom Post Type)" as tags.  
If true, use "Categories (Custom Post Type)" as categories.

= Search filter dose not work. =

Check the setting of the **exclude_from_search** argument of the register_post_type function.

> 'exclude_from_search'
> (bool) Whether to exclude posts with this post type from front end search results. Default is the opposite value of $public.

= Show featured image as a thumbnail. =

You can use the action hook `custom_post_type_widgets/recent_posts/widget/prepend` to adding thumbnails.

Code sample is as follows:

`
function cptw_hooks_setup() {
  add_action( 'custom_post_type_widgets/recent_posts/widget/prepend', 'cptw_recent_posts_prepend', 10, 4 );
}
add_action( 'after_setup_theme', 'cptw_hooks_setup' );

function cptw_recent_posts_prepend( $widget_id, $posttype, $instance, $recent_post ) {
  if ( has_post_thumbnail( $recent_post ) ) {
    echo get_the_post_thumbnail( $recent_post );
  }
}
`

Insert the above code into functions.php in your theme.

= Does Custom Post Type Widgets have hooks ? =

Custom Post Type Widgets has its own hooks.

Filter hooks

* custom_post_type_widgets/archive/widget_archives_dropdown_args
* custom_post_type_widgets/archive/widget_archives_args
* custom_post_type_widgets/categories/widget_categories_dropdown_args
* custom_post_type_widgets/categories/widget_categories_args
* custom_post_type_widgets/recent_comments/widget_comments_args
* custom_post_type_widgets/recent_posts/widget_posts_args
* custom_post_type_widgets/search/filter_post_type
* custom_post_type_widgets/tag_cloud/widget_tag_cloud_args
* custom_post_type_widgets/calendar/get_custom_post_type_calendar
* custom_post_type_widgets/archive/get_year_link_custom_post_type
* custom_post_type_widgets/archive/get_day_link_custom_post_type
* custom_post_type_widgets/archive/get_month_link_custom_post_type
* custom_post_type_widgets/archive/trim_post_type
* custom_post_type_widgets/calendar/get_day_link_custom_post_type
* custom_post_type_widgets/calendar/get_month_link_custom_post_type

Action hooks

* custom_post_type_widgets/recent_posts/widget/before
* custom_post_type_widgets/recent_posts/widget/prepend
* custom_post_type_widgets/recent_posts/widget/append
* custom_post_type_widgets/recent_posts/widget/after

Reference: [https://github.com/thingsym/custom-post-type-widgets#hooks](https://github.com/thingsym/custom-post-type-widgets#hooks)

== Screenshots ==

1. Recent Posts (Custom Post Type)
2. Archives (Custom Post Type)
3. Categories (Custom Post Type)
4. Calendar (Custom Post Type)
5. Recent Comments (Custom Post Type)
6. Tag Cloud (Custom Post Type)
7. Search (Custom Post Type)

== Installation ==

1. Download and unzip files. Or install Custom Post Type Widgets plugin using the WordPress plugin installer. In that case, skip 2.
2. Upload "custom-post-type-widgets" to the "/wp-content/plugins/" directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Adds widgets to a widget area and configure settings through the 'Widgets' menu in WordPress.
5. Have fun!

**IMPORTANT**: By default, WordPress will not work Date-based permalinks of custom post type. Recommend that you install the plugin in order to edit the permalink, if you are using a Date-based permalinks.

And try the following: [Custom Post Type Rewrite](https://wordpress.org/plugins/custom-post-type-rewrite/)

== Changelog ==

= 1.5.2 =
* update japanese translation
* tested up to 6.2.0
* fix composer scripts
* update github actions
* fix load_textdomain method for testability
* add msgmerge to composer scripts
* add support section and enhance contribution section to README
* fix license
* update screenshots

= 1.5.1 =
* edit README
* fix the priority of the rewrite rule of register_post_type()

= 1.5.0 =
* tested up to 6.0.0
* fix wp-plugin-unit-test.yml
* update japanese translation
* update pot
* fix test case
* add format argument
* supports translation
* add ASC/DESC order option for archive widget
* add constants CUSTOM_POST_TYPE_WIDGETS_DISABLE_LINKS_ARCHIVE and CUSTOM_POST_TYPE_WIDGETS_DISABLE_LINKS_CALENDAR
* fix rewrite slug for has_archive setting
* rename variable name

= 1.4.2 =
* add composer script
* remove makepot:php composer script
* change action hook
* rename method name
* fix hook to load_textdomain for translate on Widgets Screen
* replace assert from assertEquals to assertSame

= 1.4.1 =
* add test case
* fix README
* update wp-plugin-unit-test.yml
* bump up yoast/phpunit-polyfills version
* change os to ubuntu-20.04 for ci
* add Upgrade Notice
* change requires at least to wordpress 4.9
* change requires to PHP 5.6
* fix: fix pot and translation
* fix label
* fix test unit configuration and lint ruleset
* update composer.json
* add timeout-minutes to workflows
* add phpunit-polyfills
* update install-wp-tests.sh
* fix .editorconfig
* tested up to 5.8.0
* fix github workflows

= 1.4.0 =
* update screenshot
* tested up to 5.6.2
* update japanese translation
* update pot
* add init method, change method name
* separate class into separate a file
* add sponsor link
* add dropdown label option with categories widget
* add archive type option with archive widget
* add donate link
* add filter hooks, custom_post_type_widgets/archive/get_month_link_custom_post_type, custom_post_type_widgets/archive/trim_post_type, custom_post_type_widgets/calendar/get_day_link_custom_post_type, custom_post_type_widgets/calendar/get_month_link_custom_post_type
* add FUNDING.yml
* add GitHub actions for CI/CD, remove .travis.yml
* imporve code with phpcs, phpmd and phpstan

= 1.3.0 =
* edit README
* update japanese translation
* update pot
* fix test case
* imporve code with phpcs, phpmd and phpstan
* update testunit configuration
* restructure code to perform a single task
* add denying direct file access
* divide as load_textdomain function
* fix composer.json
* change calendar widget markup
* add hook custom_post_type_widgets/calendar/get_custom_post_type_calendar
* fix get_custom_post_type_calendar method
* change to call method directly instead of via hook in the calendar widget

= 1.2.1 =
* replace from id attribute to calss attribute in the calendar
* add any matching value as all types

= 1.2.0 =
* change Requires at least version 4.0
* [Calendar widget] cache the calendar
* improve filter hooks and action hooks
* fix test case
* refactoring
* add customize_selective_refresh
* fix phpcs.ruleset.xml
* replace from strip_tags to wp_strip_all_tags
* replace from _e to esc_html_e
* add PHPDoc
* fix header
* add reset-wp-tests.sh, uninstall-wp-tests.sh
* fix indent and reformat with phpcs and phpcbf
* add composer.json for test
* add static code analysis config

= 1.1.3 =
* change Requires at least version 3.7
* fix add_action
* add sanitize
* add tests

= 1.1.2 =
* [Categories widget] change value_field of the cat_args from name to slug
* limit the scope of the filter hook
* [Search widget] rename the name of the filter hook
* fix the initial value of the posttype, the archive_name and the taxonomy

= 1.1.1 =
* [Search widget] add apply_filters 'WP_Custom_Post_Type_Widgets_Search_filter_post_type'
* improve function 'query_search_filter_only_post_type' [Search widget]
* [Search widget] change to add_action 'pre_get_posts' run only on the front-end page

= 1.1.0 =
* [Comments widgets, Search widgets] add 'All' to posttype option
* refactoring
* add Custom Post Type Search widget

= 1.0.4 =
* fix cache key of Custom Post Type Calendar widget
* support for custom post type slugs
* add german translation

= 1.0.3 =
* add italian translation

= 1.0.2 =
* fix $cat_args['show_option_none']

= 1.0.1 =
* fix the 'name' param of get_terms()

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.4.1 =
* Requires at least version 4.9 of the WordPress
* Requires PHP version 5.6

= 1.2.0 =
* Requires at least version 4.0 of the WordPress

= 1.1.3 =
* Requires at least version 3.7 of the WordPress
