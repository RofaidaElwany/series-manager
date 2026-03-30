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
                'label' => $label,
                'add_new_item' => "Add New $name",
                'public' => true,
                'show_in_rest' => true,
                'supports' => ['title', 'editor'],
                'has_archive' => true,
            ]);
        }
    }
}