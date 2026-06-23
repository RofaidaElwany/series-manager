<?php

namespace Layouts;

require_once __DIR__ . '/../Components/SeriesHeader.php';
require_once __DIR__ . '/../Components/SeriesNavigation.php';
require_once __DIR__ . '/../Helpers/SeriesStyleHelper.php';

use Components\SeriesHeader;
use Components\SeriesNavigation;
use Helpers\SeriesStyleHelper;


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
    public static function render(array $data, $variant_class, string $align = ''): string
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
            $style = SeriesStyleHelper::getForTerm((int) $term->term_id);

?>

            <div class="sm-series-layout space-y-8" data-sm-series-term-id="<?php echo esc_attr((string) $term->term_id); ?>"<?php echo SeriesStyleHelper::layoutContainerStyle($style); ?>>

                <?php
                echo SeriesStyleHelper::ksesWithStyles(
                    SeriesHeader::render(
                        $term,
                        $current_index,
                        $total_posts,
                        $style
                    )
                );
                ?>

                <?php
                echo SeriesStyleHelper::ksesWithStyles(
                    $item_variant_class::render(
                        $posts,
                        $current_post_id,
                        $style, 
                        $align,
                    )
                );
                ?>

                <?php
                echo SeriesStyleHelper::ksesWithStyles(
                    SeriesNavigation::render(
                        $prev_post,
                        $next_post,
                        $style
                    )
                );
                ?>

            </div>
<?php
        }

        return ob_get_clean();
    }
}
