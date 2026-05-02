<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/../core/Service/SeriesService.php';

use Admin\Pages\DashboardPage;
use Admin\Pages\CPTpage;
use Admin\Pages\Layouts;
class SM_Series_Admin
{
    public static function init()
    {
        add_action('admin_menu', [self::class, 'register_menu']);
        add_action('admin_post_sm_save_layout_settings', [Layouts::class, 'handle_save']);
    }

    public static function register_menu()
    {
        // Main Menu
        add_menu_page(
            'Series Manager',
            'Series Manager',
            'manage_options',
            'series-manager',
            [DashboardPage::class, 'render'],
            'dashicons-list-view',
            25
        );

        // Submenu: Custom Post Types
        add_submenu_page(
            'series-manager',
            'Custom Post Types',
            'Custom Post Types',
            'manage_options',
            'available-custom-post-types',
            [CPTpage::class, 'render']
        );

        // submenu: Layouts
        add_submenu_page(
            'series-manager',
            'Layouts',
            'Layouts',
            'manage_options',
            'series-layouts',
            [Layouts::class, 'render']
        );
    }

    
}
