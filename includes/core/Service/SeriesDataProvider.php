<?php

namespace Service;

if (! defined('ABSPATH')) {
    exit;
}

class SeriesDataProvider
{
    private static function ensureTermOrderColumn(): bool
    {
        global $wpdb;

        $table = $wpdb->term_relationships;

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is provided by wpdb.
        $exists = $wpdb->get_results(
            $wpdb->prepare("SHOW COLUMNS FROM {$table} LIKE %s", 'term_order')
        );
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

        if (! empty($exists)) {
            return true;
        }

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared -- Schema query uses a wpdb table name and no user input.
        $created = $wpdb->query(
            "ALTER TABLE {$table}
             ADD COLUMN term_order INT(11) NOT NULL DEFAULT 0
             AFTER term_taxonomy_id"
        );
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared

        return $created !== false;
    }

    private static function getOrderedPostsForTerm(\WP_Term $term, int $current_post_id = 0, bool $include_current_post = false): array
    {
        global $wpdb;

        $posts_table = $wpdb->posts;
        $tr_table    = $wpdb->term_relationships;

        $post_types = array_values(array_filter(array_map('sanitize_key', (array) SeriesService::getSupportedPostTypes())));
        if (empty($post_types)) {
            $post_types = ['post'];
        }

        $placeholders = implode(',', array_fill(0, count($post_types), '%s'));

        $status_sql = "p.post_status = 'publish'";
        $params = array_merge([(int) $term->term_taxonomy_id], $post_types);

        if ($include_current_post && $current_post_id) {
            $status_sql = "(p.post_status = 'publish' OR p.ID = %d)";
            $params[] = (int) $current_post_id;
        }

        $orderby = self::ensureTermOrderColumn()
            ? "tr.term_order ASC, p.ID ASC"
            : "p.ID ASC";

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table names and SQL fragments are built from sanitized internal values.
        $posts = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT p.*
                FROM {$posts_table} p
                INNER JOIN {$tr_table} tr
                    ON p.ID = tr.object_id
                WHERE tr.term_taxonomy_id = %d
                AND p.post_type IN ($placeholders)
                AND {$status_sql}
                ORDER BY {$orderby}
                ",
                $params
            )
        );
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

        return $posts;
    }

    private static function getTerms(?array $series_ids, int $post_id): array
    {
        if ($series_ids === null) {
            $terms = wp_get_post_terms($post_id, 'series');

            return is_wp_error($terms) ? [] : $terms;
        }

        $series_ids = array_values(array_filter(array_map('absint', $series_ids)));
        if (empty($series_ids)) {
            return [];
        }

        $terms = get_terms([
            'taxonomy'   => 'series',
            'include'    => $series_ids,
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms) || empty($terms)) {
            return [];
        }

        usort($terms, function ($a, $b) use ($series_ids) {
            return array_search((int) $a->term_id, $series_ids, true)
                <=> array_search((int) $b->term_id, $series_ids, true);
        });

        return $terms;
    }

    private static function addCurrentPostForPreview(array $posts, int $post_id): array
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

        $post_types = (array) SeriesService::getSupportedPostTypes();
        if (! in_array($post->post_type, $post_types, true)) {
            return $posts;
        }

        $posts[] = $post;

        return $posts;
    }

    public static function getSeriesWithPosts(int $post_id, ?array $series_ids = null, bool $include_current_post = false): array
    {
        $terms = self::getTerms($series_ids, $post_id);

        if (empty($terms)) {
            return [];
        }

        $result = [];

        foreach ($terms as $term) {
            $posts = self::getOrderedPostsForTerm($term, $post_id, $include_current_post);

            if ($include_current_post && $series_ids !== null) {
                $posts = self::addCurrentPostForPreview($posts, $post_id);
            }

            if (empty($posts)) {
                continue;
            }

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
                'current_post_id' => $post_id,
            ];
        }

        return $result;
    }
}
