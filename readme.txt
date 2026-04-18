=== Gab - Classic Menu Migrator ===
Contributors: gabrieleiacovone
Tags: menu, migration, export, import, classic theme
Requires at least: 5.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Gab - Classic Menu Migrator is a lightweight and smart tool designed to move WordPress menus between websites effortlessly. 

Unlike standard export tools, this plugin uses a **Hybrid Smart Mapping** logic:
* **Object Recognition**: It automatically tries to reconnect menu items to existing Pages, Posts, and Categories on the new site by matching slugs.
* **Dynamic Fallback**: If a page doesn't exist on the target site, it doesn't break. It converts the link into a relative custom path (e.g., `/contact`).
* **Hard-Relative URLs**: It forces relative paths for custom links, making your menu perfectly portable between Local, Staging, and Live environments without domain conflicts.

This plugin is specifically built for Classic Themes or for developers who manage menus through the classic WordPress interface.

== Installation ==

1. Upload the `gab-classic-menu-migrator` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access the tool via **Tools > Classic Menu Migrator**.

== Frequently Asked Questions ==

= Does it work with Block Themes (FSE)? =
It works with menus managed via the Classic interface. If you use a Block Theme, you can still import your menu here and then use the "Import Classic Menu" feature within the Navigation Block in the Site Editor.

= Will it overwrite my existing menus? =
No. Every time you import a menu, the plugin creates a new one with a timestamp (e.g., "Main Menu - Imported 14:30") to avoid overwriting your current data.

== Screenshots ==

1. The main dashboard with Export and Import options.
2. Example of an imported menu with smart relative links.

== Changelog ==

= 1.5 =
* Initial public release.
* Added Hybrid Smart Mapping (Slug-based reconnection).
* Added Hard-Relative URL enforcement.
* Complete English localization.

== Upgrade Notice ==

= 1.5 =
First stable version. Recommended for all users migrating menus between local and live environments.