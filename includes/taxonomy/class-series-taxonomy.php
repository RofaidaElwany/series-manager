<?php

use Service\SeriesService;

if (! defined('ABSPATH')) {
    exit;
}

class SM_Series_Taxonomy
{

    public static function register()
    {
        $postTypes = SeriesService::getSupportedPostTypes();
        register_taxonomy(
            'series',
            $postTypes,
            [
                'labels' => [
                    'name'          => 'Series',
                    'singular_name' => 'Series',
                    'add_new_item' => 'Add New Series',
                    'edit_item' => 'Edit Series',
                    'new_item' => 'New Series',
                    'view_item' => 'View Series',
                    'search_items' => 'Search Series',
                    'not_found' => 'No Series found',
                ],
                'public'            => true,
                'hierarchical'      => false,
                'show_in_rest'      => false,
                'show_ui'          => true,
                'show_admin_column' => true,
                'rewrite'           => ['slug' => 'series'],
                'meta_box_cb'        => false,
            ]
        );
    }
}
