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
        <!-- Media Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
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
                if ($title_plain !== '') {
                    $initial = function_exists('mb_substr')
                        ? mb_strtoupper(mb_substr($title_plain, 0, 1, 'UTF-8'), 'UTF-8')
                        : strtoupper(substr($title_plain, 0, 1));
                } else {
                    $initial = '?';
                }
                ?>

                <a
                    href="<?php echo esc_url($link_url); ?>"
                    class="group relative flex flex-col h-full overflow-hidden rounded-[28px] border transition-all duration-300 bg-surface-container-lowest
                    <?php echo $is_current
                        ? 'border-primary shadow-lg ring-2 ring-primary/20'
                        : 'border-outline-variant hover:border-primary/30 hover:shadow-md'; ?>">

                    <!-- Current Post Badge -->
                    <?php if ($is_current): ?>
                        <div class="absolute top-4 right-4 z-20">
                            <div class="flex items-center gap-1 bg-primary text-on-primary px-3 py-1 rounded-full shadow-sm">

                                <span class="material-symbols-outlined text-[18px]">
                                    visibility
                                </span>

                                <span class="text-label-md font-medium">
                                    Reading
                                </span>

                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Thumbnail -->
                    <div class="relative h-56 overflow-hidden">


                    <?php if ($avatar_img_url): ?>
                            <img
                                src="<?php echo esc_url($avatar_img_url); ?>"
                                alt="<?php echo esc_attr($p->post_title); ?>"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <?php else: ?>
                            <div class="w-full h-full bg-surface-container flex items-center justify-center relative overflow-hidden">

                            <!-- Decorative Background -->
                            <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-surface-container to-secondary/10"></div>

                            <!-- Big Initial -->
                            <span class="relative text-7xl font-display text-primary/70 tracking-tight select-none">
                                <?php echo esc_html($initial); ?>
                            </span>

                            </div>
                            
                        <?php endif; ?>
                        <!-- Part Number -->
                        <div class="absolute top-4 left-4">

                            <div class="bg-surface-container-lowest/90 backdrop-blur-md text-primary text-label-md font-bold px-3 py-1.5 rounded-xl shadow-sm">

                                <?php echo intval($part_number); ?>

                            </div>

                        </div>

                    </div>

                    <!-- Content -->
                    <div class="flex flex-col flex-1 p-2">

                        <div class="flex-1 space-y-3">
                            <h3 class="
                                text-headline-sm font-display transition-colors
                                <?php echo $is_current
                                    ? 'text-primary'
                                    : 'text-on-surface group-hover:text-primary'; ?>
                            ">

                                <?php echo esc_html($p->post_title); ?>

                            </h3>

                            <p class="text-body-lg text-on-surface-variant leading-relaxed line-clamp-3">

                                <?php
                                echo esc_html(
                                    wp_trim_words(
                                        wp_strip_all_tags($p->post_content),
                                        22
                                    )
                                );
                                ?>
                            </p>
                        </div>
                        <!-- Footer -->
                        <div class="mt-6 pt-2 border-t border-outline-variant flex items-center justify-end">
                            <div class="flex items-center gap-2 text-primary font-semibold group-hover:gap-4 transition-all duration-300">
                                <span class="material-symbols-outlined">
                                    arrow_forward
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>

        </div>

<?php
        return ob_get_clean();
    }
}