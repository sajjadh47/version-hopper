=== Version Hopper ===
Tags: plugin version, rollback, downgrade, update, themes
Contributors: sajjad67
Author: Sajjad Hossain Sagor
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily switch between versions of your WordPress plugins and themes directly from the admin dashboard.

== Description ==

Version Hopper allows administrators to **rollback, downgrade, or upgrade** WordPress plugins and themes to any previous version available on WordPress.org. 

=== Features include: ===
* View installed plugins and themes along with all available versions.
* Select a specific version and apply it without leaving the admin page.
* Uses WordPress native upgrade process for safe updates.
* AJAX-powered version switching with human-readable messages.
* Supports both plugins and themes.

**Note:** This plugin works with plugins and themes available on WordPress.org. Custom or premium plugins hosted outside WordPress.org may not be supported.

== Installation ==
1. Upload the `version-hopper` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Version Hopper** to start switching plugin and theme versions.

== Frequently Asked Questions ==
= Can I switch to a version not listed on WordPress.org? =
No. Version Hopper only supports versions available through WordPress.org.

= Is it safe to downgrade plugins? =
Yes, Version Hopper uses WordPress' native upgrader API. However, always **backup your site** before downgrading.

= Can I switch themes as well? =
Yes, you can switch installed themes to any previous version available on WordPress.org.

== Screenshots ==
1. Plugin list with version selector.
2. Theme list with version selector.

== Changelog ==
= 1.0.3 =
* Compatibility checkup for latest wp version 7.1
= 1.0.2 =
* Compatibility checkup for latest wp version 7.0
= 1.0.1 =
* Compatibility checkup for latest wp version 6.9
= 1.0.0 =
* Initial release.
* Plugin and theme version switching via WordPress.org.
* AJAX-based feedback messages.
* Native WordPress upgrader API integration.

== Upgrade Notice ==
Always try to keep your plugin update so that you can get the improved and additional features added to this plugin up to date.

== Additional Notes ==
* This plugin is intended for administrators and developers familiar with WordPress plugin and theme management.
* Always test version changes on a staging site before applying on production.