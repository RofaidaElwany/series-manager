<?php

namespace Components;

use Helpers\SeriesStyleHelper;

if (! defined('ABSPATH')) {
    exit;
}

class SeriesHeader
{
    /**
     * @param \WP_Term $term
     * @param int|false $current_index
     * @param int $total_posts
     * @param array<string, string> $style
     * @return string
     */
    public static function render($term, $current_index, $total_posts, array $style = [])
    {
        $button_color = esc_attr($style['buttonColor'] ?? '#0066d9');
        $bg_color = esc_attr($style['headerBackgroundColor'] ?? '#e8f0fe');
        $bg_style = $bg_color ? "background-color: {$bg_color};" : '';

        $title_color = esc_attr($style['titleColor'] ?? '');
        $titleStyle = $title_color ? "color: {$title_color};" : '';

        $badge_color = esc_attr($style['buttonColor'] ?? '#0066d9');
        $badgeStyle = "background-color: {$badge_color};";
        ob_start();
?>
        <!-- Series Header -->
        <!-- Series Header -->
        <div
            style="background-color: <?php echo $bg_color; ?>;
                    border: 1px solid <?php echo $button_color; ?>;
                    border-radius: 1rem;"
            class="flex items-center justify-between p-6 shadow-sm">
            <div>
                <h2
                    class="text-headline-sm font-display text-on-surface"
                    style="<?php echo $titleStyle; ?>">
                    <?php echo esc_html($term->name); ?>
                </h2>
            </div>

            <?php if ($current_index !== false): ?>
                <div
                    class="bg-primary text-on-primary px-4 py-2 rounded-full text-label-md font-medium shadow-sm"
                    style="<?php echo $badgeStyle; ?>">
                    Part <?php echo intval($current_index + 1); ?> of <?php echo intval($total_posts); ?>
                </div>
            <?php endif; ?>
        </div>
<?php
        return ob_get_clean();
    }
}
