<?php

/**
 * Plugin Name: Series Manager
 * Description: Manage post series and navigation between them.
 * Version: 0.1.0
 * Author: Rofaida
 */

/* =====================================================
 *  Security: Prevent direct access
 * ===================================================== */
if (! defined('ABSPATH')) {
    exit;
}

/* =====================================================
 *  Autoload & Core Includes
 * ===================================================== */

// Composer autoload (if used)
require_once plugin_dir_path(__FILE__) . '/vendor/autoload.php';

// Core architecture files
require_once plugin_dir_path(__FILE__) . '/includes/core/Repository/SeriesRepository.php';
require_once plugin_dir_path(__FILE__) . '/includes/core/Helpers/SeriesFormatter.php';
require_once plugin_dir_path(__FILE__) . '/includes/core/Controller/SeriesController.php';
require_once plugin_dir_path(__FILE__) . '/includes/core/Service/SeriesService.php';
require_once plugin_dir_path(__FILE__) . '/includes/core/Service/SeriesDataProvider.php';

// Taxonomy logic
require_once plugin_dir_path(__FILE__) . '/includes/taxonomy/class-series-taxonomy.php';
require_once plugin_dir_path(__FILE__) . '/includes/taxonomy/class-series-taxonomy-edit.php';

// Frontend rendering
require_once plugin_dir_path(__FILE__) . '/includes/frontend/renderer.php';

// Admin panel
require_once plugin_dir_path(__FILE__) . '/includes/Admin/Admin.php';

use Service\SeriesService;

/* =====================================================
 *  Admin Initialization
 * ===================================================== */

// Initialize admin UI (menus, pages, etc.)
SM_Series_Admin::init();

/* =====================================================
 *  Plugin Initialization (Core Logic)
 * ===================================================== */

/**
 * Initialize plugin core features:
 * - Register taxonomy
 * - Initialize frontend rendering
 * - Setup MVC structure (Repository, Service, Controller)
 */
function sm_series_manager_init()
{
    // Register taxonomy and its edit screen
    SM_Series_Taxonomy::register();
    SM_Series_Taxonomy_Edit::register();

    // Initialize frontend block rendering
    SM_Series_Renderer::init();

    // Setup database access
    global $wpdb;

    // Initialize core architecture
    $repository = new SeriesRepository($wpdb);
    $service    = new SeriesService();
    $formatter  = new SeriesFormatter();

    // Initialize controller (connect everything)
    new SeriesController($repository, $service, $formatter);
}

/**
 * Hook into WordPress initialization
 */
add_action('init', 'sm_series_manager_init');



/* =====================================================
 *  FRONTEND ASSETS (Public Site)
 * ===================================================== */

/**
 * Enqueue frontend JS only on series taxonomy pages
 */
function sm_enqueue_frontend_assets()
{
    if (is_tax('series')) {
        wp_enqueue_script(
            'sm-series-frontend',
            plugins_url('assets/frontend.js', __FILE__),
            [],
            filemtime(plugin_dir_path(__FILE__) . 'assets/frontend.js'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'sm_enqueue_frontend_assets');


/**
 * Enqueue accordion JavaScript on single posts
 */
function sm_enqueue_accordion_assets()
{
    if (is_singular('post')) {
        wp_enqueue_script(
            'sm-accordion',
            plugins_url('assets/accordion.js', __FILE__),
            [],
            filemtime(plugin_dir_path(__FILE__) . 'assets/accordion.js'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'sm_enqueue_accordion_assets');


/**
 * Enqueue frontend styles (Tailwind + Fonts)
 */
function sm_enqueue_front_assets()
{
    // Google Font: Inter
    wp_enqueue_style(
        'inter-font',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        [],
        null
    );

    // Google Icons: Material Symbols
    wp_enqueue_style(
        'material-symbols-font',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap',
        [],
        null
    );

    // Tailwind compiled CSS
    wp_enqueue_style(
        'sm-series-frontend',
        plugins_url('build/index.css', __FILE__),
        [],
        filemtime(plugin_dir_path(__FILE__) . 'build/index.css')
    );
}
add_action('wp_enqueue_scripts', 'sm_enqueue_front_assets');


/* =====================================================
 *  POST CONTENT MODIFICATION (Filter)
 * ===================================================== */

/**
 * Append series UI to post content
 */
function sm_append_series_to_content(string $content)
{
    if (! is_singular('post')) {
        return $content;
    }
    if (function_exists('has_block') && has_block('series-manager/series-list', $content)) {
        return $content;
    }

    $series_html = SM_Series_Renderer::render_series();
    if (! $series_html) {
        return $content;
    }

    return $content . $series_html;
}
add_filter('the_content', 'sm_append_series_to_content', 20);


/* =====================================================
 *  BLOCK EDITOR (Gutenberg) ASSETS
 * ===================================================== */

/**
 * Enqueue assets for block editor (JS + CSS + AJAX data)
 */
function sm_enqueue_post_editor_assets()
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    // Limit to specific editor screens
    if ($screen && ! in_array($screen->base, ['post', 'site-editor', 'widgets', 'customize'], true)) {
        return;
    }

    // Restrict to supported post types
    if ($screen && $screen->post_type) {
        $service   = new SeriesService();
        $supported = $service->getSupportedPostTypes();

        if (! in_array($screen->post_type, $supported, true)) {
            return;
        }
    }

    // Load asset metadata (dependencies + version)
    $asset_file_path = plugin_dir_path(__FILE__) . 'build/index.asset.php';

    if (! file_exists($asset_file_path)) {
        return;
    }

    $asset_file = include $asset_file_path;

    $script_handle = 'sm-series-post-editor';

    // Enqueue JS
    wp_enqueue_script(
        $script_handle,
        plugins_url('build/index.js', __FILE__),
        array_merge(
            $asset_file['dependencies'] ?? ['wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data'],
            ['wp-api-fetch']
        ),
        $asset_file['version'] ?? filemtime(plugin_dir_path(__FILE__) . 'build/index.js'),
        true
    );

    // Enqueue CSS if exists
    $css_file_path = plugin_dir_path(__FILE__) . 'build/index.css';
    if (file_exists($css_file_path)) {
        wp_enqueue_style(
            'sm-series-post-editor',
            plugins_url('build/index.css', __FILE__),
            [],
            $asset_file['version'] ?? filemtime($css_file_path)
        );
    }

    // Pass AJAX data to JS
    wp_localize_script(
        $script_handle,
        'SMSeries',
        [
            'nonce'   => wp_create_nonce('sm_series_nonce'),
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]
    );
}
add_action('enqueue_block_editor_assets', 'sm_enqueue_post_editor_assets');
add_action('enqueue_widgets_block_editor_assets', 'sm_enqueue_post_editor_assets');

/* =====================================================
 * GLOBAL BLOCK ASSETS (Frontend + Editor)
 * ===================================================== */

/**
 * Enqueue Material Icons globally (frontend + editor)
 */
add_action('enqueue_block_assets', function () {
    wp_enqueue_style(
        'material-symbols',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined',
        [],
        null
    );
});

/* =====================================================
 *  ADMIN PANEL ASSETS (Dashboard)
 * ===================================================== */

/**
 * Enqueue Tailwind CSS for plugin admin pages only
 */
function sm_enqueue_admin_assets(string $hook): void
{
    // Limit to plugin admin pages
    if (! in_array($hook, ['toplevel_page_series-manager', 'series-manager_page_available-custom-post-types', 'series-manager_page_series-layouts'], true)) {
        return;
    }

    // Enqueue Inter font
    wp_enqueue_style(
        'inter-font-admin',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        [],
        null
    );

    // Enqueue Material Symbols font
    wp_enqueue_style(
        'material-symbols-font-admin',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap',
        [],
        null
    );

    $css_file_path = plugin_dir_path(__FILE__) . 'build/index.css';

    // Ensure CSS file exists
    if (! file_exists($css_file_path)) {
        return;
    }

    // Enqueue Tailwind CSS
    wp_enqueue_style(
        'sm-series-admin',
        plugins_url('build/index.css', __FILE__),
        [],
        filemtime($css_file_path)
    );
}
add_action('admin_enqueue_scripts', 'sm_enqueue_admin_assets');
