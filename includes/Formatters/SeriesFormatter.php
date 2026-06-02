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
     * @param array $termSettings Array of term settings indexed by term ID
     * @return array Formatted terms
     */
    public function formatTerms(array $terms, array $termSettings): array
    {
        return array_map(function ($term) use ($termSettings) {
            return [
                'id' => (int) $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'taxonomy' => $term->taxonomy,
                'count' => (int) $term->post_count,
                'settings' => $termSettings[$term->term_id] ?? [],
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
     * @param array $termSettings Array of term settings indexed by term ID
     * @return array Formatted term
     */
    public function formatTerm(object $term, array $termSettings): array
    {
        return [
            'id' => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
            'taxonomy' => $term->taxonomy,
            'count' => $term->count ?? 0,
            'settings' => $termSettings[$term->term_id] ?? [],
        ];
    }
}
