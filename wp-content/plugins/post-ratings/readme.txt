=== Post Ratings ===
Contributors: dfactory
Donate link: http://www.dfactory.eu/
Tags: ajax, post, rating, ratings, postrating, postratings, simple, jquery, vote, voting, widget
Requires at least: 4.0.0
Tested up to: 4.5.2
Stable tag: 3.0
License: MIT License
License URI: http://opensource.org/licenses/MIT   

Simple, developer-friendly, straightforward post rating plugin. Relies on post meta to store avg. rating / vote count.

== Description ==

[Post Ratings](http://www.dfactory.eu/plugins/post-ratings/) is a simple, developer-friendly, straightforward post rating plugin. Relies on post meta to store avg. rating / vote count.

For more information, check out plugin page at [dFactory](http://www.dfactory.eu/) or plugin [support forum](http://www.dfactory.eu/support/forum/post-ratings/).

= Features include: =

* Allow your site users to rate posts (of any kind)
* Display the average post rating, vote count or weighted (bayesian) rating within your posts
* Display a widget with the top rated posts in your sidebar
* Allow you to create your own rating formula

Why another rating plugin? Because the existing ones are either outdated, bloated with useless functionality, or just too buggy :)

== Installation ==

1. Install Post Ratings either via the WordPress.org plugin directory, or by uploading the files to your server
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Post Ratings settings and set your options.

== Frequently Asked Questions ==

= How does this plugin log votes? =

Basic check trough cookies, IP match against a limited transient-cached IP list, and user meta records (if registration-only ratings are enabled). If any of these checks fail the user is not allowed to rate.

= How do I change the rating template? =

If you want to change the HTML mark-up for the ratings create a "post-ratings-control.php" template within your theme folder.
Check out the original template from this package for help on how to edit it.

= How do I manually display the rating links where I want? =

Either fill the custom filter field with your own action tag in the plugin settings, or use  the `[rate]` shortcode.

= Translations: =

* German - by Thorsten Wollenhoefer
* Polish - by Bartosz Arendt
* Slovak - by Martin

== Changelog ==

= 3.0 =
* Complete plugin rewrite and update by [dfactory](https://dfactory.eu/)

= 2.4 =
* Added Slovak translation, tx Martin
* Fixed wrong path to .mo/.po files
* Added wrapper for current_user_can (experimental), eg. current_user_can('rate', $post_id)

= 2.3 =
* Added support for proxies that send the client IP
* Fixed a bug in the template loader, tx @ Spencer
* Fixed a bug with the transient post limit checks
* Added German translations (tx Thorsten) and fixed a possible localization issue

= 2.2 =
* The rating control (HTML) can now be fully customized trough the "post-ratings-control.php" template (create one inside your theme)
* Removed the formatRatingsMeta method, as it's now unnecessary
* Fixed a compatibility issue with certain PHP setups

= 2.1 =
* Fixed issue on widgets page from 2.0

= 2.0 =
* Fixed buggy rating records on multisite
* Themes can now override default CSS, if post-ratings.css is present in the theme dir

= 1.9 =
* Added cache flush triggers
* Decreased the_content filter priority to accomodate plugins that don't handle excerpts correctly
* Added CPT support for the "archives" page visibility setting
* Fixed an issue with duplicate user rated posts ID records

= 1.8 =
* Fixed an issue with the shortcode (not being displayed in certain situations)
* Added "force" argument to the shortcode; if present, page visibility setting is ignored

= 1.7 =
* Support for Google Rich Snippets, using microformats mark-up; note that this can only work on singular pages!

= 1.6 =
* Added a few filter tags (and a javascript event on succesful rate), so the output can be easily changed

= 1.5 =
* Fixed a js issue with voting introduced by mistake in 1.4
* Some updates to the Atom widget code (as Atom Widget API changed)

= 1.4 =
* Fixed 2 bugs related to Atom-based themes

= 1.3 =
* Fixed bug in which the user formula wasn't working outside the widget
* Fixed an issue where under certain conditions pages would be empty
* Added some API info the FAQ

= 1.2 =
* Added the ability to use a custom bayesian formula
* Made the IMDB rating formula as default
* Fixed some localization inconsistencies on the Atom widget

= 1.1 =
* Added Atom widget (replaces the default widget if the site runs on an Atom theme)
* Fixed javascript error when running non-Atom 2+ themes

= 1.0 =
* First public release.

== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png


== Upgrade Notice ==

= 3.0 =
* Complete plugin rewrite and update by [dfactory](https://dfactory.eu/)