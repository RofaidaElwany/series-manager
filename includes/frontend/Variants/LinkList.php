<?php

namespace Variants;

use Helpers\SeriesStyleHelper;

if (! defined('ABSPATH')) {
    exit;
}

class LinkList
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
        $bg_color     = esc_attr($style['headerBackgroundColor'] ?? '#e8f0fe');

        ob_start();
?>
        <!-- Posts List -->
        <div class="flex flex-col sm-series-variant w-full">
            <?php foreach ($posts as $index => $p): ?>
                <?php
                $is_current  = ($p->ID == $current_post_id);
                $part_number = $index + 1;
                $link_url    = get_permalink((int) $p->ID);
                ?>

                <?php if ($is_current): ?>
                    <!-- CURRENT POST -->
                    <a href="<?php echo esc_url($link_url); ?>"
                        style="background-color: <?php echo $bg_color; ?>;
                               border-left: 4px solid <?php echo $button_color; ?>;
                               box-shadow: 0 4px 24px <?php echo SeriesStyleHelper::withOpacity($style['buttonColor'] ?? '#0066d9', 0.12); ?>;"
                        class="flex items-center gap-3 md:gap-6 p-3 md:p-4 transition-all duration-200 rounded-xl">

                        <!-- ICON -->
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl shrink-0 flex items-center justify-center"
                            style="background-color: <?php echo SeriesStyleHelper::withOpacity($style['buttonColor'] ?? '#0066d9', 0.15); ?>">
                            <span class="material-symbols-outlined text-2xl"
                                style="color: <?php echo $button_color; ?>">link</span>
                        </div>

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
                        class="sm-link-list__item group flex items-center gap-3 md:gap-6 p-3 md:p-4 transition-all duration-200 hover:bg-slate-50 rounded-xl">

                        <!-- ICON -->
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl shrink-0 flex items-center justify-center bg-slate-100 transition-colors group-hover:bg-[color-mix(in_srgb,var(--sm-hover-color)_15%,transparent)]">
                            <span class="material-symbols-outlined text-2xl text-slate-400 transition-colors group-hover:text-[var(--sm-hover-color)]">
                                link
                            </span>
                        </div>

                        <!-- CONTENT -->
                        <div class="flex-1 min-w-0">
                            <h4 class="sm-link-list__title font-bold text-base md:text-lg text-slate-900 truncate transition-colors group-hover:text-[var(--sm-hover-color)]">
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
