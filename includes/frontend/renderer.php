<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/designes/list.php';
require_once __DIR__ . '/designes/accordion.php';
require_once __DIR__ . '/designes/graid.php';

class SM_Series_Renderer
{
    /**
     * Register block
     */
    public static function init()
    {
        register_block_type('series-manager/series-list', [
            'render_callback' => [self::class, 'render_series'],
        ]);
    }

    /**
     * Decide layout
     */
    public static function get_layout_class(): string
    {
        $post_id = get_the_ID();

        // 👇 default
        $layout = 'list';

        if ($post_id) {
            $terms = wp_get_post_terms($post_id, 'series');

            if (!is_wp_error($terms) && count($terms) > 1) {
                $layout = 'accordion';
            }
        }

        $map = [
            'list'      => \frontend\designes\ListLayout::class,
            'accordion' => \frontend\designes\AccordionLayout::class,
            'grid'      => \frontend\designes\GraidLayout::class,
        ];

        return $map[$layout] ?? \frontend\designes\ListLayout::class;
    }

    /**
     * Render HTML (used by block)
     */
    public static function render_series($attributes = []): string
    {
        $class = self::get_layout_class();

        if (!class_exists($class) || !method_exists($class, 'render')) {
            return '';
        }

        return $class::render($attributes);
    }
}