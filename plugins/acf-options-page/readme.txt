=== Advanced Custom Fields: Options Page ===
Contributors: elliotcondon
Author: Elliot Condon
Author URI: http://www.elliotcondon.com
Plugin URI: http://www.advancedcustomfields.com
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: trunk
Homepage: http://www.advancedcustomfields.com/add-ons/options-page/
Version: 1.0.1


== Copyright ==
Copyright 2011 - 2013 Elliot Condon

This software is NOT to be distributed, but can be INCLUDED in WP themes: Premium or Contracted.
This software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.


== Description ==

= Global Options have never been so easy =

The “options page” addon creates a new menu item called “Options” which can hold advanced custom field groups (just like any other edit page). You can also register multiple options pages

http://www.advancedcustomfields.com/add-ons/options-page/


== Installation ==

This software can be treated as both a WP plugin and a theme include.
However, only when activated as a plugin will updates be available/

= Plugin =
1. Copy the 'acf-options-page' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

= Include =
1. Copy the 'acf-options-page' folder into your theme folder (can use sub folders)
   * You can place the folder anywhere inside the 'wp-content' directory
2. Edit your functions.php file and add the following code to include the field:

`
include_once('acf-options-page/acf-options-page.php');

`

3. Make sure the path is correct to include the acf-options-page.php file


== Changelog ==

= 1.0.1 =
* wrapped the register_options_page function in an if statement to prevent error when activation this add-on with ACF v3

= 1.0.0 =
* [Updated] Updated update_field parameters
* Official Release

= 0.0.4 =
* [Updated] Update nonce name to 'acf_nonce' => 'input' to match naming convention

= 0.0.3 =
* [Updated] Drop support of old filters / actions

= 0.0.2 =
* Fixed errors caused by an update to the core functions.

= 0.0.1 =
* Initial Release.
