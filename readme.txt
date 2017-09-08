=== TM Timeline ===

Contributors: TemplateMonster 2002
Tags: timeline, timelines, event, history
Requires at least: 4.5
Tested up to: 4.8.1
Stable tag: 1.1.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

TM Timeline plugin allows you to showcase your most important events.

== Description ==

TM Timeline plugin allows you to showcase the most important events of your business in a chronological order, which can be a great addition to your 'About' page.
Tell your visitors the history of your company, and show the turning points you experienced along the way. The plugin has a simple interface and lets you build your timelines quickly.

== Installation ==

1. Upload "TM Timeline" folder to the "/wp-content/plugins/" directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. Navigate to the "TM Timeline" page available through the left menu

== Screenshots ==

1. Settings page, where you can generate the shortcode. Multiple layouts and date formats available.

== Configuration ==

= Events =
The timeline consists of events, to which you can assign dates and tags. To create an event, go to "TM Timeline" -> "Add New". Enter the title of your event, and description. In the sidebar specify a date when the event occurred.

= Tags =
If you want to have multiple timelines, you can group your events by assigning tags to them. If you assign more than one tag to an event, you can display it in multiple timelines.

= Settings =
Once you've created your events, you have to configure their display settings. We have several options here:

* Layout - layout represents the way your timeline will be displayed on the page. There are three layout variations: horizontal, vertical, and vertical (chess order).
* Visible items limit - this setting applies to horizontal layout only. You can set the number of events that can be displayed per view. In order to access the events hidden from the view, you have to use navigation arrows.
* Date format - choose from 8 different date formats to display the date in your timeline.
* Tag - select which event group to display based on their tags. If no tag is specified, the timeline will contain all events available.
* Display anchors - if this box is checked, your events will be linking to corresponding event posts, so that your visitors can read about each event in detail.

In order to create a timeline follow these steps:
1. Navigate to the "TM Timeline" -> "Add New" page
2. Create event posts, specify the "Timeline Date" option in each one
3. Create a tag and asign it to the event posts. List of all event posts available on "TM Timeline" -> "Posts" page
4. Navigate to the "TM Timeline" -> "Settings" page, fill in required options and press the "Generate Shortcode" button
5. Simply copy the generated shortcode to your page or post

== Changelog ==

= 1.1.1 =

* Fixed bugs

= 1.1.0 =

* ADD: compatibility with Elementor plugin

= 1.0.5 =

* ADD: `the_content` filter
* ADD: unique prefix for functions
* UPD: utilities
* UPD: enqueue public-facing script

= 1.0.4 =

* Remove Date column from the Timeline Posts screen

= 1.0.3 =

* Horizontal layout improvements

= 1.0.2 =

* Use current date as default
* Timeline posts url navigation

= 1.0.1 =

* Add sorting order option

= 1.0.0 =

* Initial release
