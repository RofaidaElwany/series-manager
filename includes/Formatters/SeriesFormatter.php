<?php

if (! defined('ABSPATH')) {
    exit;
}

class SeriesFormatter
{
    /**
     * Format series terms for API response.
     *
     * @param array $terms Array of WP_Term objects
     * @param callable $layoutResolver Callback to resolve layout position: fn($term_id) => string
     * @return array Formatted terms
     */
    public function formatTerms(array $terms, callable $layoutResolver): array
    {
        return array_map(function ($term) use ($layoutResolver) {
            return [
                'id' => (int) $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'taxonomy' => $term->taxonomy,
                'count' => (int) $term->post_count,
                'layoutPosition' => $layoutResolver((int) $term->term_id),
            ];
        }, $terms);
    }

    /**
     * Format posts for API response.
     *
     * @param array $posts Array of WP_Post objects or stdClass
     * @return array Formatted posts
     */
    public function formatPosts(array $posts): array
    {
        return array_map(function ($post) {
            return [
                'id' => $post->ID,
                'title' => [
                    'rendered' => $post->post_title ?: 'Untitled'
                ]
            ];
        }, $posts);
    }

    /**
     * Format a single series term for API response.
     *
     * @param object $term WP_Term object
     * @param callable $layoutResolver Callback to resolve layout position
     * @return array Formatted term
     */
    public function formatTerm(object $term, callable $layoutResolver): array
    {
        return [
            'id' => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
            'taxonomy' => $term->taxonomy,
            'count' => $term->count ?? 0,
            'layoutPosition' => $layoutResolver((int) $term->term_id),
        ];
    }
}
