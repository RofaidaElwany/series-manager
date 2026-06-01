<?php

namespace Service;

if (! defined('ABSPATH')) {
    exit;
}

class SeriesNavigationService
{
    private \SeriesRepository $repository;

    public function __construct(\SeriesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getSeriesWithPosts(int $post_id, ?array $series_ids = null, bool $include_current_post = false): array
    {
        $terms = $this->repository->getSeriesTermsForPost($post_id, $series_ids);
        if (empty($terms)) {
            return [];
        }

        $result = [];

        foreach ($terms as $term) {
            $posts = $this->repository->getOrderedPosts($term->term_taxonomy_id);

            if ($include_current_post && $series_ids !== null) {
                $posts = $this->addCurrentPostForPreview($posts, $post_id);
            }

            if (empty($posts)) {
                continue;
            }

            $post_ids = array_map('intval', wp_list_pluck($posts, 'ID'));
            $current_index = array_search((int) $post_id, $post_ids, true);
            $total_posts = count($posts);

            $result[] = [
                'term' => $term,
                'posts' => $posts,
                'current_index' => $current_index,
                'total_posts' => $total_posts,
                'prev_post' => $this->getPreviousPost($posts, $current_index),
                'next_post' => $this->getNextPost($posts, $current_index),
                'current_post_id' => $post_id,
            ];
        }

        return $result;
    }

    private function addCurrentPostForPreview(array $posts, int $post_id): array
    {
        if (! $post_id) {
            return $posts;
        }

        $post_ids = array_map('intval', wp_list_pluck($posts, 'ID'));
        if (in_array($post_id, $post_ids, true)) {
            return $posts;
        }

        $post = get_post($post_id);
        if (! $post || $post->post_status === 'trash') {
            return $posts;
        }

        $supported_post_types = SeriesService::getSupportedPostTypes();
        if (! in_array($post->post_type, $supported_post_types, true)) {
            return $posts;
        }

        $posts[] = $post;

        return $posts;
    }

    /**
     * Get the previous post given the current index.
     *
     * @param array $posts
     * @param int|false $current_index Index returned by array_search() or false when not found
     * @return object|null
     */
    private function getPreviousPost(array $posts, $current_index)
    {
        if ($current_index === false || $current_index <= 0) {
            return null;
        }

        return $posts[$current_index - 1] ?? null;
    }

    /**
     * Get the next post given the current index.
     *
     * @param array $posts
     * @param int|false $current_index Index returned by array_search() or false when not found
     * @return object|null
     */
    private function getNextPost(array $posts, $current_index)
    {
        if ($current_index === false || $current_index >= count($posts) - 1) {
            return null;
        }

        return $posts[$current_index + 1] ?? null;
    }
}
