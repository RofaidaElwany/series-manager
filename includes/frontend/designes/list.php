<?php

namespace frontend\designes;

use Service\SeriesService;

if (! defined('ABSPATH')) {
    exit;
}

class ListLayout
{


    public static function render($attributes)
    {
        $current_post_id = get_the_ID();

        // =========================
        // Get Series Terms
        // =========================
        $terms = wp_get_post_terms($current_post_id, 'series');

        if (empty($terms) || is_wp_error($terms)) {
            return '';
        }

        ob_start();
?>

        <div class="flex justify-center my-7">
            <?php foreach ($terms as $term): ?>

                <?php
                // =========================
                // Get Posts in Series
                // =========================
                $posts = get_posts([
                    'post_type'   => SeriesService::getSupportedPostTypes(),
                    'numberposts' => -1,
                    'tax_query'   => [
                        [
                            'taxonomy' => 'series',
                            'terms'    => $term->term_id,
                        ],
                    ],
                    'orderby' => 'date',
                    'order'   => 'ASC',
                ]);

                if (empty($posts)) continue;

                // =========================
                // Detect Current Index
                // =========================
                $post_ids = wp_list_pluck($posts, 'ID');

                $current_index = array_search(
                    (int)$current_post_id,
                    array_map('intval', $post_ids),
                    true
                );

                $total_posts = count($posts);

                // =========================
                // Navigation
                // =========================
                $prev_post = null;
                $next_post = null;

                if ($current_index !== false) {
                    $prev_post = $current_index > 0 ? $posts[$current_index - 1] : null;
                    $next_post = $current_index < $total_posts - 1 ? $posts[$current_index + 1] : null;
                }
                ?>


                <div class="w-full max-w-3xl bg-surface-container-lowest rounded-3xl neumorphic-raised overflow-hidden mt-20 pt-6 border-t">

                    <!-- Header -->
                    <div class="px-6 py-5 bg-surface-container-low flex items-center justify-between">
                        <div class="flex flex-col">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                Series: <?php echo esc_html($term->name); ?>
                            </h2>
                        </div>

                        <?php if ($current_index !== false): ?>
                            <div class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-medium">
                                Part <?php echo intval($current_index + 1); ?> of <?php echo intval($total_posts); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Posts List -->
                    <div class="flex flex-col py-2">

                        <?php foreach ($posts as $index => $p): ?>
                            <?php
                            $is_current = ($p->ID == $current_post_id);
                            $link_url = get_permalink($p);
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

                                <!-- CURRENT POST -->
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

                                <!-- OTHER POSTS -->
                                <a href="<?php echo esc_url($link_url); ?>"
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

                    <!-- Navigation Footer -->
                    <div class="px-6 pb-6 pt-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/30">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Previous -->
                            <?php if ($prev_post): ?>
                                <a href="<?php echo esc_url(get_permalink($prev_post)); ?>"
                                    class="group flex items-center gap-4 p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-primary/50 hover:shadow-md transition-all">

                                    <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 group-hover:bg-primary group-hover:text-white">
                                        <span class="material-symbols-outlined">arrow_back</span>
                                    </div>

                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-400">Previous</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            <?php echo esc_html($prev_post->post_title); ?>
                                        </span>
                                    </div>
                                </a>
                            <?php else: ?>
                                <div class="opacity-50 p-4 bg-white dark:bg-gray-800 border rounded-xl">
                                    <span class="text-sm text-gray-400">No previous post</span>
                                </div>
                            <?php endif; ?>

                            <!-- Next -->
                            <?php if ($next_post): ?>
                                <a href="<?php echo esc_url(get_permalink($next_post)); ?>"
                                    class="group flex items-center justify-end gap-4 p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-primary/50 hover:shadow-md transition-all text-right">

                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-400">Next</span>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                            <?php echo esc_html($next_post->post_title); ?>
                                        </span>
                                    </div>

                                    <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 group-hover:bg-primary group-hover:text-white">
                                        <span class="material-symbols-outlined">arrow_forward</span>
                                    </div>
                                </a>
                            <?php else: ?>
                                <div class="opacity-50 p-4 bg-white dark:bg-gray-800 border rounded-xl text-right">
                                    <span class="text-sm text-gray-400">No next post</span>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

<?php
        return ob_get_clean();
    }
}
