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
    public function getSupportedPostTypes()
    {   
        $postTypes = get_post_types(['public' => true], 'names');   
        return apply_filters('sm_series_supported_post_types', $postTypes);
    }

}
