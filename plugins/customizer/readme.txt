=== Customizer ===
Contributors: amielucha
Donate link: http://example.com/
Tags: customize, customizer, developer, backend, 3.4
Requires at least: 3.4
Tested up to: 3.4.1
Stable tag: 0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add theme's or plugin's options to the Customizer introduced in WP 3.4. Build theme and plugin options accessible from WordPress front-end.

== Description ==

*Customizer* Plugin is a tool built for WordPress **Theme Developers** and **Plugin Developers**. It extends Customizer panel introduced in WP 3.4.

With *Customizer* Plugin you can:

* quickly add your theme's or plugin's options to the Customizer Panel without leaving the frontent preview
* take advantage of both serialized and non-serialized Theme Options
* add dropdowns, colour pickers, image uploaders, radio options and more
* extend your current theme to enable front-end customizations

Check this [quick user guide](http://wp-customizer.com/user-guide/ "How to configure Customizer") for examples of how to configure WordPress Customizer.

Download the alpha version now and discuss the functionality or report a bug. Your feedback is appreciated.

= Upcoming Features =

The plugin is currently being developed to support even wider range of features and increase developers' productivity. The final release will include:

**Import / Export**

Once you add your Theme Options to the Customizer's panel you can save them to a .csv file and import into another project.

**White-label mode**

Future release will allow you to add controls to the front-end Customizer Panel, export them to a csv file and seamlessly integrate with your product (Theme or Plugin).

**More Option Types**

If the default text, image, color, radio, select and checkbox arent enough.

== Installation ==


= 1. Download and install Customizer Plugin =

Currently only available via downloads section.

= 2. Activate the plugin =

= 3. Configure Settings =

If your theme or plugin is storing options in a formalized format (recommended) you will have to configure this section. Select Use Serialization and specify your options name. For example if you are storing your options in my_options[option] format then the value of this field should be my_option.
configure serialized options

*Don't forget to save your changes.*

Note: If you are working with more than one options array you will have to set your Sections and Controls (more about them in the second part of the guide) for the first table, go back to customizer options, change the Serialized Options Group Name and continue working on the second array in the frontend Customizer editor.

= 4. You can start working with Customizer. =

See the second part of the guide: [Working with Customizer](http://wp-customizer.com/user-guide/).


== Frequently Asked Questions ==

= Is it possible to add x feature? =

Yes, I will review every reasonable request and will consider implementing more functions.

== Screenshots ==

Coming soon.

== Changelog ==

= 0.6 =
* Added autocomplete function to "Add Control" section (requires serialized options)

= 0.5 =
* First publicly released version of the plugin. Fully functional but might contain occasional bugs.

== Upgrade Notice ==

= 0.6 =
* Autocomplete added

= 0.5 =
* First Release
