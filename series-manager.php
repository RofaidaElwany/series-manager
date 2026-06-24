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
require_once plugin_dir_path(__FILE__) . '/includes/Core/Plugin.php';

// Taxonomy logic
require_once plugin_dir_path(__FILE__) . '/includes/taxonomy/class-series-taxonomy.php';
require_once plugin_dir_path(__FILE__) . '/includes/taxonomy/class-series-taxonomy-edit.php';

// Frontend rendering
require_once plugin_dir_path(__FILE__) . '/includes/frontend/renderer.php';

// Admin panel
require_once plugin_dir_path(__FILE__) . '/includes/Admin/Admin.php';

/* =====================================================
 *  Admin Initialization
 * ===================================================== */

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
function series_manager_init()
{
    // Register taxonomy and its edit screens
    SM_Series_Taxonomy::register();
    SM_Series_Taxonomy_Edit::register();

    // Initialize frontend block rendering
    SM_Series_Renderer::init();

    // Boot core plugin composition root
    SM_Series_Plugin::init();
}

/**
 * Hook into WordPress initialization
 */
add_action('init', 'series_manager_init');



/* =====================================================
 *  FRONTEND ASSETS (Public Site)
 * ===================================================== */

/**
 * Enqueue frontend JS only on series taxonomy pages
 */
function series_manager_enqueue_frontend_assets()
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
add_action('wp_enqueue_scripts', 'series_manager_enqueue_frontend_assets');


/**
 * Enqueue accordion JavaScript on single posts
 */
function series_manager_enqueue_accordion_assets()
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
add_action('wp_enqueue_scripts', 'series_manager_enqueue_accordion_assets');


/**
 * Enqueue frontend styles (Tailwind + Fonts)
 */
function series_manager_enqueue_front_assets()
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
add_action('wp_enqueue_scripts', 'series_manager_enqueue_front_assets');

/* =====================================================
 *  BLOCK EDITOR (Gutenberg) ASSETS
 * ===================================================== */

/**
 * Enqueue assets for block editor (JS + CSS + AJAX data)
 */
function series_manager_enqueue_post_editor_assets()
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    // Limit to specific editor screens when a screen object is available.
    if ($screen && ! in_array($screen->base, ['post', 'site-editor', 'widgets', 'customize'], true)) {
        return;
    }

    // Restrict to supported post types only when a screen object is available.
    if ($screen && $screen->post_type) {
        $service   = new \Service\SeriesService();
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
add_action('enqueue_block_editor_assets', 'series_manager_enqueue_post_editor_assets');
add_action('enqueue_widgets_block_editor_assets', 'series_manager_enqueue_post_editor_assets');

function series_manager_enqueue_block_styles()
{
    $css_file_path = plugin_dir_path(__FILE__) . 'build/index.css';
    if (file_exists($css_file_path)) {
        wp_enqueue_style(
            'sm-series-block-styles',
            plugins_url('build/index.css', __FILE__),
            [],
            filemtime($css_file_path)
        );
    }
}
add_action('enqueue_block_assets', 'series_manager_enqueue_block_styles');

/**
 * Shared layout width fallback when the theme does not expose global content size.
 */
function series_manager_enqueue_layout_width_fallback()
{
    $css = ':root{--sm-series-layout-max-width:var(--wp--style--global--content-size,650px);}';

    wp_add_inline_style('sm-series-block-styles', $css);
    wp_add_inline_style('sm-series-frontend', $css);
}
add_action('enqueue_block_assets', 'series_manager_enqueue_layout_width_fallback', 20);
add_action('wp_enqueue_scripts', 'series_manager_enqueue_layout_width_fallback', 20);
add_action('enqueue_block_editor_assets', 'series_manager_enqueue_layout_width_fallback', 20);

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
function series_manager_enqueue_admin_assets(string $hook): void
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
add_action('admin_enqueue_scripts', 'series_manager_enqueue_admin_assets');

wp_enqueue_style(
    'series-manager-admin-responsive',
    plugin_dir_url(__FILE__) . 'assets/css/series-manager-responsive.css'
);
