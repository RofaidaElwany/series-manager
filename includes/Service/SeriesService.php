<?php

namespace Service;
class SeriesService
{
    public function parsePostIds(string $postIdsString): array
    {
        return array_map('intval', array_filter(explode(',', $postIdsString)));
    }

    public function findPostIndex(array $postIds, int $postId)
    {
        return array_search($postId, $postIds, true);
    }

    public function sortPostsBySeriesOrder(array $posts, array $postIds): array
    {
        usort($posts, function ($a, $b) use ($postIds) {
            return array_search($a->ID, $postIds) - array_search($b->ID, $postIds);
        });

        return $posts;
    }
    public static function getSupportedPostTypes()
    {
        $saved = get_option('sm_supported_post_types', []);
        if (!empty($saved)) {
            return $saved;
        }
        // Fallback to all public post types if not set
        $postTypes = get_post_types(['public' => true], 'names');
        return apply_filters('sm_series_supported_post_types', $postTypes);
    }

    public static function addCustomPostType($name, $label)
    {
        if (!$name || !$label) {
                return;
            }
                $name = sanitize_key($name);
        $label = sanitize_text_field($label);

        $custom_post_types = get_option('sm_custom_post_types', []);

        foreach ($custom_post_types as $cpt) {
            if ($cpt['name'] === $name) {
                return; // already exists
            }
        }

        $custom_post_types = get_option('sm_custom_post_types', []);

        $custom_post_types[] = [
            'name'  => sanitize_key($name),
            'label' => sanitize_text_field($label),
        ];

        update_option('sm_custom_post_types', $custom_post_types);
    }
}
