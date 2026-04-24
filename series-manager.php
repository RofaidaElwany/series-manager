<?php

/**
 * Plugin Name: Series Manager
 * Description: Manage post series and navigation between them.
 * Version: 0.1.0
 * Author: Rofaida
 */

if (! defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . '/includes/core/Repository/SeriesRepository.php';
require_once plugin_dir_path(__FILE__) . '/includes/core/Helpers/SeriesFormatter.php';
require_once plugin_dir_path(__FILE__) . '/includes/core/Controller/SeriesController.php';
require_once plugin_dir_path(__FILE__) . '/includes/Admin/taxonomy/class-series-taxonomy.php';
require_once plugin_dir_path(__FILE__) . '/includes/Admin/taxonomy/class-series-taxonomy-edit.php';
require_once plugin_dir_path(__FILE__) . '/includes/frontend/class-series-block-render.php';
require_once plugin_dir_path(__FILE__) . '/includes/Admin/class-series-admin.php';
require_once plugin_dir_path(__FILE__) . '/includes/core/Service/SeriesService.php';

use Service\SeriesService;

SM_Series_Admin::init();

/* ========= INIT FUNCTION ========= */

function sm_series_manager_init()
{
    SM_Series_Taxonomy::register();
    SM_Series_Taxonomy_Edit::register();
    SM_Series_Block_Render::init();

    global $wpdb;

    $repository = new SeriesRepository($wpdb);
    $service = new SeriesService();
    $formatter  = new SeriesFormatter();

    new SeriesController($repository, $service, $formatter);
}

/* ========= HOOK ========= */

add_action('init', 'sm_series_manager_init');

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

function sm_enqueue_post_editor_assets()
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    // If we have a screen object, only enqueue for post editor, site editor, widgets, or the customizer.
    // This ensures the block is available in widget-area/block-based widget editors too.
    if ($screen && ! in_array($screen->base, ['post', 'site-editor', 'widgets', 'customize'], true)) {
        return;
    }

    // Only enqueue for post types that support series when editing posts.
    if ($screen && $screen->post_type) {
        $service = new SeriesService();
        $supported = $service->getSupportedPostTypes();
        if (! in_array($screen->post_type, $supported, true)) {
            return;
        }
    }

    $asset_file_path = plugin_dir_path(__FILE__) . 'build/index.asset.php';

    if (! file_exists($asset_file_path)) {
        return;
    }

    $asset_file = include $asset_file_path;

    $script_handle = 'sm-series-post-editor';

    wp_enqueue_script(
        $script_handle,
        plugins_url('build/index.js', __FILE__),
        array_merge($asset_file['dependencies'] ?? ['wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data'], ['wp-api-fetch']),
        $asset_file['version'] ?? filemtime(plugin_dir_path(__FILE__) . 'build/index.js'),
        true
    );

    // Only enqueue CSS if the file exists
    $css_file_path = plugin_dir_path(__FILE__) . 'build/index.css';
    if (file_exists($css_file_path)) {
        wp_enqueue_style(
            'sm-series-post-editor',
            plugins_url('build/index.css', __FILE__),
            [],
            $asset_file['version'] ?? filemtime($css_file_path)
        );
    }

    // Localize the script with AJAX data
    $nonce = wp_create_nonce('sm_series_nonce');

    wp_localize_script(
        $script_handle,
        'SMSeries',
        [
            'nonce'   => $nonce,
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]
    );
}
add_action('enqueue_block_editor_assets', 'sm_enqueue_post_editor_assets');



// Enqueue front-end styles

function sm_enqueue_front_assets()
{
    // Enqueue Inter font
    wp_enqueue_style(
        'inter-font',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        [],
        null
    );

    // Enqueue Material Symbols font
    wp_enqueue_style(
        'material-symbols-font',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap',
        [],
        null
    );

    // Enqueue compiled Tailwind styles
    wp_enqueue_style(
        'sm-series-frontend',
        plugins_url('build/index.css', __FILE__),
        [],
        filemtime(plugin_dir_path(__FILE__) . 'build/index.css')
    );
}
add_action('wp_enqueue_scripts', 'sm_enqueue_front_assets');
add_filter('the_content', 'sm_append_series_to_content');

function sm_append_series_to_content($content)
{
    if (! is_singular('post')) {
        return $content;
    }

    if (! in_the_loop() || ! is_main_query()) {
        return $content;
    }

    $series_html = SM_Series_Block_Render::render([]);

    if (! $series_html) {
        return $content;
    }

    return $content . $series_html;
}

// Enqueue Material Symbols font for both editor and front-end
add_action('enqueue_block_assets', function () {
    wp_enqueue_style(
        'material-symbols',
        'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined',
        [],
        null
    );
});

// Register taxonomy on init
add_action('init', function () {
    SM_Series_Taxonomy::register();
});
