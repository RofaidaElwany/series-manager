<?php

namespace Variants;

use Helpers\SeriesStyleHelper;

if (! defined('ABSPATH')) {
    exit;
}

class MediaGrid
{
    /**
     * @param array<int, \WP_Post|object> $posts
     * @param int $current_post_id
     * @param array<string, string> $style
     * @return string
     */
    public static function render($posts, $current_post_id, array $style = [])
    {

        ob_start();
?>
        <!-- SECTION -->
        <section class="sm-series-variant w-full bg-background font-body">

            <!-- GRID -->
            <div class="grid grid-cols-1 gap-4 mt-6
                        sm:gap-6
                        md:grid-cols-2 md:gap-8
                        lg:grid-cols-3 lg:mt-10">
                <?php foreach ($posts as $index => $p): ?>
                    <?php
                    $button_color = esc_attr($style['buttonColor'] ?? '#0066d9');
                    $is_current = ($p->ID == $current_post_id);
                    $link_url = get_permalink((int) $p->ID);
                    $post_obj = get_post($p->ID);
                    $avatar_img_url = '';
                    $thumb_id = get_post_thumbnail_id($p->ID);
                    if ($thumb_id) {
                        $avatar_img_url = wp_get_attachment_image_url((int) $thumb_id, 'large');
                    }

                    if (! $avatar_img_url && $post_obj && ! empty($post_obj->post_content)) {
                        if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $post_obj->post_content, $matches)) {
                            $avatar_img_url = $matches[1];
                        }
                    }
                    $placeholder_img_url = plugins_url('assets\img\1.jpg', dirname(__DIR__, 3) . '/series-manager.php');
                    $part_number = $index + 1;

                    $title_plain = trim(wp_strip_all_tags((string) $p->post_title));
                    $initial = $title_plain !== ''
                        ? (function_exists('mb_substr')
                            ? mb_strtoupper(mb_substr($title_plain, 0, 1, 'UTF-8'), 'UTF-8')
                            : strtoupper(substr($title_plain, 0, 1)))
                        : '?';

                    $currentCardStyle = $is_current
                        ? SeriesStyleHelper::inlineStyle([
                            'background-color' => $style['headerBackgroundColor'] ?? null,
                            'border-color' => $style['buttonColor'] ?? null,
                        ])
                        : '';

                    $currentBadgeStyle = $is_current
                        ? SeriesStyleHelper::inlineStyle([
                            'background-color' => $style['buttonColor'] ?? null,
                        ])
                        : '';

                    ?>

                    <!-- CARD -->
                    <a
                        href="<?php echo esc_url($link_url); ?>"
                        style="--sm-hover-color: <?php echo $button_color; ?>;
                                border: 1px solid <?php echo esc_attr($style['buttonColor'] ?? '#0066d9'); ?>;
                                border-radius: 1rem;"
                        class="sm-media-grid-card group <?php echo $is_current ? 'sm-media-grid-card--current' : 'sm-media-grid-card--default'; ?>"
                        <?php echo $currentCardStyle; ?>>

                        <!-- BADGE (ACTIVE ONLY) -->
                        <?php if ($is_current): ?>
                            <div class="absolute top-5 right-5 z-20">
                                <div class="px-4 py-2 rounded-full bg-primary text-white text-sm font-semibold shadow-md" <?php echo $currentBadgeStyle; ?>>
                                    Reading Now
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- IMAGE -->
                        <div class="sm-media-grid-card-img  <?php echo $is_current ? 'bg-[#e9f1ff]' : 'bg-[#f4ede8]'; ?>">
                            <?php if ($avatar_img_url): ?>
                                <img
                                    src="<?php echo esc_url($avatar_img_url); ?>"
                                    alt="<?php echo esc_attr($p->post_title); ?>"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div class="w-full h-full bg-blue-50 rounded-2xl border-2  overflow-hidden">
                                    <img
                                        src="<?php echo esc_url($placeholder_img_url); ?>"
                                        alt="No image available"
                                        class="w-full h-full object-cover">
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- CONTENT -->
                        <div class="p-2 md:p-3 sm-preview-lines">
                            <h3 class="sm-media-grid__title text-xl md:text-3xl font-bold text-slate-900 transition-colors">
                                <?php echo esc_html($p->post_title); ?>
                            </h3>
                            <div class="w-16 md:w-20 h-1 rounded-full mt-3 md:mt-4 mb-3 md:mb-5"
                                style="background: <?php echo esc_attr($style['buttonColor'] ?? '#0066d9'); ?>">
                            </div>
                            <span class="text-base font-semibold" ...>
                                Part <?php echo intval($part_number); ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
<?php
        return ob_get_clean();
    }
}
