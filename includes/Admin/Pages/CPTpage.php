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
            echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully', 'series-manager') . '</p></div>';
        }
?>
        <div class="sm-cpt-wrapper">
            <div class="sm-cpt-container">
                <header class="sm-cpt-header">
                    <h1 class="sm-cpt-title">Custom Post Types</h1>
                    <p class="sm-cpt-intro">Select the post types that should support series functionality.</p>
                </header>

                <form method="post" class="sm-cpt-form">
                    <?php wp_nonce_field('sm_save_settings'); ?>

                    <section class="sm-cpt-section">
                        <h2 class="sm-cpt-section-title">Supported Post Types</h2>
                        <p class="sm-cpt-section-desc">Check the post types where you want to enable series features.</p>

                        <fieldset class="sm-cpt-fieldset">
                            <?php
                            $all_post_types = get_post_types([
                                'public'  => true,
                                'show_ui' => true,
                            ], 'objects');
                            $supported = get_option('sm_supported_post_types', ['post']);

                            if (empty($all_post_types)) {
                                echo '<p class="sm-cpt-empty">' . esc_html__('No public post types found.', 'series-manager') . '</p>';
                            } else {
                                echo '<div class="sm-cpt-checkboxes">';

                                foreach ($all_post_types as $post_type) {
                                    // Skip non-content post types
                                    if (in_array($post_type->name, ['attachment', 'revision', 'nav_menu_item'], true)) {
                                        continue;
                                    }

                                    $is_checked = in_array($post_type->name, $supported, true);
                            ?>
                                    <label class="sm-cpt-checkbox-label">
                                        <input
                                            type="checkbox"
                                            name="supported_post_types[]"
                                            value="<?php echo esc_attr($post_type->name); ?>"
                                            class="sm-cpt-checkbox"
                                            <?php checked($is_checked); ?> />
                                        <span class="sm-cpt-checkbox-text"><?php echo esc_html($post_type->label); ?></span>
                                        <span class="sm-cpt-checkbox-desc"><?php echo esc_html($post_type->name); ?></span>
                                    </label>
                            <?php
                                }

                                echo '</div>';
                            }
                            ?>
                        </fieldset>
                    </section>

                    <div class="sm-cpt-actions">
                        <?php submit_button('Save Settings', 'primary', 'submit', false, ['class' => 'button button-primary']); ?>
                    </div>
                </form>
            </div>
        </div>
<?php
    }
}
