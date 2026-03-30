<?php

if (!defined('ABSPATH')) {
    exit;
}

class SM_Post_Types
{
    public static function register()
    {
        $custom_post_types = get_option('sm_custom_post_types', []);

        if (empty($custom_post_types)) {
            return;
        }

        foreach ($custom_post_types as $cpt) {

            $name  = $cpt['name'];
            $label = $cpt['label'];

            register_post_type($name, [
                'labels' => [
                    'name' => $label,
                    'singular_name' => $label,
                    'add_new' => 'Add ' . $label,
                    'add_new_item' => 'Add New ' . $label,
                    'edit_item' => 'Edit ' . $label,
                    'new_item' => 'New ' . $label,
                    'view_item' => 'View ' . $label,
                    'all_items' => 'All ' . $label . 's',
                    'menu_name' => $label,
                ],
                'public' => true,
                'show_in_rest' => true,
                'supports' => ['title', 'editor'],
                'has_archive' => true,
            ]);
        }
    }
}