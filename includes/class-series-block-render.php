<?php

use Service\SeriesService;

if (! defined('ABSPATH')) {
    exit;
}

class SM_Series_Block_Render
{
    public static function init()
    {
        register_block_type('series-manager/series-list', [
            'api_version' => 3,
            'render_callback' => [self::class, 'render'],
            'attributes'      => [
                'mode' => [
                    'type' => 'string',
                    'default' => 'all',
                ],
                'limit' => [
                    'type' => 'number',
                    'default' => 5,
                ],
                'userId' => [
                    'type' => 'string',
                    'default' => '',
                ],
                'align' => [
                    'type' => 'string',
                ],
            ],
        ]);
    }

    public static function render($attributes)
    {
        $mode = $attributes['mode'] ?? 'all';
        $limit = $attributes['limit'] ?? 5;
        $userId = $attributes['userId'] ?? '';

        global $wpdb;
        $repository = new SeriesRepository($wpdb);

        $series = [];

        switch ($mode) {
            case 'all':
                $series = $repository->getAllSeries();
                break;
            case 'top':
                $series = $repository->getTopSeries($limit);
                break;
            case 'user':
                if ($userId) {
                    $series = $repository->getSeriesByUser((int) $userId);
                }
                break;
            case 'topics':
                if ($userId) {
                    $series = $repository->getTopicsByUser((int) $userId);
                }
                break;
        }

        if (empty($series)) {
            return '<div class="series-list-block-preview"><p>' . __('No series found.', 'series-manager') . '</p></div>';
        }

        ob_start();
?>
        <div class="series-list-block">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($series as $term): ?>
                    <div class="series-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h3 class="text-lg font-semibold mb-2">
                            <a href="<?php echo esc_url(get_term_link($term)); ?>" class="text-blue-600 hover:text-blue-800">
                                <?php echo esc_html($term->name); ?>
                            </a>
                        </h3>
                        <?php if ($term->description): ?>
                            <p class="text-gray-600 text-sm mb-2"><?php echo esc_html($term->description); ?></p>
                        <?php endif; ?>
                        <p class="text-xs text-gray-500">
                            <?php printf(_n('%d post', '%d posts', $term->count, 'series-manager'), $term->count); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
<?php
        return ob_get_clean();
    }
}
