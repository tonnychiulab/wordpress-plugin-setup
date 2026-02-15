# WordPress Plugin Structure

## Standard Directory Layout

```
my-plugin/
├── my-plugin.php          # Main plugin file (required)
├── readme.txt             # WordPress.org readme
├── uninstall.php          # Cleanup on deletion
├── license.txt            # License file
├── index.php              # Security: prevent directory listing
├── admin/                 # Admin-specific files
│   ├── index.php
│   └── css/
│   └── js/
├── inc/                   # Include files (classes, functions)
│   ├── index.php
│   └── class-my-plugin.php
├── public/                # Public-facing files
│   ├── index.php
│   ├── css/
│   └── js/
├── src/                   # Source files (SCSS, TypeScript)
│   └── css/
│       └── main.css
├── dist/                  # Compiled production files
│   └── css/
│       └── admin.min.css
├── languages/             # Translation files
├── templates/             # Custom template files
└── assets/                # Images, fonts, etc.
```

## Required: Plugin Header

Every plugin must have a valid header in the main PHP file:

```php
/*
Plugin Name:  My Plugin
Plugin URI:   https://example.com/my-plugin
Description:  A brief description of what the plugin does.
Version:      1.0.0
Requires at least: 5.0
Requires PHP: 7.2
Author:       Your Name
Author URI:   https://example.com
License:      GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  my-plugin
Domain Path:  /languages
*/
```

## WordPress Coding Standards

- Use tabs for indentation (not spaces)
- Prefix all functions, classes, and global variables with your plugin's text domain
- Use `wp_ajax_` and `wp_ajax_nopriv_` hooks for AJAX
- Use WordPress built-in functions instead of native PHP when available
- Enqueue scripts and styles properly using `wp_enqueue_scripts` hook

## Security Best Practices

- Always use `$wpdb->prepare()` for database queries
- Sanitize all input with functions like `sanitize_text_field()`, `esc_html()`, `esc_url()`
- Escape all output with `esc_html()`, `esc_attr()`, `esc_url()`
- Use nonces for form submissions and AJAX requests
- Check user capabilities with `current_user_can()`

## References

- [WordPress Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- [Security Best Practices](https://developer.wordpress.org/plugins/security/)
