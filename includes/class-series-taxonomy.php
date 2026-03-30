<?php

use Service\SeriesService;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SM_Series_Taxonomy {

    public static function register() {
        $postTypes = SeriesService::getSupportedPostTypes();
        register_taxonomy(
            'series',
            $postTypes,
            [
                'labels' => [
                    'name'          => 'Series',
                    'singular_name' => 'Series',
                ],
                'public'            => true,
                'hierarchical'      => false,
                'show_in_rest'      => false,
                'show_ui'          => false,
                'show_admin_column' => true,
                'sort'               => true,
                'args'              => [ 'orderby' => 'term_order' ],
                'rewrite'           => [ 'slug' => 'series' ],
                'meta_box_cb'        =>  false ,
            ]
        );
    }
}
