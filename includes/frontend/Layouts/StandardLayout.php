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
    public static function render(array $data, string $variant_class): string
    {
        ob_start();

        foreach ($data as $item) {

            $term           = $item['term'];
            $posts          = $item['posts'];
            $current_index  = $item['current_index'];
            $total_posts    = $item['total_posts'];
            $prev_post      = $item['prev_post'];
            $next_post      = $item['next_post'];

            $current_post_id = get_the_ID();

?>

            <div class="max-w-7xl mx-auto px-6 mt-20 space-y-8">

                <?php
                echo SeriesHeader::render(
                    $term,
                    $current_index,
                    $total_posts
                );
                ?>

                <?php
                echo $variant_class::render(
                    $posts,
                    $current_post_id
                );
                ?>

                <?php
                echo SeriesNavigation::render(
                    $prev_post,
                    $next_post
                );
                ?>

            </div>
<?php
        }

        return ob_get_clean();
    }
}