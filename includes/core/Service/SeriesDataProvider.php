<?php

namespace Service;

if (! defined('ABSPATH')) {
    exit;
}

class SeriesDataProvider
{
    public static function getSeriesWithPosts(int $post_id): array
    {
        $terms = wp_get_post_terms($post_id, 'series');

        if (empty($terms) || is_wp_error($terms)) {
            return [];
        }

        $result = [];

        foreach ($terms as $term) {
            $posts = get_posts([
                'post_type'   => SeriesService::getSupportedPostTypes(),
                'numberposts' => -1,
                'tax_query'   => [
                    [
                        'taxonomy' => 'series',
                        'terms'    => $term->term_id,
                    ],
                ],
                'orderby' => 'date',
                'order'   => 'ASC',
            ]);

            if (empty($posts)) {
                continue;
            }

            $current_index = array_search(
                $post_id,
                array_map('intval', wp_list_pluck($posts, 'ID')),
                true
            );

            $post_ids = wp_list_pluck($posts, 'ID');

            $current_index = array_search(
                (int) $post_id,
                array_map('intval', $post_ids),
                true
            );
            $total_posts = count($posts);

            $prev_post = null;
            $next_post = null;
            if ($current_index !== false) {
                $prev_post = $current_index > 0 ? $posts[$current_index - 1] : null;
                $next_post = $current_index < $total_posts - 1 ? $posts[$current_index + 1] : null;
            }

            $result[] = [
                'term'          => $term,
                'posts'         => $posts,
                'current_index' => $current_index,
                'total_posts'   => $total_posts,
                'prev_post'     => $prev_post,
                'next_post'     => $next_post,
            ];
        }

        return $result;
    }
}
