<?php

namespace Admin\Pages;

if (!defined('ABSPATH')) {
    exit;
}

class Layouts
{
    public static function handle_save()
    {
        if (! current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to perform this action.', 'series-manager'));
        }
        $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
        if (! wp_verify_nonce($nonce, 'sm_save_layout_settings')) {
            wp_die(esc_html__('Security check failed.', 'series-manager'));
        }
        $selected_layout = isset($_POST['content_variant']) ? sanitize_text_field(wp_unslash($_POST['content_variant'])) : 'link-list';
        update_option('content_variant', $selected_layout);
        wp_safe_redirect(admin_url('admin.php?page=series-manager&tab=layouts&updated=1'));
        exit;
    }

    public static function render()
    {
        $selected_layout = get_option('content_variant', 'link-list');
?>
        <main class="sm-layouts-main">
            <div class="sm-layouts-container">
                <?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php esc_html_e('Layout settings saved.', 'series-manager'); ?></p>
                    </div>
                <?php endif; ?>

                <header class="sm-layouts-header">
                    <h1 class="sm-layouts-title">Post Layout Settings</h1>
                    <p class="sm-layouts-description">
                        Configure how your content series and archives are displayed to your readers.
                        These settings will override global theme defaults.
                    </p>
                </header>

                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="sm-layouts-form" method="post" name="content_variant_form">
                    <input type="hidden" name="action" value="sm_save_layout_settings" />
                    <?php wp_nonce_field('sm_save_layout_settings'); ?>

                    <!-- Section: Layout Selection -->
                    <section class="sm-layouts-section">
                        <div class="sm-layouts-section-header">
                            <span class="material-symbols-outlined" data-icon="dashboard_customize">dashboard_customize</span>
                            <h3 class="sm-layouts-section-title">Display Layout</h3>
                        </div>

                        <div class="sm-layouts-cards">
                            <!-- Layout Card: Media Grid -->
                            <label class="sm-layout-card">
                                <input class="sm-layout-input" name="content_variant" type="radio" value="media-grid" <?php checked($selected_layout, 'media-grid'); ?> />
                                <div class="sm-layout-card-content">
                                    <div class="sm-layout-preview sm-preview-grid-container">
                                        <div class="sm-preview-grid">
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">image</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">image</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">image</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">image</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">image</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">image</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sm-layout-info">
                                        <div class="sm-layout-text">
                                            <p class="sm-layout-name">Media Grid Layout</p>
                                            <p class="sm-layout-desc">Clean multi-column display</p>
                                        </div>
                                        <div class="sm-layout-check">
                                            <span class="material-symbols-outlined" data-icon="check_circle">check_circle</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Layout Card: Link Grid -->
                            <label class="sm-layout-card">
                                <input class="sm-layout-input" name="content_variant" type="radio" value="link-grid" <?php checked($selected_layout, 'link-grid'); ?> />
                                <div class="sm-layout-card-content">
                                    <div class="sm-layout-preview sm-preview-grid-container">
                                        <div class="sm-preview-grid">
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">link</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">link</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">link</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">link</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">link</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-item">
                                                <span class="material-symbols-outlined">link</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sm-layout-info">
                                        <div class="sm-layout-text">
                                            <p class="sm-layout-name">Link Grid Layout</p>
                                            <p class="sm-layout-desc">Clean multi-column display</p>
                                        </div>
                                        <div class="sm-layout-check">
                                            <span class="material-symbols-outlined" data-icon="check_circle">check_circle</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Layout Card: Media List -->
                            <label class="sm-layout-card">
                                <input class="sm-layout-input" name="content_variant" type="radio" value="media-list" <?php checked($selected_layout, 'media-list'); ?> />
                                <div class="sm-layout-card-content">
                                    <div class="sm-layout-preview sm-preview-list-container">
                                        <div class="sm-preview-list">
                                            <div class="sm-preview-list-item">
                                                <span class="material-symbols-outlined">image</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-list-item">
                                                <span class="material-symbols-outlined">image</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-list-item">
                                                <span class="material-symbols-outlined">image</span>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sm-layout-info">
                                        <div class="sm-layout-text">
                                            <p class="sm-layout-name">Media List</p>
                                            <p class="sm-layout-desc">Display posts with featured images and titles</p>
                                        </div>
                                        <div class="sm-layout-check">
                                            <span class="material-symbols-outlined" data-icon="check_circle">check_circle</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Layout Card: Link List -->
                            <label class="sm-layout-card">
                                <input class="sm-layout-input" name="content_variant" type="radio" value="link-list" <?php checked($selected_layout, 'link-list'); ?> />
                                <div class="sm-layout-card-content">
                                    <div class="sm-layout-preview sm-preview-list-container">
                                        <div class="sm-preview-list">
                                            <div class="sm-preview-list-item-link">
                                                <div class="sm-preview-icon-link"><span class="material-symbols-outlined">link</span></div>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-list-item-link">
                                                <div class="sm-preview-icon-link"><span class="material-symbols-outlined">link</span></div>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                            <div class="sm-preview-list-item-link">
                                                <div class="sm-preview-icon-link"><span class="material-symbols-outlined">link</span></div>
                                                <div class="sm-preview-lines">
                                                    <div class="sm-preview-line sm-preview-line-title"></div>
                                                    <div class="sm-preview-line sm-preview-line-part"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sm-layout-info">
                                        <div class="sm-layout-text">
                                            <p class="sm-layout-name">Link List</p>
                                            <p class="sm-layout-desc">Minimal link-style display with icons and titles</p>
                                        </div>
                                        <div class="sm-layout-check">
                                            <span class="material-symbols-outlined" data-icon="check_circle">check_circle</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </section>

                    <div class="sm-layouts-actions">
                        <button class="button button-primary" type="submit">Save Settings</button>
                    </div>
                </form>
            </div>
        </main>
<?php
    }
}
