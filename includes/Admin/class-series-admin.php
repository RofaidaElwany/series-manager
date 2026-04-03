<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/../Service/SeriesService.php';

use Service\SeriesService;

class SM_Series_Admin
{
    public static function init()
    {
        add_action('admin_menu', [self::class, 'register_menu']);
    }

    public static function register_menu()
    {
        // Main Menu
        add_menu_page(
            'Series Manager',
            'Series Manager',
            'manage_options',
            'series-manager',
            [self::class, 'render_dashboard'],
            'dashicons-list-view',
            25
        );

        // Submenu: Settings
        add_submenu_page(
            'series-manager',
            'Settings',
            'Settings',
            'manage_options',
            'series-manager-settings',
            [self::class, 'render_settings']
        );
    }

    public static function render_dashboard()
    {
?>
        <div class="wrap">
            <h1>Series Manager</h1>
            <p>Welcome 👋</p>
        </div>
    <?php
    }

    public static function render_settings()
    {
        if (isset($_POST['sm_supported_post_types']) && check_admin_referer('sm_save_settings')) {
            $supported = isset($_POST['supported_post_types']) ? (array) $_POST['supported_post_types'] : [];
            update_option('sm_supported_post_types', $supported);
            echo '<div class="notice notice-success"><p>Settings saved successfully</p></div>';
        }
    ?>
        <div class="wrap">
            <h1>Series Manager Settings</h1>

            <form method="post">
                <?php wp_nonce_field('sm_save_settings'); ?>

                <h2>Supported Post Types</h2>
                <p>Select the post types that should support series.</p>

                <?php
                $all_post_types = get_post_types([
                    'public' => true,
                    'show_ui' => true,
                ], 'objects');
                $supported = get_option('sm_supported_post_types', ['post']);

                foreach ($all_post_types as $post_type) {
                    if (in_array($post_type->name, ['attachment', 'revision', 'nav_menu_item'])) {
                        continue; // Skip non-content post types
                    }
                    $checked = in_array($post_type->name, $supported) ? 'checked' : '';
                    echo '<label>';
                    echo '<input type="checkbox" name="supported_post_types[]" value="' . esc_attr($post_type->name) . '" ' . $checked . '> ';
                    echo esc_html($post_type->label) ;
                    echo '</label><br>';
                }
                ?>

                <?php submit_button('Save Settings'); ?>
            </form>
        </div>
<?php
    }
}
