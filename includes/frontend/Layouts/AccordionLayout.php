<?php

namespace Layouts;

use Components\SeriesHeader;

if (! defined('ABSPATH')) {
    exit;
}

class AccordionLayout
{
    public static function render(array $data, string $variant_class): string
    {
        ob_start();

?>
        <div class="max-w-5xl mx-auto px-6 mt-20 space-y-8">

            <?php foreach ($data as $item): ?>
                <?php
                $term           = $item['term'];
                $posts          = $item['posts'];
                $current_index  = $item['current_index'];
                $total_posts    = $item['total_posts'];

                $current_post_id = $item['current_post_id'] ?? get_the_ID();
                ?>
                <details class="group bg-surface-container-lowest rounded-2xl border border-outline-variant/20 overflow-hidden">
                    <summary class="list-none cursor-pointer p-2">
                        <?php
                        echo wp_kses_post(
                            SeriesHeader::render(
                            $term,
                            $current_index,
                            $total_posts
                        )
                        );
                        ?>
                    </summary>
                    <div class="p-6 pt-2">
                        <?php
                        echo wp_kses_post(
                            $variant_class::render(
                                $posts,
                                $current_post_id
                            )
                        );
                        ?>
                    </div>
                </details>
            <?php endforeach; ?>
                
        </div>

    <?php

        return ob_get_clean();
    }
}
