<?php

namespace Variants;

if (! defined('ABSPATH')) {
    exit;
}

class MediaList
{
    /**
     * @param array<int, \WP_Post|object> $posts
     * @param int $current_post_id
     * @return string
     */
    public static function render($posts, $current_post_id)
    {
        ob_start();
?>
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
                    <div class="flex items-center gap-4 p-2 rounded-xl bg-primary/10 border-l-4 border-primary shadow-sm">

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

                            <h4 class="text-title-lg font-medium text-on-surface group-hover:text-primary transition-colors">
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
<?php
        return ob_get_clean();
    }
}
