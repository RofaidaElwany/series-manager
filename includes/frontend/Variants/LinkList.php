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
        $selectedRowStyle = SeriesStyleHelper::inlineStyle([
            'background-color' => $style['headerBackgroundColor'] ?? null,
            'border-left-color' => $style['buttonColor'] ?? null,
        ]);

        $accentStyle = SeriesStyleHelper::inlineStyle([
            'color' => $style['buttonColor'] ?? null,
        ]);

        $accentBgStyle = SeriesStyleHelper::inlineStyle([
            'background-color' => SeriesStyleHelper::withOpacity($style['buttonColor'] ?? null, 0.2),
            'color' => $style['buttonColor'] ?? null,
        ]);

        ob_start();
?>
        <!-- Posts List -->
        <div class="flex flex-col py-2">
            <?php foreach ($posts as $p): ?>
                <?php
                $is_current = ($p->ID == $current_post_id);
                $link_url = get_permalink((int) $p->ID);
                $title_for_avatar = trim((string) $p->post_title);
                $initial = strtoupper(substr($title_for_avatar, 0, 1));
                $avatar_img_url = '';
                if (function_exists('mb_substr') && $title_for_avatar !== '') {
                    $initial = strtoupper(mb_substr($title_for_avatar, 0, 1));
                }
                if ($initial === '') {
                    $initial = '?';
                }
                if (has_post_thumbnail($p->ID)) {
                    $avatar_img_url = get_the_post_thumbnail_url($p->ID, 'thumbnail');
                }
                if (! $avatar_img_url && ! empty($p->post_content)) {
                    if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $p->post_content, $matches)) {
                        $avatar_img_url = $matches[1];
                    }
                }
                ?>

                <?php if ($is_current): ?>
                    <a href="<?php echo esc_url($link_url); ?>"
                        class="relative flex items-center gap-4 bg-primary/10 px-6 py-4 border-l-4 border-primary"<?php echo $selectedRowStyle; ?>>

                        <div class="flex items-center justify-center rounded-lg text-on-surface w-10 h-10 overflow-hidden">
                            <div class="flex items-center justify-center rounded-lg text-primary w-10 h-10"<?php echo $accentStyle; ?>>
                                <span class="material-symbols-outlined text-2xl">link</span>
                            </div>
                        </div>
                        <p class="text-on-surface font-medium flex-1 truncate">
                            <?php echo esc_html($p->post_title); ?>
                        </p>

                        <div class="flex items-center justify-center rounded-lg bg-primary/20 text-primary w-10 h-10"<?php echo $accentBgStyle; ?>>
                            <span class="material-symbols-outlined text-2xl">check_circle</span>
                        </div>
                    </a>
                <?php else: ?>
                    <!-- OTHER POSTS -->
                    <a href="<?php echo esc_url($link_url); ?>"
                        class="group flex items-center gap-4 px-6 py-4 hover:bg-surface-container-low transition">

                        <div class="flex items-center justify-center rounded-lg text-on-surface w-10 h-10 overflow-hidden">
                            <div class="flex items-center justify-center rounded-lg text-gray-300 group-hover:text-primary w-16 ">
                                <span class="material-symbols-outlined text-2xl">link</span>
                            </div>
                        </div>
                        <p class="text-on-surface font-medium flex-1 truncate group-hover:text-primary">
                            <?php echo esc_html($p->post_title); ?>
                        </p>

                        <div class="text-gray-300 group-hover:text-primary">
                            <span class="material-symbols-outlined text-2xl">chevron_right</span>
                        </div>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
<?php
        return ob_get_clean();
    }
}
