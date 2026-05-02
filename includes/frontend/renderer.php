<?php

use frontend\designes\AccordionLayout;
use frontend\designes\GraidLayout;
use frontend\designes\ListLayout;

if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/designes/list.php';
require_once __DIR__ . '/designes/accordion.php';
require_once __DIR__ . '/designes/graid.php';

class SM_Series_Block_Render
{
    private static $injected_in_block_flow = false;

    public static function init()
    {
        // register_block_type('series-manager/series-list', [
        //     'render_callback' => [self::class, 'render_selected_layout'],
        //     'attributes'      => [
        //         'align' => [
        //             'type' => 'string',
        //         ],
        //     ],
        // ]);

        // Block-theme placement: before title (top) or after content (bottom).
        add_filter('render_block', [self::class, 'inject_around_post_blocks'], 20, 2);
    }


    public static function render_selected_layout($attributes = [])
    {
        $layout = get_option('post_layout', 'list');
        $post_id = get_the_ID();

        // If a post belongs to multiple series terms, force accordion layout.
        if ($post_id) {
            $terms = wp_get_post_terms($post_id, 'series');
            if (! is_wp_error($terms) && count($terms) > 1) {
                $layout = 'accordion';
            }
        }

        $map = [
            'list'      => ListLayout::class,
            'accordion' => AccordionLayout::class,
            'grid'      => GraidLayout::class,
        ];

        $layout_class = $map[$layout] ?? ListLayout::class;

        if (! class_exists($layout_class) || ! method_exists($layout_class, 'render')) {
            return ListLayout::render($attributes);
        }

        return $layout_class::render($attributes);
    }

    // public static function series_navigation_position(string $content)
    // {
    //     if (! is_singular('post')) {
    //         return $content;
    //     }

    //     // If already injected through block rendering, skip fallback injection.
    //     if (self::$injected_in_block_flow) {
    //         return $content;
    //     }

    //     // Avoid duplicate output when the series block is already manually inserted.
    //     if (function_exists('has_block')) {
    //         $post = get_post();
    //         if ($post && has_block('series-manager/series-list', $post->post_content)) {
    //             return $content;
    //         }
    //     }

    //     $series_html = self::render_selected_layout();
    //     if (! $series_html) {
    //         return $content;
    //     }

    //     $position = strtolower(trim((string) get_option('navigation_position', 'bottom')));
    //     if ($position === 'buttom') {
    //         $position = 'bottom';
    //     }
    //     if (! in_array($position, ['top', 'bottom'], true)) {
    //         $position = 'bottom';
    //     }

    //     if ($position === 'top') {
    //         return $series_html . $content;
    //     }

    //     return $content . $series_html;
    // }

    public static function inject_around_post_blocks(string $block_content, array $block)
    {
        if (! is_singular('post') || self::$injected_in_block_flow) {
            return $block_content;
        }

        if (! is_array($block) || empty($block['blockName'])) {
            return $block_content;
        }

        if (function_exists('has_block')) {
            $post = get_post();
            if ($post && has_block('series-manager/series-list', $post->post_content)) {
                return $block_content;
            }
        }

        $position = strtolower(trim((string) get_option('navigation_position', 'bottom')));
        if ($position === 'buttom') {
            $position = 'bottom';
        }
        if (! in_array($position, ['top', 'bottom'], true)) {
            $position = 'bottom';
        }

        $series_html = self::render_selected_layout();
        if (! $series_html) {
            return $block_content;
        }

        // Top means before post title (first visible post element).
        if ($position === 'top' && $block['blockName'] === 'core/post-title') {
            self::$injected_in_block_flow = true;
            return $series_html . $block_content;
        }

        // Bottom means after post content (last post body element).
        if ($position === 'bottom' && $block['blockName'] === 'core/post-content') {
            self::$injected_in_block_flow = true;
            return $block_content . $series_html;
        }

        return $block_content;
    }
}
