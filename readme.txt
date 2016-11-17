=== User Dropdown Menu ===
Contributors: sagaio
Tags: dropdown menu, user dropdown menu, dropdown, woocommerce menu, menu
Requires at least: 4.5.2
Tested up to: 4.5.2
Stable tag: 1.0.3
License: GPLv2 or later
Licemse URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

Insert a dropdown menu with a icon button, based on Bootstrap 4.0 Dropdown. All settings can be found in the Theme Customizer.

= Features =

* Build your menu from the standard interface and assign it to User Dropdown Menu
* All settings found in your Theme Customizer
* Option to change the default icon image
* Option to display login form that is customizable
* Option to display header above login form
* Option to display current username in menu
* Option to display header above username
* Option to display logout link
* Lots of more options when it comes to styling

Are you missing an option? Head over to https://github.com/sagaio/user-dropdown-menu and submit an issue.

== Installation ==

= From your WordPress dashboard =

Visit 'Plugins > Add New'
Search for and download 'User Dropdown Menu'
Activate User Dropdown Menu from your Plugins page.

= From other sources =

Download User Dropdown Menu.
Upload the 'user-dropdown-menu' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
Activate User Dropdown Menu from your Plugins page.

== Frequently Asked Questions ==

= How do I use it? =

Through a shortcode: [user_dropdown_menu]

= How do I configure it? =

All settings can be found in the Theme Customizer

= Which icons are available? =

For the moment being, only 1 icon is available and is loaded as a .png image. If you'd like more icons, submit them as an issue in the github repo: https://github.com/sagaio/user-dropdown-menu

= I have an idea / I found a bug =

Create an issue at github: https://github.com/sagaio/user-dropdown-menu

= I need help =

Start a topic in the support section

== Screenshots ==

== Changelog ==

= 1.0.3 =
*2016-11-17*

* Fix: CSS for outer and inner wrapper

= 1.0.2 =
*2016-11-17*

* Fix: Shortcode was using ```echo``` and is now using ```return``` instead.
* Fix: Position of inner wrapper changed to relative to contain dropdown menu.
* Update: ```#sagaio-udm-menu-wrapper``` changed name to ```#sagaio-udm-menu-inner-wrapper```.
* New: Added outer wrapper and options for configuring its position, id: ```#sagaio-udm-menu-wrapper```.

= 1.0.1 =
*2016-11-14*

* Fix: Typo in ```<div class="sagaio-udm-menu-header">```.
* New: Options for configuring the login header, class: ```.sagaio-udm-menu-header```.

= 1.0.0 =
*2016-11-14*

* First release