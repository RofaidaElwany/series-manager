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

        // Submenu: Post Types
        add_submenu_page(
            'series-manager',
            'Post Types',
            'Post Types',
            'manage_options',
            'series-manager-post-types',
            [self::class, 'render_post_types']
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

    public static function render_post_types()
    {
        if (isset($_POST['cpt_name']) && check_admin_referer('sm_add_cpt')) {
            $name  = $_POST['cpt_name'];
            $label = $_POST['cpt_label'];

            SeriesService::addCustomPostType($name, $label);

            echo '<div class="notice notice-success"><p>Added successfully</p></div>';
        }
    ?>
        <div class="wrap">
            <h1>Post Types</h1>

            <h2>Add Custom Post Type</h2>
            <form method="post">
                <?php wp_nonce_field('sm_add_cpt'); ?>

                <table class="form-table">
                    <tr>
                        <th><label>Name</label></th>
                        <td><input type="text" name="cpt_name" required /></td>
                    </tr>
                    <tr>
                        <th><label>Label</label></th>
                        <td><input type="text" name="cpt_label" required /></td>
                    </tr>
                </table>

                <?php submit_button('Add'); ?>
            </form>

            <hr>

            <h2>Existing Post Types</h2>
            <ul>
                <?php
                $custom_post_types = get_option('sm_custom_post_types', []);

                foreach ($custom_post_types as $cpt) {
                    echo '<li>' . esc_html($cpt['label']) . ' (' . esc_html($cpt['name']) . ')</li>';
                }
                ?>
            </ul>
        </div>
<?php
    }
}
