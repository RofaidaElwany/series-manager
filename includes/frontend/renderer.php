<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/../core/Service/SeriesDataProvider.php';

require_once __DIR__ . '/Layouts/StandardLayout.php';
require_once __DIR__ . '/Layouts/AccordionLayout.php';

require_once __DIR__ . '/Variants/MediaList.php';
require_once __DIR__ . '/Variants/LinkList.php';

use Layouts\StandardLayout;
use Layouts\AccordionLayout;

use Variants\MediaList;
use Variants\LinkList;

use Service\SeriesDataProvider;

class SM_Series_Renderer
{
    public static function init()
    {
        register_block_type('series-manager/series-list', [
            'render_callback' => [self::class, 'render_series'],
        ]);
    }

    /**
     * Get selected content variant
     */
    public static function get_variant_class(): string
    {
        $variant = get_option('content_variant', 'link-list');

        $map = [
            'media-list' => MediaList::class,
            'link-list'  => LinkList::class,
        ];

        return $map[$variant] ?? LinkList::class;
    }

    /**
     * Determine if post belongs to multiple series
     */
    public static function is_multiple_series(): bool
    {
        $post_id = get_the_ID();

        if (! $post_id) {
            return false;
        }

        $terms = wp_get_post_terms($post_id, 'series');

        return ! is_wp_error($terms) && count($terms) > 1;
    }

    /**
     * Get layout class
     */
    public static function get_layout_class(): string
    {
        return self::is_multiple_series()
            ? AccordionLayout::class
            : StandardLayout::class;
    }

    /**
     * Render
     */
    public static function render_series($attributes = []): string
    {
        $post_id = get_the_ID();

        if (! $post_id) {
            return '';
        }

        $series_data = SeriesDataProvider::getSeriesWithPosts($post_id);

        if (empty($series_data)) {
            return '';
        }

        $layout_class = self::get_layout_class();

        $variant_class = self::get_variant_class();

        return $layout_class::render(
            $series_data,
            $variant_class
        );
    }
}