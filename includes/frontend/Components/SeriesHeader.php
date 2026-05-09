<?php

namespace Components;

if (! defined('ABSPATH')) {
    exit;
}

class SeriesHeader
{
    public static function render($term, $current_index, $total_posts)
    {
        ob_start();
?>
        <!-- Series Header -->
        <div class="flex items-center justify-between p-6 bg-primary/10 border border-outline-variant/30 rounded-xl shadow-sm">

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
<?php
        return ob_get_clean();
    }
}
