<?php

namespace Variants;

use Helpers\SeriesStyleHelper;

if (! defined('ABSPATH')) {
    exit;
}

class LinkGrid
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
        <section class="sm-series-variant sm-link-grid">
            <div class="sm-link-grid__grid">
                <?php foreach ($posts as $index => $p): ?>
                    <?php
                    $button_color = esc_attr($style['buttonColor'] ?? '#0066d9');
                    $is_current  = ($p->ID == $current_post_id);
                    $link_url    = get_permalink((int) $p->ID);
                    $part_number = $index + 1;
                    ?>
                    <a href="<?php echo esc_url($link_url); ?>"
                        style="--sm-hover-color: <?php echo $button_color; ?>;
                                border: 1px solid <?php echo $button_color; ?>;
                                border-radius: 1rem;
                                box-shadow: 0 4px 24px <?php echo SeriesStyleHelper::withOpacity($style['buttonColor'] ?? '#0066d9', 0.12); ?>, 0 1px 4px <?php echo SeriesStyleHelper::withOpacity($style['buttonColor'] ?? '#0066d9', 0.08); ?>;"
                        class="sm-link-grid__card <?php echo $is_current ? 'sm-link-grid__card--current' : 'sm-link-grid__card--default'; ?> 
                                group relative flex items-start gap-4 p-5 
                                md:gap-6 md:p-8 
                                md:rounded-3xl 
                                transition-all duration-300 overflow-hidden">
                        <?php if ($is_current): ?>
                            <div class=" reading-now ">
                                <span class=" px-5 py-2 rounded-full text-white text-sm font-semibold"
                                    style="background: <?php echo esc_attr($style['buttonColor'] ?? '#0066d9'); ?>">
                                    Reading Now
                                </span>
                            </div>
                        <?php endif; ?>
                        <!-- ICON -->
                        <div class="sm-link-grid__icon w-16 h-16 md:w-24 md:h-24 shrink-0 rounded-2xl md:rounded-3xl bg-slate-50 flex items-center justify-center">
                            <span class="material-symbols-outlined"
                                style="color: <?php echo esc_attr($style['buttonColor'] ?? '#0066d9'); ?>; font-size: 32px;">
                                link
                            </span>
                        </div>

                        <!-- CONTENT -->
                        <div class="flex flex-col pt-1 md:pt-2">
                            <h3 class="sm-link-grid__title text-xl md:text-3xl font-bold text-slate-900 transition-colors">
                                <?php echo esc_html($p->post_title); ?>
                            </h3>
                            <div class="w-16 md:w-20 h-1 rounded-full mt-3 md:mt-4 mb-3 md:mb-5"
                                style="background: <?php echo esc_attr($style['buttonColor'] ?? '#0066d9'); ?>">
                            </div>
                            <span class="text-base font-semibold" ...>
                                Part <?php echo intval($part_number); ?>
                            </span>
                        </div>

                        <!-- BOTTOM ACCENT -->
                        <div class="absolute left-0 bottom-0 w-full h-1"
                            style="background: <?php echo esc_attr($style['buttonColor'] ?? '#0066d9'); ?>">
                        </div>

                    </a>
                <?php endforeach; ?>
            </div>
        </section>
<?php
        return ob_get_clean();
    }
}
