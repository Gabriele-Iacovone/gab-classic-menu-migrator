# Gab - Classic Menu Migrator

A lightweight and smart WordPress plugin to export and import menus between websites. Specifically designed for **Classic Themes**, it ensures that your menu structure remains intact during migrations.

## 🚀 Key Features

- **Smart Mapping**: Automatically attempts to reconnect menu items to existing Pages, Posts, and Categories on the new site using slugs.
- **Hybrid Logic**: If a linked page doesn't exist on the target site, it converts the item into a **Relative Custom Link** (e.g., `/contact-us`) to prevent broken absolute URLs.
- **Hard-Relative URLs**: Forces relative paths for all custom links, making the menu perfectly portable between Local, Staging, and Live environments.
- **Clean Export**: Generates a clean, readable `.json` file named after your menu.
- **No Dependencies**: Works out of the box with native WordPress functions.

## 🛠 Installation

1. Download or clone this repository into your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Tools > Classic Menu Migrator** to start migrating.

## 📖 How to Use

### Exporting
1. Select the menu you want to export from the dropdown list.
2. Click **Download JSON**.
3. A file named `menu-migrator-[menu-name].json` will be saved to your computer.

### Importing
1. Go to the target WordPress site where the plugin is installed.
2. Upload the JSON file in the **Import** section.
3. Click **Import Menu**.
4. Your new menu will be created as `[Menu Name] - Imported [Time]`.

## ⚠️ Requirements
- WordPress 5.0+
- Classic Theme (or a Block Theme where you intend to use Classic Menus).
- PHP 7.4+

## 📄 License
This project is licensed under the GPLv2 or later.

---
*Created with ❤️ by Gabriele Iacovone*