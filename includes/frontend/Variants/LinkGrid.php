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

        <!-- SECTION -->
        <section class="sm-series-variant w-full font-body">
            <!-- GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2  gap-8 mt-10">
                <?php foreach ($posts as $index => $p): ?>
                    <?php
                    $is_current = ($p->ID == $current_post_id);
                    $link_url = get_permalink((int) $p->ID);
                    $currentAccentStyle = $is_current
                        ? SeriesStyleHelper::inlineStyle([
                            'color' => $style['buttonColor'] ?? null,
                        ])
                        : '';
                    $part_number = $index + 1;
                    ?>
                    <!-- CARD -->
                    <a
                        href="<?php echo esc_url($link_url); ?>"
                        class="
        relative
        group
        flex
        items-start
        gap-6
        p-8
        rounded-3xl
        border
        transition-all
        duration-300
        overflow-hidden
        min-h-[220px]
        <?php echo $is_current
                        ? 'bg-[#f5f8ff] border-[#dbe7ff] shadow-lg'
                        : 'bg-white border-slate-200 hover:shadow-lg'; ?>
    ">
                        <?php if ($is_current): ?>
                            <div class="absolute top-6 right-6">
                                <span
                                    class="px-5 py-2 rounded-full text-white text-sm font-semibold"
                                    style="background: <?php echo esc_attr($style['buttonColor'] ?? '#0066d9'); ?>">
                                    Reading Now
                                </span>
                            </div>
                        <?php endif; ?>

                        <!-- ICON -->
                        <div
                            class="
            w-24
            h-24
            shrink-0
            rounded-3xl
            bg-slate-50
            flex
            items-center
            justify-center
        ">
                            <span
                                class="material-symbols-outlined text-48px"
                                style="color: <?php echo esc_attr($style['buttonColor'] ?? '#0066d9'); ?>">
                                link
                            </span>
                        </div>

                        <!-- CONTENT -->
                        <div class="flex flex-col pt-2">

                            <!-- TITLE -->
                            <h3
                                class="
                text-3xl
                font-bold
                text-slate-900
                group-hover:text-primary
                transition-colors
            ">
                                <?php echo esc_html($p->post_title); ?>
                            </h3>
                            <!-- SMALL ACCENT LINE -->
                            <div
                                class="w-20 h-1 rounded-full mt-4 mb-5"
                                style="background: <?php echo esc_attr($style['buttonColor'] ?? '#0066d9'); ?>"></div>
                            <!-- PART NUMBER -->
                            <span
                                class="text-xl font-medium"
                                style="<?php echo $is_current ? 'color:' . esc_attr($style['buttonColor'] ?? '#0066d9') . ';' : ''; ?>"> Part <?php echo intval($part_number); ?>
                            </span>
                        </div>
                        <!-- BOTTOM ACCENT -->
                        <div
                            class="absolute left-0 bottom-0 w-full h-1"
                            style="background: <?php echo esc_attr($style['buttonColor'] ?? '#0066d9'); ?>"></div>

                    </a>
                <?php endforeach; ?>
            </div>
        </section>
<?php
        return ob_get_clean();
    }
}
