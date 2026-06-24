<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/../Services/SeriesService.php';
require_once __DIR__ . '/Pages/CPTpage.php';
require_once __DIR__ . '/Pages/Layouts.php';

use Admin\Pages\DashboardPage;
use Admin\Pages\CPTpage;
use Admin\Pages\Layouts;

class SM_Series_Admin
{
    // Define your tabs here
    private static $tabs = [

        'custom-post-types' => [
            'label' => 'Custom Post Types',
            'class' => CPTpage::class,
        ],
        'layouts' => [
            'label' => 'Layouts',
            'class' => Layouts::class,
        ],
    ];

    public static function init()
    {
        add_action('admin_menu', [self::class, 'register_menu']);
        add_action('admin_post_sm_save_layout_settings', [Layouts::class, 'handle_save']);
    }

    public static function register_menu()
    {
        // Only register ONE main menu page
        add_menu_page(
            'Series Manager',
            'Series Manager',
            'manage_options',
            'series-manager',
            [self::class, 'render_tabbed_page'],
            'dashicons-list-view',
            25
        );
        
        // Remove submenus - don't add any submenu pages
    }

    public static function render_tabbed_page()
    {
        // Get current tab from URL parameter, default to 'dashboard'
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';

        // Verify tab exists
        if (!isset(self::$tabs[$current_tab])) {
            $current_tab = 'dashboard';
        }

        ?>
        <div class="wrap">
            <!-- Tab Navigation -->
            <div class="nav-tab-wrapper">
                <?php foreach (self::$tabs as $tab_key => $tab_data): ?>
                    <a href="<?php echo esc_url(add_query_arg('tab', $tab_key)); ?>" 
                       class="nav-tab <?php echo $current_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html($tab_data['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Tab Content -->
            <div class="tab-content shadow-xl">
                <?php
                // Instantiate and render the appropriate page class
                $tab_class = self::$tabs[$current_tab]['class'];
                if (class_exists($tab_class) && method_exists($tab_class, 'render')) {
                    $tab_class::render();
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Helper function to add a new tab
     * @param int $tab_key
     * @param string $label
     * @param string $class_name
     */
    public static function add_tab($tab_key, $label, $class_name)
    {
        self::$tabs[$tab_key] = [
            'label' => $label,
            'class' => $class_name,
        ];
    }
}