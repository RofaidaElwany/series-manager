<?php

namespace Variants;

if (! defined('ABSPATH')) {
    exit;
}

class MediaGrid
{
    public static function render($posts, $current_post_id)
    {
        ob_start();
?>
        <!-- SECTION -->
        <section class="max-w-7xl mx-auto px-6 bg-background font-body">

            <!-- GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 mt-10">

                <?php foreach ($posts as $index => $p): ?>
                    <?php
                    $is_current = ($p->ID == $current_post_id);

                    $link_url = get_permalink($p);

                    $avatar_img_url = '';

                    if (has_post_thumbnail($p->ID)) {
                        $avatar_img_url = get_the_post_thumbnail_url($p->ID, 'large');
                    }

                    if (! $avatar_img_url && ! empty($p->post_content)) {
                        if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $p->post_content, $matches)) {
                            $avatar_img_url = $matches[1];
                        }
                    }

                    $part_number = $index + 1;

                    $title_plain = trim(wp_strip_all_tags((string) $p->post_title));
                    $initial = $title_plain !== ''
                        ? (function_exists('mb_substr')
                            ? mb_strtoupper(mb_substr($title_plain, 0, 1, 'UTF-8'), 'UTF-8')
                            : strtoupper(substr($title_plain, 0, 1)))
                        : '?';
                    ?>

                    <!-- CARD -->
                    <a
                        href="<?php echo esc_url($link_url); ?>"
                        class="group relative overflow-hidden p-5 rounded-2xl border-2 
                        <?php echo $is_current
                            ? 'bg-[#f3f7ff]  border-primary shadow-[0_10px_50px_rgba(0,98,162,0.12)] hover:shadow-[0_20px_70px_rgba(0,98,162,0.18)]'
                            : 'bg-white border-gray-200 hover:shadow-[0_20px_60px_rgba(15,23,42,0.10)]'; ?>">

                        <!-- BADGE (ACTIVE ONLY) -->
                        <?php if ($is_current): ?>
                            <div class="absolute top-5 right-5 z-20">
                                <div class="px-4 py-2 rounded-full bg-primary text-white text-sm font-semibold shadow-md">
                                    Reading Now
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- IMAGE -->
                        <div class="aspect-[4/3] overflow-hidden rounded-2xl
                            <?php echo $is_current ? 'bg-[#e9f1ff]' : 'bg-[#f4ede8]'; ?>">
                            <?php if ($avatar_img_url): ?>
                                <img
                                    src="<?php echo esc_url($avatar_img_url); ?>"
                                    alt="<?php echo esc_attr($p->post_title); ?>"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <?php else: ?>
                                <div
                                    class="w-full h-64 bg-primary/10 rounded-3xl flex items-center justify-center border border-primary/20">
                                    <svg
                                        class="w-3 h-36 text-primary"
                                        fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path
                                            fill-rule="evenodd"
                                            d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm11 2a1 1 0 110 2 1 1 0 010-2zM4 15l4-4 3 3 4-5 3 6H4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- CONTENT -->
                        <div class="p-7">
                            <div class="text-primary text-[15px] font-semibold mb-3">
                                Part <?php echo intval($part_number); ?>
                            </div>
                            <h3 class="text-[2rem] leading-tight font-display font-semibold text-on-surface tracking-[-0.03em]">
                                <?php echo esc_html($p->post_title); ?>
                            </h3>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
<?php
        return ob_get_clean();
    }
}
