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
        $position        = isset($_POST['navigation_position']) ? sanitize_text_field(wp_unslash($_POST['navigation_position'])) : 'bottom';

        if (! in_array($selected_layout, ['link-list', 'media-list', 'media-grid'], true)) {
            $selected_layout = 'link-list';
        }
        if (! in_array($position, ['top', 'bottom'], true)) {
            $position = 'bottom';
        }

        update_option('content_variant', $selected_layout);
        update_option('navigation_position', $position);

        wp_safe_redirect(admin_url('admin.php?page=series-layouts&updated=1'));
        exit;
    }

    public static function render()
    {
        $selected_layout = get_option('content_variant', 'link-list');
        $position = get_option('navigation_position', '');
        if ($position === '') {
            $position = 'bottom';
            update_option('navigation_position', $position);
        }
?>
        <main class="pt-16 min-h-screen">
            <div class="max-w-5xl mx-auto px-xl py-xl">
                <?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php esc_html_e('Layout settings saved.', 'series-manager'); ?></p>
                    </div>
                <?php endif; ?>
                <header class="mb-xl">
                    <h1 class="font-headline-lg text-headline-lg text-on-surface mb-xs">
                        Post Layout Settings
                    </h1>
                    <p class="font-body-md text-on-surface-variant max-w-2xl">
                        Configure how your content series and archives are displayed to your
                        readers. These settings will override global theme defaults.
                    </p>
                </header>
                <form
                    action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                    class="space-y-xl"
                    method="post"
                    name="content_variant_form">
                    <input type="hidden" name="action" value="sm_save_layout_settings" />
                    <?php wp_nonce_field('sm_save_layout_settings'); ?>
                    <!-- Section 1: Layout Selection -->
                    <section
                        class="bg-surface-container-lowest rounded-xl border border-outline-variant p-xl shadow-sm">
                        <div class="flex items-center gap-sm mb-lg">
                            <span
                                class="material-symbols-outlined text-primary"
                                data-icon="dashboard_customize">dashboard_customize</span>
                            <h3 class="font-title-lg text-title-lg">Display Layout</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
                            <!-- Layout Card: Grid -->
                            <label class="relative cursor-pointer group">
                                <input
                                    class="peer sr-only"
                                    name="content_variant"
                                    type="radio"
                                    value="media-grid"
                                    <?php checked($selected_layout, 'media-grid'); ?> />
                                <div
                                    class="flex flex-col h-full bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md peer-checked:border-primary peer-checked:ring-2 peer-checked:ring-primary/20 peer-checked:bg-surface-container-low">
                                    <div
                                        class="h-32 bg-surface-container-high flex items-center justify-center border-b border-outline-variant p-md">
                                        <div
                                            class="grid grid-cols-3 gap-xs w-full h-full opacity-60">
                                            <div class="bg-primary rounded-sm"></div>
                                            <div class="bg-primary rounded-sm"></div>
                                            <div class="bg-primary rounded-sm"></div>
                                            <div class="bg-primary rounded-sm"></div>
                                            <div class="bg-primary rounded-sm"></div>
                                            <div class="bg-primary rounded-sm"></div>
                                        </div>
                                    </div>
                                    <div class="p-md flex items-start justify-between">
                                        <div>
                                            <p
                                                class="font-title-lg text-body-md font-semibold text-on-surface">
                                                Media Grid Layout
                                            </p>
                                            <p
                                                class="font-body-sm text-label-sm text-on-surface-variant">
                                                Clean multi-column display
                                            </p>
                                        </div>
                                        <div class="hidden peer-checked:block text-primary">
                                            <span
                                                class="material-symbols-outlined"
                                                data-icon="check_circle"
                                                data-weight="fill"
                                                style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <!-- Layout Card: List with img -->
                            <label class="relative cursor-pointer group">
                                <input
                                    class="peer sr-only"
                                    name="content_variant"
                                    type="radio"
                                    value="media-list"
                                    <?php checked($selected_layout, 'media-list'); ?> />
                                <div
                                    class="flex flex-col h-full bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md peer-checked:border-primary peer-checked:ring-2 peer-checked:ring-primary/20 peer-checked:bg-surface-container-low">
                                    <div
                                        class="h-32 bg-surface-container-high flex items-center justify-center border-b border-outline-variant p-md">
                                        <div class="flex flex-col gap-xs w-full h-full opacity-60">
                                            <div
                                                class="h-6 bg-primary rounded-sm w-full flex items-center px-2">
                                                <div class="w-4 h-4 bg-white rounded-full mr-2"></div> <!-- صورة -->
                                                <div class="h-2 bg-white/70 rounded w-1/3"></div> <!-- نص -->
                                            </div>
                                            <div
                                                class="h-6 bg-primary rounded-sm w-full flex items-center px-2">
                                                <div class="w-4 h-4 bg-white rounded-full mr-2"></div> <!-- صورة -->
                                                <div class="h-2 bg-white/70 rounded w-1/3"></div> <!-- نص -->
                                            </div>
                                            <div
                                                class="h-6 bg-primary rounded-sm w-full flex items-center px-2">
                                                <div class="w-4 h-4 bg-white rounded-full mr-2"></div> <!-- صورة -->
                                                <div class="h-2 bg-white/70 rounded w-1/3"></div> <!-- نص -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-md flex items-start justify-between">
                                        <div>
                                            <p
                                                class="font-title-lg text-body-md font-semibold text-on-surface">
                                                Media List
                                            </p>
                                            <p
                                                class="font-body-sm text-label-sm text-on-surface-variant">
                                                Display posts with featured images and titles
                                            </p>
                                        </div>
                                        <div class="hidden peer-checked:block text-primary">
                                            <span
                                                class="material-symbols-outlined"
                                                data-icon="check_circle"
                                                data-weight="fill"
                                                style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer group">
                                <input
                                    class="peer sr-only"
                                    name="content_variant"
                                    type="radio"
                                    value="link-list"
                                    <?php checked($selected_layout, 'link-list'); ?> />
                                <div
                                    class="flex flex-col h-full bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md peer-checked:border-primary peer-checked:ring-2 peer-checked:ring-primary/20 peer-checked:bg-surface-container-low">
                                    <div
                                        class="h-32 bg-surface-container-high flex items-center justify-center border-b border-outline-variant p-md">
                                        <div class="flex flex-col gap-xs w-full h-full opacity-60">
                                            <div
                                                class="h-6 bg-primary rounded-sm w-full flex items-center mr-2">
                                                <div class="flex items-center justify-center rounded-lg bg-primary text-on-primary w-10 ">
                                                    <span class="material-symbols-outlined">link</span>
                                                </div>
                                                <div class="h-2 bg-white/70 rounded w-1/3"></div> <!-- نص -->

                                            </div>
                                            <div
                                                class="h-6 bg-primary rounded-sm w-full flex items-center mr-2">
                                                <div class="flex items-center justify-center rounded-lg bg-primary text-on-primary w-10 ">
                                                    <span class="material-symbols-outlined">link</span>
                                                </div>
                                                <div class="h-2 bg-white/70 rounded w-1/3"></div> <!-- نص -->

                                            </div>
                                            <div
                                                class="h-6 bg-primary rounded-sm w-full flex items-center mr-2">
                                                <div class="flex items-center justify-center rounded-lg bg-primary text-on-primary w-10 ">
                                                    <span class="material-symbols-outlined">link</span>
                                                </div>
                                                <div class="h-2 bg-white/70 rounded w-1/3"></div> <!-- نص -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-md flex items-start justify-between">
                                        <div>
                                            <p
                                                class="font-title-lg text-body-md font-semibold text-on-surface">
                                                Link List
                                            </p>
                                            <p
                                                class="font-body-sm text-label-sm text-on-surface-variant">
                                                Minimal link-style display with icons and titles
                                            </p>
                                        </div>
                                        <div class="hidden peer-checked:block text-primary">
                                            <span
                                                class="material-symbols-outlined"
                                                data-icon="check_circle"
                                                data-weight="fill"
                                                style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </section>

                    <div class="flex justify-end">
                        <button
                            class="px-xl py-md rounded-md font-label-md bg-primary text-on-primary hover:opacity-90 transition"
                            type="submit"
                            value="1">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </main>
<?php
    }
}
