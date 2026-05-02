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

        $selected_layout = isset($_POST['post_layout']) ? sanitize_text_field(wp_unslash($_POST['post_layout'])) : 'list';
        $position        = isset($_POST['navigation_position']) ? sanitize_text_field(wp_unslash($_POST['navigation_position'])) : 'bottom';

        if (! in_array($selected_layout, ['list', 'accordion', 'grid'], true)) {
            $selected_layout = 'list';
        }
        if (! in_array($position, ['top', 'bottom'], true)) {
            $position = 'bottom';
        }

        update_option('post_layout', $selected_layout);
        update_option('navigation_position', $position);

        wp_safe_redirect(admin_url('admin.php?page=series-layouts&updated=1'));
        exit;
    }

    public static function render()
    {
        $selected_layout = get_option('post_layout', 'list');
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
                    name="post_layout_form">
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
                                    name="post_layout"
                                    type="radio"
                                    value="grid"
                                    <?php checked($selected_layout, 'grid'); ?> />
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
                                                Grid Layout
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
                            <!-- Layout Card: Accordion -->
                            <label class="relative cursor-pointer group">
                                <input
                                    class="peer sr-only"
                                    name="post_layout"
                                    type="radio"
                                    value="accordion"
                                    <?php checked($selected_layout, 'accordion'); ?> />
                                <div
                                    class="flex flex-col h-full bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md peer-checked:border-primary peer-checked:ring-2 peer-checked:ring-primary/20 peer-checked:bg-surface-container-low">
                                    <div
                                        class="h-32 bg-surface-container-high flex items-center justify-center border-b border-outline-variant p-md">
                                        <div class="flex flex-col gap-xs w-full h-full opacity-60">
                                            <div
                                                class="h-6 bg-primary rounded-sm w-full"></div>
                                            <div
                                                class="h-12 bg-white rounded-sm w-full border border-outline-variant"></div>
                                            <div
                                                class="h-6 bg-primary rounded-sm w-full"></div>
                                        </div>
                                    </div>
                                    <div class="p-md flex items-start justify-between">
                                        <div>
                                            <p
                                                class="font-title-lg text-body-md font-semibold text-on-surface">
                                                Accordion
                                            </p>
                                            <p
                                                class="font-body-sm text-label-sm text-on-surface-variant">
                                                Collapsible sectioned list
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
                            <!-- Layout Card: List -->
                            <label class="relative cursor-pointer group">
                                <input
                                    class="peer sr-only"
                                    name="post_layout"
                                    type="radio"
                                    value="list"
                                    <?php checked($selected_layout, 'list'); ?> />
                                <div
                                    class="flex flex-col h-full bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden transition-all duration-200 hover:shadow-md peer-checked:border-primary peer-checked:ring-2 peer-checked:ring-primary/20 peer-checked:bg-surface-container-low">
                                    <div
                                        class="h-32 bg-surface-container-high flex items-center justify-center border-b border-outline-variant p-md">
                                        <div class="flex flex-col gap-xs w-full h-full opacity-60">
                                            <div
                                                class="h-6 bg-primary rounded-sm w-full flex items-center px-2">
                                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                            </div>
                                            <div
                                                class="h-6 bg-primary rounded-sm w-full flex items-center px-2">
                                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                            </div>
                                            <div
                                                class="h-6 bg-primary rounded-sm w-full flex items-center px-2">
                                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-md flex items-start justify-between">
                                        <div>
                                            <p
                                                class="font-title-lg text-body-md font-semibold text-on-surface">
                                                List
                                            </p>
                                            <p
                                                class="font-body-sm text-label-sm text-on-surface-variant">
                                                Traditional vertical stack
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
                    <!-- Section 2: Segmented Control -->
                    <section
                        class="bg-surface-container-lowest rounded-xl border border-outline-variant p-xl shadow-sm">
                        <div
                            class="flex flex-col md:flex-row md:items-center justify-between gap-lg">
                            <div class="max-w-md">
                                <div class="flex items-center gap-sm mb-base">
                                    <span
                                        class="material-symbols-outlined text-primary"
                                        data-icon="navigation">navigation</span>
                                    <h3 class="font-title-lg text-title-lg">
                                        Series Navigation Position
                                    </h3>
                                </div>
                                <p class="font-body-sm text-on-surface-variant">
                                    Determine where the "Previous" and "Next" series controls will
                                    appear on single post pages.
                                </p>
                            </div>
                            <div
                                class="flex bg-surface-container-low p-sm rounded-lg border border-outline-variant w-fit">
                                <label class="cursor-pointer">
                                    <input
                                        class="peer sr-only"
                                        name="navigation_position"
                                        type="radio"
                                        value="top"
                                        <?php checked($position, 'top'); ?> />
                                    <span
                                        class="px-xl py-md rounded-md font-label-md transition-all peer-checked:bg-primary peer-checked:text-on-primary text-on-surface-variant hover:bg-surface-container-high block">
                                        Top
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input
                                        class="peer sr-only"
                                        name="navigation_position"
                                        type="radio"
                                        value="bottom"
                                        <?php checked($position, 'bottom'); ?> />
                                    <span
                                        class="px-xl py-md rounded-md font-label-md transition-all peer-checked:bg-primary peer-checked:text-on-primary text-on-surface-variant hover:bg-surface-container-high block">
                                        Bottom
                                    </span>
                                </label>
                            </div>
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
