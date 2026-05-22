<?php

namespace Admin\Pages;

if (!defined('ABSPATH')) {
    exit;
}
class CPTpage
{
    public static function render()
    {
        if (isset($_POST['supported_post_types']) && check_admin_referer('sm_save_settings')) {
            $supported = isset($_POST['supported_post_types'])
                ? array_map('sanitize_text_field', wp_unslash((array) $_POST['supported_post_types']))
                : [];
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
                    echo '<label>';
                    echo '<input type="checkbox" 
                            name="supported_post_types[]" 
                            value="' . esc_attr($post_type->name) . '" ';

                    checked(in_array($post_type->name, $supported, true));
                    echo '> ';
                    echo esc_html($post_type->label);
                    echo '</label><br>';
                }
                ?>

                <?php submit_button('Save Settings'); ?>
            </form>
        </div>
<?php
    }
}
