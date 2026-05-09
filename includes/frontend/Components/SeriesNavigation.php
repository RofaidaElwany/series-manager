<?php

namespace Components;

if (! defined('ABSPATH')) {
    exit;
}

class SeriesNavigation
{
    public static function render($prev_post, $next_post)
    {
        ob_start();
?>
        <!-- Navigation Footer -->
        <div class="flex justify-between items-center pt-8 border-t border-outline-variant/20">

            <!-- Previous -->
            <?php if ($prev_post): ?>
                <a href="<?php echo esc_url(get_permalink($prev_post)); ?>"
                    class="flex items-center gap-2 px-6 py-3 rounded-full bg-primary text-on-primary hover:bg-primary-dim transition-all active:scale-95 shadow-sm">

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
<?php
        return ob_get_clean();
    }
}
