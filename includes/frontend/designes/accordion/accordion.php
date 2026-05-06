<?php

namespace frontend\designes\accordion;

use Service\SeriesDataProvider;

if (! defined('ABSPATH')) {
    exit;
}

class AccordionLayout
{
    public static function render($attributes = [])
    {
        $current_post_id = get_the_ID();
        $series_items = SeriesDataProvider::getSeriesWithPosts($current_post_id);

        if (empty($series_items)) {
            return '';
        }

        ob_start();
?>

        <div class="w-full max-w-3xl mx-auto my-10 space-y-4">

            <?php foreach ($series_items as $item): ?>
                <?php
                $term = $item['term'];
                $posts = $item['posts'];
                $current_index = $item['current_index'];

                if (empty($posts)) {
                    continue;
                }
                ?>

                <!-- Accordion Item -->
                <div class="bg-surface-container-lowest rounded-3xl neumorphic-raised overflow-hidden">

                    <!-- Header -->
                    <button
                        class="accordion-toggle w-full flex items-center justify-between px-6 py-5 bg-surface-container-low text-left"
                        data-target="accordion-<?php echo esc_attr($term->term_id); ?>">

                        <div>
                            <h3 class="font-semibold text-lg text-on-surface">
                                <?php echo esc_html($term->name); ?>
                            </h3>

                            <?php if ($current_index !== false): ?>
                                <span class="text-xs text-primary/70">
                                    Part <?php echo intval($current_index + 1); ?> of <?php echo count($posts); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <span class="material-symbols-outlined text-primary transition-transform accordion-icon">
                            expand_more
                        </span>
                    </button>

                    <!-- Content -->
                    <div id="accordion-<?php echo esc_attr($term->term_id); ?>"
                        class="accordion-content hidden">

                        <div class="flex flex-col py-2">

                            <?php foreach ($posts as $p): ?>
                                <?php
                                $is_current = ($p->ID == $current_post_id);
                                $title_for_avatar = trim((string) $p->post_title);
                                $initial = strtoupper(substr($title_for_avatar, 0, 1));
                                $avatar_img_url = '';
                                if (function_exists('mb_substr') && $title_for_avatar !== '') {
                                    $initial = strtoupper(mb_substr($title_for_avatar, 0, 1));
                                }
                                if ($initial === '') {
                                    $initial = '?';
                                }
                                if (has_post_thumbnail($p->ID)) {
                                    $avatar_img_url = get_the_post_thumbnail_url($p->ID, 'thumbnail');
                                }
                                if (! $avatar_img_url && ! empty($p->post_content)) {
                                    if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $p->post_content, $matches)) {
                                        $avatar_img_url = $matches[1];
                                    }
                                }
                                ?>

                                <?php if ($is_current): ?>

                                    <!-- CURRENT -->
                                    <div class="relative flex items-center gap-4 bg-primary/10 px-6 py-4 border-l-4 border-primary">
                                        <div class="flex items-center justify-center rounded-lg bg-surface-container-low text-on-surface w-10 h-10 overflow-hidden">
                                            <?php if ($avatar_img_url): ?>
                                                <img
                                                    src="<?php echo esc_url($avatar_img_url); ?>"
                                                    alt="<?php echo esc_attr($p->post_title); ?>"
                                                    class="w-full h-full object-cover" />
                                            <?php else: ?>
                                                <span class="text-sm font-bold tracking-wide uppercase text-primary">
                                                    <?php echo esc_html($initial); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex flex-col flex-1 truncate">
                                            <p class="text-primary font-semibold">
                                                <?php echo esc_html($p->post_title); ?>
                                            </p>
                                            <span class="text-xs text-primary/70">
                                                You are here
                                            </span>
                                        </div>

                                        <div class="flex items-center justify-center rounded-lg bg-primary/20 text-primary w-10 h-10">
                                            <span class="material-symbols-outlined">check_circle</span>
                                        </div>
                                    </div>

                                <?php else: ?>

                                    <!-- OTHER -->
                                    <a href="<?php echo esc_url(get_permalink($p)); ?>"
                                        class="group flex items-center gap-4 px-6 py-4 hover:bg-surface-container-low transition">
                                        <div class="flex items-center justify-center rounded-lg bg-surface-container-low text-on-surface w-10 h-10 overflow-hidden">
                                            <?php if ($avatar_img_url): ?>
                                                <img
                                                    src="<?php echo esc_url($avatar_img_url); ?>"
                                                    alt="<?php echo esc_attr($p->post_title); ?>"
                                                    class="w-full h-full object-cover" />
                                            <?php else: ?>
                                                <span class="text-sm font-bold tracking-wide uppercase text-primary">
                                                    <?php echo esc_html($initial); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <p class="text-on-surface font-medium flex-1 truncate group-hover:text-primary">
                                            <?php echo esc_html($p->post_title); ?>
                                        </p>

                                        <div class="text-gray-300 group-hover:text-primary">
                                            <span class="material-symbols-outlined">chevron_right</span>
                                        </div>

                                    </a>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

<?php
        return ob_get_clean();
    }
}
