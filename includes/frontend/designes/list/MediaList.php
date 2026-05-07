<?php

namespace frontend\designes\list;

require_once __DIR__ . '/../../../core/Service/SeriesDataProvider.php';

use Service\SeriesDataProvider;

if (! defined('ABSPATH')) {
    exit;
}

class MediaList
{
    public static function render($attributes)
    {
        $current_post_id = get_the_ID();
        $series_items = SeriesDataProvider::getSeriesWithPosts($current_post_id);

        if (empty($series_items)) {
            return '';
        }

        ob_start();
?>
        <div class="flex justify-center my-7">
            <?php foreach ($series_items as $item): ?>
                <?php
                $term = $item['term'];
                $posts = $item['posts'];
                $current_index = $item['current_index'];
                $total_posts = $item['total_posts'] ?? count($posts);
                $prev_post = $item['prev_post'] ?? null;
                $next_post = $item['next_post'] ?? null;

                if (empty($posts)) {
                    continue;
                }
                ?>

                <div class="w-full max-w-4xl mt-20">
                    <!-- Series Header -->
                    <div class="flex items-center justify-between p-5 bg-primary/10 border border-outline-variant/30 rounded-xl shadow-sm">

                        <div>
                            <h2 class="text-headline-sm font-display text-on-surface">
                                <?php echo esc_html($term->name); ?>
                            </h2>
                        </div>

                        <?php if ($current_index !== false): ?>
                            <div class="bg-primary text-on-primary px-4 py-2 rounded-full text-label-md font-medium shadow-sm">
                                Part <?php echo intval($current_index + 1); ?> of <?php echo intval($total_posts); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Posts List -->
                    <div class="space-y-4 mb-12">
                        <?php foreach ($posts as $p): ?>
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
                                <div class="flex items-center gap-4 p-4 rounded-xl bg-primary/10 border-l-4 border-primary shadow-sm">

                                    <?php if ($avatar_img_url): ?>
                                        <img
                                            src="<?php echo esc_url($avatar_img_url); ?>"
                                            alt="<?php echo esc_attr($p->post_title); ?>"
                                            class="w-20 h-20 rounded-xl object-cover ring-2 ring-primary/20">
                                    <?php else: ?>
                                        <div class="w-20 h-20 rounded-xl bg-primary/20 flex items-center justify-center text-primary text-title-lg font-bold">
                                            <?php echo esc_html($initial); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="flex-1">
                                        <h4 class="text-title-md font-semibold text-primary-dim">
                                            <?php echo esc_html($p->post_title); ?>
                                        </h4>
                                        <span class="text-label-md text-primary font-semibold">
                                            Part <?php echo intval(array_search($p, $posts) + 1); ?> • Reading Now
                                        </span>
                                    </div>

                                    <span class="material-symbols-outlined text-primary text-3xl">
                                        check_circle
                                    </span>
                                </div>
                            <?php else: ?>
                                <!-- OTHER POSTS -->
                                <a href="<?php echo esc_url($link_url); ?>"
                                    class="group flex items-center gap-4 p-4 rounded-xl hover:bg-surface-container-low transition-all duration-200">

                                    <?php if ($avatar_img_url): ?>
                                        <img
                                            src="<?php echo esc_url($avatar_img_url); ?>"
                                            alt="<?php echo esc_attr($p->post_title); ?>"
                                            class="w-20 h-20 rounded-xl object-cover">
                                    <?php else: ?>
                                        <div class="w-20 h-16 rounded-xl bg-surface-container flex items-center justify-center text-on-surface-variant text-title-lg font-bold">
                                            <?php echo esc_html($initial); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="flex-1">

                                        <h4 class="text-title-md font-medium text-on-surface group-hover:text-primary transition-colors">
                                            <?php echo esc_html($p->post_title); ?>
                                        </h4>
                                        <span class="text-label-md text-on-surface-variant">
                                            Part <?php echo intval(array_search($p, $posts) + 1); ?>
                                        </span>

                                    </div>

                                    <span class="material-symbols-outlined text-on-surface-variant group-hover:text-primary transition-colors">
                                        arrow_forward
                                    </span>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Navigation Footer -->
                    <!-- Navigation Footer -->
                    <div class="flex justify-between items-center pt-8 border-t border-outline-variant/20">

                        <!-- Previous -->
                        <?php if ($prev_post): ?>
                            <a href="<?php echo esc_url(get_permalink($prev_post)); ?>"
                                class="flex items-center gap-2 px-6 py-3 rounded-full text-on-surface hover:bg-surface-container-low transition-all active:scale-95">

                                <span class="material-symbols-outlined">arrow_back</span>

                                <span class="text-label-lg font-medium">
                                    Previous Post
                                </span>
                            </a>
                        <?php else: ?>
                            <div class="px-6 py-3 rounded-full opacity-40 bg-surface-container-low text-on-surface-variant">
                                No Previous
                            </div>
                        <?php endif; ?>

                        <!-- Next -->
                        <?php if ($next_post): ?>
                            <a href="<?php echo esc_url(get_permalink($next_post)); ?>"
                                class="flex items-center gap-2 px-6 py-3 rounded-full bg-primary text-on-primary hover:bg-primary-dim transition-all active:scale-95 shadow-sm">

                                <span class="text-label-lg font-medium">
                                    Next Post
                                </span>

                                <span class="material-symbols-outlined">arrow_forward</span>
                            </a>
                        <?php else: ?>
                            <div class="px-6 py-3 rounded-full opacity-40 bg-surface-container-low text-on-surface-variant">
                                No Next
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

<?php
        return ob_get_clean();
    }
}
