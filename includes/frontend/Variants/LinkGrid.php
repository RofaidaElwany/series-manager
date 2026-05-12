<?php

namespace Variants;

if (! defined('ABSPATH')) {
    exit;
}

class LinkGrid
{
    public static function render($posts, $current_post_id)
    {
        ob_start();

        // 🎨 Dynamic Color System
        $color_classes = [
            [
                'bg'        => 'bg-blue-50',
                'badge_bg'  => 'bg-blue-600',
                'text'      => 'text-blue-600',
                'icon_bg'   => 'bg-blue-100',
                'border'    => 'border-blue-500',
                'shadow'    => 'shadow-[0_20px_60px_rgba(59,130,246,0.3)]',
                'glow'      => 'bg-blue-500',

            ],
            [
                'bg'        => 'bg-purple-50',
                'badge_bg'  => 'bg-purple-600',
                'text'      => 'text-purple-600',
                'icon_bg'   => 'bg-purple-100',
                'border'    => 'border-purple-500',
                'shadow'    => 'shadow-[0_20px_60px_rgba(168,85,247,0.3)]',
                'glow'      => 'bg-purple-500',
            ],
            [
                'bg'        => 'bg-green-50',
                'badge_bg'  => 'bg-green-600',
                'text'      => 'text-green-600',
                'icon_bg'   => 'bg-green-100',
                'border'    => 'border-green-500',
                'shadow'    => 'shadow-[0_20px_60px_rgba(34,197,194,0.3)]',
                'glow'      => 'bg-green-500',
            ],
            [
                'bg'        => 'bg-orange-50',
                'badge_bg'  => 'bg-orange-600',
                'text'      => 'text-orange-600',
                'icon_bg'   => 'bg-orange-100',
                'border'    => 'border-orange-500',
                'shadow'    => 'shadow-[0_20px_60px_rgba(251,146,60,0.3)]',
                'glow'      => 'bg-orange-500',
            ],
            [
                'bg'        => 'bg-indigo-50',
                'badge_bg'  => 'bg-indigo-600',
                'text'      => 'text-indigo-600',
                'icon_bg'   => 'bg-indigo-100',
                'border'    => 'border-indigo-500',
                'shadow'    => 'shadow-[0_20px_60px_rgba(99,102,241,0.3)]',
                'glow'      => 'bg-indigo-500',
            ],
        ];
?>

        <!-- SECTION -->
        <section class="max-w-7xl mx-auto px-6 bg-background font-body">

            <!-- GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 mt-10">

                <?php foreach ($posts as $index => $p): ?>

                    <?php
                    $is_current = ($p->ID == $current_post_id);

                    $link_url = get_permalink($p);

                    // 🎨 Rotate Colors
                    $classes = $color_classes[$index % count($color_classes)];

                    $part_number = $index + 1;
                    ?>

                    <!-- CARD -->
                    <a
                        href="<?php echo esc_url($link_url); ?>"
                        class="group relative overflow-hidden rounded-2xl min-h-[280px] flex flex-col p-7 border-2 transition-all duration-500 hover:-translate-y-2

                            <?php echo $is_current
                                ? "{$classes['bg']} {$classes['border']} {$classes['shadow']}"
                                : "bg-white border-gray-200 hover:shadow-xl"; ?>
                        ">

                        <!-- ACTIVE BADGE -->
                        <?php if ($is_current): ?>

                            <div class="absolute top-5 right-5 z-20">

                                <div class="inline-flex items-center px-4 py-2 rounded-full <?php echo $classes['badge_bg']; ?> text-white text-sm font-semibold shadow-md">

                                    Reading Now

                                </div>

                            </div>

                        <?php endif; ?>

                        <!-- CONTENT -->
                        <div class="flex flex-col h-full">

                            <!-- ICON -->
                            <div class="mb-10">

                                <div
                                    class="
                                        w-20
                                        h-20
                                        rounded-2xl
                                        flex
                                        items-center
                                        justify-center
                                        <?php echo $classes['icon_bg']; ?>
                                    ">

                                    <svg
                                        class="w-10 h-10 <?php echo $classes['text']; ?>"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5m5.656-5.656a4 4 0 015.656 5.656l-1.5 1.5m-4.242-4.242l-4.242 4.242" />

                                    </svg>

                                </div>

                            </div>

                            <!-- TEXT -->
                            <div class="mt-auto">

                                <!-- PART -->
                                <div
                                    class="
                                        text-sm
                                        font-semibold
                                        tracking-[-0.01em]
                                        mb-4
                                        <?php echo $is_current
                                            ? $classes['text']
                                            : 'text-slate-500'; ?>
                                    ">

                                    Part <?php echo intval($part_number); ?>

                                </div>

                                <!-- TITLE -->
                                <h3
                                    class="
                                        max-w-[13ch]
                                        text-[1.9rem]
                                        leading-[1.05]
                                        tracking-[-0.04em]
                                        font-display
                                        font-semibold
                                        text-slate-900
                                    ">

                                    <?php echo esc_html($p->post_title); ?>

                                </h3>

                            </div>

                        </div>

                        <!-- BOTTOM GLOW -->
                        <div
                            class="
                                absolute
                                bottom-0
                                left-0
                                right-0
                                h-[5px]
                                opacity-80
                                <?php echo $classes['glow']; ?>
                            ">
                        </div>

                    </a>

                <?php endforeach; ?>

            </div>

        </section>

<?php

        return ob_get_clean();
    }
}
