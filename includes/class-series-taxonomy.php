<?php

use Service\SeriesService;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SM_Series_Taxonomy {

    public static function register() {
        $customPostType = new SeriesService();
        register_taxonomy(
            'series',
            $customPostType->getSupportedPostTypes(),
            
            [
                'labels' => [
                    'name'          => 'Series',
                    'singular_name' => 'Series',
                ],
                'public'            => true,
                'hierarchical'      => false,
                'show_in_rest'      => true,
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
