<?php



namespace Layouts;



require_once __DIR__ . '/../Helpers/SeriesStyleHelper.php';



use Components\SeriesHeader;

use Helpers\SeriesStyleHelper;



if (! defined('ABSPATH')) {

    exit;

}



class AccordionLayout

{

    /**

     * Render the accordion layout with optional per-term variant classes.

     *

     * @param array $data

     * @param string|array<int,string> $variant_class String class name or map of term IDs to class names.

     * @return string

     */

    public static function render(array $data, $variant_class, string $align = ''): string

    {

        ob_start();
?>

        <div class="sm-series-accordion space-y-8"<?php echo SeriesStyleHelper::layoutContainerStyle([]); ?>>



            <?php foreach ($data as $item): ?>

                <?php

                $term           = $item['term'];

                $posts          = $item['posts'];

                $current_index  = $item['current_index'];

                $total_posts    = $item['total_posts'];



                $current_post_id = $item['current_post_id'] ?? get_the_ID();

                $style = SeriesStyleHelper::getForTerm((int) $term->term_id);

                ?>

                <details class="sm-series-layout-item group bg-surface-container-lowest rounded-2xl border border-outline-variant/20 overflow-hidden" data-sm-series-term-id="<?php echo esc_attr((string) $term->term_id); ?>"<?php echo SeriesStyleHelper::layoutContainerStyleCustomOnly($style); ?>>

                    <summary class="list-none cursor-pointer">

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

                    </summary>

                    <div class="p-6 pt-2">

                        <?php

                        $item_variant_class = is_array($variant_class)

                            ? ($variant_class[$term->term_id] ?? \Variants\LinkList::class)

                            : $variant_class;

                        echo SeriesStyleHelper::ksesWithStyles(
                            $item_variant_class::render(
                                $posts,
                                $current_post_id,
                                $style,
                                $align,
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


