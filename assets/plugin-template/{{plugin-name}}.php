<?php
/**
 * Plugin Name:       {{plugin-name}}
 * Plugin URI:        {{plugin-uri}}
 * Description:       {{plugin-description}}
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            {{author-name}}
 * Author URI:        {{author-uri}}
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       {{text-domain}}
 * Domain Path:       /languages
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class {{class_prefix}}_Plugin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Load required files.
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . 'inc/class-admin.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/class-validator.php';
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	/**
	 * Enqueue admin styles.
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style(
			'{{text-domain}}-admin',
			plugin_dir_url( __FILE__ ) . 'dist/css/admin.min.css',
			array(),
			'1.0.0'
		);
	}

	/**
	 * Add admin menu page.
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( '{{plugin-name}}', '{{text-domain}}' ),
			__( '{{plugin-name}}', '{{text-domain}}' ),
			'manage_options',
			'{{menu-slug}}',
			array( $this, 'render_admin_page' ),
			'dashicons-admin-generic',
			99
		);
	}

	/**
	 * Render admin page.
	 */
	public function render_admin_page() {
		// Load the admin view.
		require_once plugin_dir_path( __FILE__ ) . 'admin/settings-page.php';
	}
}

// Initialize the plugin.
new {{class_prefix}}_Plugin();
