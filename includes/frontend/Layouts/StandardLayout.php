<?php

namespace Layouts;

require_once __DIR__ . '/../Components/SeriesHeader.php';
require_once __DIR__ . '/../Components/SeriesNavigation.php';

use Components\SeriesHeader;
use Components\SeriesNavigation;


if (! defined('ABSPATH')) {
    exit;
}

class StandardLayout
{
    /**
     * Render the standard layout with optional per-term variant classes.
     *
     * @param array $data
     * @param string|array<int,string> $variant_class String class name or map of term IDs to class names.
     * @return string
     */
    public static function render(array $data, $variant_class): string
    {
        ob_start();

        foreach ($data as $item) {

            $term           = $item['term'];
            $posts          = $item['posts'];
            $current_index  = $item['current_index'];
            $total_posts    = $item['total_posts'];
            $prev_post      = $item['prev_post'];
            $next_post      = $item['next_post'];

            $current_post_id = $item['current_post_id'] ?? get_the_ID();
            $item_variant_class = is_array($variant_class)
                ? ($variant_class[$term->term_id] ?? \Variants\LinkList::class)
                : $variant_class;

?>

            <div class="max-w-6xl mx-auto px-6 mt-20 space-y-8">

                <?php
                echo wp_kses_post(
                    SeriesHeader::render(
                        $term,
                        $current_index,
                        $total_posts
                    )
                );
                ?>

                <?php
                echo wp_kses_post(
                    $item_variant_class::render(
                        $posts,
                        $current_post_id
                    )
                );
                ?>

                <?php
                echo wp_kses_post(
                    SeriesNavigation::render(
                        $prev_post,
                        $next_post
                    )
                );
                ?>

            </div>
<?php
        }

        return ob_get_clean();
    }
}
