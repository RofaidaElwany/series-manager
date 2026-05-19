<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/../core/Service/SeriesDataProvider.php';

require_once __DIR__ . '/Layouts/StandardLayout.php';
require_once __DIR__ . '/Layouts/AccordionLayout.php';

require_once __DIR__ . '/Variants/MediaList.php';
require_once __DIR__ . '/Variants/LinkList.php';
require_once __DIR__ . '/Variants/MediaGrid.php';
require_once __DIR__ . '/Variants/LinkGrid.php';

use Layouts\StandardLayout;
use Layouts\AccordionLayout;


use Variants\MediaList;
use Variants\LinkList;
use Variants\MediaGrid;
use Variants\LinkGrid;

use Service\SeriesDataProvider;

class SM_Series_Renderer
{
    public static function init()
    {
        if (function_exists('register_block_type_from_metadata')) {
            $metadata_path = __DIR__ . '/../../build/blocks/series-list/block.json';

            if (! file_exists($metadata_path)) {
                $metadata_path = __DIR__ . '/../../src/blocks/series-list/block.json';
            }

            register_block_type_from_metadata($metadata_path, [
                'render_callback' => [self::class, 'render_series'],
            ]);
        } else {
            register_block_type('series-manager/series-list', [
                'render_callback' => [self::class, 'render_series'],
            ]);
        }
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
            'link-grid'  => LinkGrid::class,
            'media-grid' => MediaGrid::class,
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

    private static function is_editor_preview_request(): bool
    {
        return defined('REST_REQUEST') && REST_REQUEST;
    }

    private static function get_preview_post_id(): int
    {
        if (! self::is_editor_preview_request()) {
            return 0;
        }

        $post_id = isset($_REQUEST['post_id'])
            ? absint($_REQUEST['post_id'])
            : 0;

        if (! $post_id || ! current_user_can('edit_post', $post_id)) {
            return 0;
        }

        return $post_id;
    }

    private static function get_preview_series_ids(int $post_id): ?array
    {
        if (
            ! $post_id ||
            ! self::is_editor_preview_request() ||
            ! isset($_REQUEST['series_ids'])
        ) {
            return null;
        }

        $raw = sanitize_text_field(wp_unslash($_REQUEST['series_ids']));

        if ($raw === '') {
            return [];
        }

        return array_values(array_filter(array_map('absint', explode(',', $raw))));
    }

    /**
     * Render
     */
    public static function render_series($attributes = []): string
    {
        $preview_post_id = self::get_preview_post_id();
        $post_id = $preview_post_id ?: get_the_ID();

        if (! $post_id) {
            return '';
        }

        $preview_series_ids = self::get_preview_series_ids($preview_post_id);
        $series_data = SeriesDataProvider::getSeriesWithPosts(
            $post_id,
            $preview_series_ids,
            (bool) $preview_post_id
        );

        if (empty($series_data)) {
            return '';
        }

        $layout_class = count($series_data) > 1
            ? AccordionLayout::class
            : StandardLayout::class;

        $variant_class = self::get_variant_class();

        return $layout_class::render(
            $series_data,
            $variant_class
        );
    }
}
