<?php

namespace Variants;

use Helpers\SeriesStyleHelper;

if (! defined('ABSPATH')) {
    exit;
}

class MediaList
{
    /**
     * @param array<int, \WP_Post|object> $posts
     * @param int $current_post_id
     * @param array<string, string> $style
     * @return string
     */
    public static function render($posts, $current_post_id, array $style = [])
    {
        $button_color = esc_attr($style['buttonColor'] ?? '#0066d9');
        $bg_color = esc_attr($style['headerBackgroundColor'] ?? '#e8f0fe');

        $accentStyle = SeriesStyleHelper::inlineStyle([
            'color' => $style['buttonColor'] ?? null,
        ]);

        $accentBgStyle = SeriesStyleHelper::inlineStyle([
            'background-color' => SeriesStyleHelper::withOpacity($style['buttonColor'] ?? null, 0.2),
            'color' => $style['buttonColor'] ?? null,
        ]);

        ob_start();
?>
        <!-- Posts List -->
        <div class="flex flex-col sm-series-variant w-full">
            <?php foreach ($posts as $index => $p): ?>
                <?php
                $is_current = ($p->ID == $current_post_id);
                $part_number = $index + 1;
                $link_url = get_permalink((int) $p->ID);

                // Same image lookup as MediaGrid
                $avatar_img_url = '';
                $thumb_id = get_post_thumbnail_id($p->ID);
                if ($thumb_id) {
                    $avatar_img_url = wp_get_attachment_image_url((int) $thumb_id, 'thumbnail');
                }
                $post_obj = get_post($p->ID);
                if (! $avatar_img_url && $post_obj && ! empty($post_obj->post_content)) {
                    if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $post_obj->post_content, $matches)) {
                        $avatar_img_url = $matches[1];
                    }
                }

                $title_plain = trim(wp_strip_all_tags((string) $p->post_title));
                $initial = $title_plain !== ''
                    ? (function_exists('mb_substr')
                        ? mb_strtoupper(mb_substr($title_plain, 0, 1, 'UTF-8'), 'UTF-8')
                        : strtoupper(substr($title_plain, 0, 1)))
                    : '?';
                ?>

                <?php if ($is_current): ?>
                    <!-- CURRENT POST -->
                    <a href="<?php echo esc_url($link_url); ?>"
                        style="background-color: <?php echo $bg_color; ?>;
                               border-left: 4px solid <?php echo $button_color; ?>;
                               box-shadow: 0 4px 24px <?php echo SeriesStyleHelper::withOpacity($style['buttonColor'] ?? '#0066d9', 0.12); ?>;"
                        class="flex items-center gap-3 md:gap-6 p-3 md:p-4 transition-all duration-200 rounded-xl">

                        <!-- IMAGE -->
                        <?php if ($avatar_img_url): ?>
                            <img
                                src="<?php echo esc_url($avatar_img_url); ?>"
                                alt="<?php echo esc_attr($p->post_title); ?>"
                                class="w-16 h-16 md:w-20 md:h-20 rounded-xl object-cover shrink-0">
                        <?php else: ?>
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-xl shrink-0 flex items-center justify-center text-white text-xl font-bold"
                                style="background-color: <?php echo $button_color; ?>">
                                <?php echo esc_html($initial); ?>
                            </div>
                        <?php endif; ?>

                        <!-- CONTENT -->
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-base md:text-lg text-slate-900 truncate">
                                <?php echo esc_html($p->post_title); ?>
                            </h4>
                            <span class="block text-xs md:text-sm font-semibold mb-1"
                                style="color: <?php echo $button_color; ?>">
                                Part <?php echo intval($part_number); ?> • Reading Now
                            </span>
                        </div>

                        <span class="material-symbols-outlined shrink-0"
                            style="color: <?php echo $button_color; ?>">
                            check_circle
                        </span>
                    </a>

                <?php else: ?>
                    <!-- OTHER POSTS -->
                    <a href="<?php echo esc_url($link_url); ?>"
                        style="--sm-hover-color: <?php echo $button_color; ?>;"
                        class="sm-media-list__item group flex items-center gap-3 md:gap-6 p-3 md:p-4 transition-all duration-200 hover:bg-slate-50">

                        <!-- IMAGE -->
                        <?php if ($avatar_img_url): ?>
                            <img
                                src="<?php echo esc_url($avatar_img_url); ?>"
                                alt="<?php echo esc_attr($p->post_title); ?>"
                                class="w-16 h-16 md:w-20 md:h-20 rounded-xl object-cover shrink-0">
                        <?php else: ?>
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-xl shrink-0 flex items-center justify-center bg-slate-100 text-slate-500 text-xl font-bold">
                                <?php echo esc_html($initial); ?>
                            </div>
                        <?php endif; ?>

                        <!-- CONTENT -->
                        <div class="flex-1 min-w-0">
                            <h4 class="sm-media-list__title font-bold text-base md:text-lg text-slate-900 truncate transition-colors">
                                <?php echo esc_html($p->post_title); ?>
                            </h4>
                            <span class="block text-xs md:text-sm text-slate-400 font-medium mb-1">
                                Part <?php echo intval($part_number); ?>
                            </span>
                        </div>

                        <span class="material-symbols-outlined shrink-0 text-slate-300 transition-colors group-hover:text-[var(--sm-hover-color)]">
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
