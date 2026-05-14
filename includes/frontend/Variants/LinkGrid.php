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
        <section class="max-w-7xl mx-auto font-body">

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
                        class="group relative overflow-hidden rounded-2xl flex flex-col p-7 border-2 transition-all duration-500 hover:-translate-y-2

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
                        <div class="flex flex-col">

                            <!-- ICON -->
                            <div class="mb-5 flex  items-start justify-between gap-4">
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
                                    <span class="material-symbols-outlined  text-[32px] p-1 w-10 h-10 <?php echo $classes['text']; ?>">link</span>
                                </div>
                                <!-- PART -->
                                <div
                                    class="
                                        flex
                                        text-sm
                                        font-semibold
                                        tracking-[-0.01em]
                                        mt-8
                                        <?php echo $is_current
                                            ? $classes['text']
                                            : 'text-slate-500'; ?>
                                    ">
                                    Part <?php echo intval($part_number); ?>
                                </div>
                            </div>
                            <!-- TEXT -->
                            <div class="mt-1 flex-2">
                                <!-- TITLE -->
                                <h2
                                    class="
                                        font-semibold
                                    ">
                                    <?php echo esc_html($p->post_title); ?>
                                </h2>
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
