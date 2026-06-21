<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/../Repositories/SeriesRepository.php';
require_once __DIR__ . '/../Services/SeriesNavigationService.php';
require_once __DIR__ . '/../Services/SeriesSettingsService.php';

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
     * Get selected content variant for a term.
     */
    public static function get_variant_class_for_term(object $term): string
    {
        $settingsService = new \Service\SeriesSettingsService();
        $settings = $settingsService->getSettings($term->term_id);
        $variant = $settings['layout'] ?? get_option('content_variant', 'link-list');

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

        // phpcs:disable WordPress.Security.NonceVerification.Recommended -- Editor preview request is gated by has_valid_preview_nonce().
        $post_id = isset($_REQUEST['post_id'])
            ? absint(wp_unslash($_REQUEST['post_id']))
            : 0;
        // phpcs:enable WordPress.Security.NonceVerification.Recommended

        if (! $post_id || ! current_user_can('edit_post', $post_id)) {
            return 0;
        }

        return $post_id;
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

        global $wpdb;

        $repository = new \SeriesRepository($wpdb);
        $navigation_service = new \Service\SeriesNavigationService($repository);
        $series_data = $navigation_service->getSeriesWithPosts(
            $post_id
        );

        // Editor preview: show placeholder instead of empty string
        if (empty($series_data)) {
            if (self::is_editor_preview_request()) {
                return '<div class="sm-series-block sm-series-empty-preview" style="padding:1em;border:1px dashed #ccc;color:#888;">'
                    . __('Series block: save the post to see your series here.', 'series-manager')
                    . '</div>';
            }
            return '';
        }

        if (empty($series_data)) {
            return '';
        }

        $layout_class = count($series_data) > 1
            ? AccordionLayout::class
            : StandardLayout::class;

        $variant_classes = [];
        foreach ($series_data as $item) {
            $variant_classes[$item['term']->term_id] = self::get_variant_class_for_term($item['term']);
        }
        $align = $attributes['align'] ?? '';

        $align_class = $align ? 'align' . $align : '';

        $class = 'sm-series-block' . ($align_class ? ' ' . $align_class : '');

        $wrapper_attributes = get_block_wrapper_attributes([
            'class' => $class,
        ]);

        return sprintf(
            '<div %s>%s</div>',
            $wrapper_attributes,
            $layout_class::render($series_data, $variant_classes)

        );
    }
}
