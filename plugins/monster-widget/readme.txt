===Monster Widget===

Contributors: automattic, mfields
Tags: widget, theme development, debug
License: GPLv2 or later
Requires at least: 3.2.0
Tested up to: 3.5
Stable tag: trunk

Provides a quick and easy method of adding all core widgets to a sidebar for testing purposes.

==Description==

The Monster widget consolidates all 13 core widgets into a single widget enabling theme developers to create multiple instances with ease. It has been created to save time during theme development and review by minimizing the steps needed to populate a sidebar with widgets. The Monster widget is not designed for use in production.

== Frequently Asked Questions ==

= Why I do not see the Menu widget? =

The core Menu widget provides an interface that enables you to select a custom menu to display. The Monster widget will attempt to locate the custom menu having the most links on your site. In the event that no menus are registered, a menu widget will not be displayed. Likewise, if all of your custom menus are empty (no links added to them), no menu widget will be displayed.

== Screenshots ==

1. The Monster widget will appear in your list of available widgets.
2. Drag it to a widget area to create an instance.
3. View your site and see that all core widgets are displayed!

==Changelog==

= v0.3 - January 5th, 2013 =
* Added test photograph by directly to the plugin. Thanks to [Rarst](http://profiles.wordpress.org/rarst) for the bug report.
* Add a really long select element to the text widget. See [_s issue #99](https://github.com/Automattic/_s/pull/99).
* Add a smiley to the image caption.
* Remove the "pipe test".
* Bump version number.
* Update Changelog.

= v0.2 - October 18th, 2012 =
* Create custom cache key for the Recent Posts widget.
* Only add the Links widget when it has been registered.
* Bump version number.
* Update Changelog.

= v0.1 - May 12th, 2012 =
* Original Release.

==Installation==

This plugin can be installed directly from your site.

1. Log in and navigate to Plugins &rarr; Add New.
2. Type "Monster Widget" into the Search input and click the "Search Widgets" button.
3. Locate the Monster Widget in the list of search results and click "Install Now".
4. Click the "Activate Plugin" link at the bottom of the install screen.
5. Navigate to Appearance &rarr; Widgets and [create a new instance](http://codex.wordpress.org/WordPress_Widgets#Activate_Widgets).

It can also be installed manually.

1. [Download](http://wordpress.org/extend/plugins/monster-widget/) the plugin from WordPress.org.
2. Unzip the package and move to your plugins directory.
3. Log into WordPress and navigate to the "Plugins" screen.
4. Locate "Monster Widget" in the list and click the "Activate" link.
5. Navigate to Appearance &rarr; Widgets and [create a new instance](http://codex.wordpress.org/WordPress_Widgets#Activate_Widgets).

==Frequently Asked Questions==

= Why was the pipe test removed in version 0.3 =

A couple members of the Theme Team at Automattic had a discussion about the relevance of this test and we came to the conclusion that it is rather unnatural. Moving forward it would be best to only include markup and data that best represent actual use cases.
