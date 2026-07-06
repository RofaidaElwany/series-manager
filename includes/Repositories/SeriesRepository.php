<?php

if (! defined('ABSPATH')) {
    exit;
}

class SeriesRepository
{
    /**
     * @var \wpdb
     */
    private  $wpdb;

    /**
     * @param \wpdb $wpdb
     */
    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /* =========================
     * Ensure DB Column
    ========================= */
    public function ensureTermOrderColumn(): void
    {
        $table = $this->wpdb->term_relationships;

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is provided by wpdb.
        $exists = $this->wpdb->get_results(
            $this->wpdb->prepare("SHOW COLUMNS FROM {$table} LIKE %s", 'term_order')
        );
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

        if (empty($exists)) {
            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared -- Schema query uses a wpdb table name and no user input.
            $this->wpdb->query("ALTER TABLE {$table}
                ADD COLUMN term_order INT(11) NOT NULL DEFAULT 0
                AFTER term_taxonomy_id");
            // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        }
    }

    /* =========================
     * READ: Get ordered posts
    ========================= */
    public function getOrderedPosts(int $term_taxonomy_id): array
    {
        $this->ensureTermOrderColumn();

        $posts_table = $this->wpdb->posts;
        $tr_table    = $this->wpdb->term_relationships;

        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table names are provided by wpdb.
        $posts = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "
                SELECT
                    p.ID,
                    p.post_title,
                    p.post_status,
                    p.post_type,
                    tr.term_order
                FROM {$posts_table} p
                INNER JOIN {$tr_table} tr
                    ON p.ID = tr.object_id
                WHERE tr.term_taxonomy_id = %d
                  AND p.post_status IN ('publish', 'draft', 'pending')
                ORDER BY tr.term_order ASC, p.ID ASC
                ",
                $term_taxonomy_id
            )
        );
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

        return $posts;
    }

    /* =========================
     * WRITE: Update order
    ========================= */
    public function updateOrder(int $term_taxonomy_id, array $post_ids): void
    {
        $this->ensureTermOrderColumn();
        $term = get_term_by(
            'term_taxonomy_id',
            $term_taxonomy_id,
            'series'
        );
        $this->removeMissingPostsFromSeries(
            $term->term_id,
            $post_ids
        );

        if (! $term || is_wp_error($term)) {
            return;
        }

        foreach ($post_ids as $index => $post_id) {
            $this->wpdb->replace(
                $this->wpdb->term_relationships,
                [
                    'object_id'        => (int) $post_id,
                    'term_taxonomy_id' => (int) $term_taxonomy_id,
                    'term_order'       => (int) $index,
                ],
                [
                    '%d',
                    '%d',
                    '%d',
                ]
            );
        }
    }

    /* ===========================
        *remove posts from series
        ========================= */

    public function removeMissingPostsFromSeries(int $term_id, array $post_ids): void
    {
        $current_posts = get_posts([
            'post_type' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'tax_query' => [
                [
                    'taxonomy' => 'series',
                    'field' => 'term_id',
                    'terms' => $term_id,
                ],
            ],
        ]);

        $removed_posts = array_diff($current_posts, $post_ids);

        foreach ($removed_posts as $post_id) {
            wp_remove_object_terms(
                $post_id,
                $term_id,
                'series'
            );
        }
    }

    /* =========================
     * Persist order meta
    ========================= */
    public function persistOrderMeta(int $term_id, array $post_ids): void
    {
        update_term_meta(
            $term_id,
            'sm_series_order',
            implode(',', array_map('intval', $post_ids))
        );
    }

    public function getSeriesTermsForPostType(string $post_type): array
    {
        $post_type = sanitize_key($post_type);

        $terms = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT t.term_id, t.name, t.slug, tx.taxonomy,
                    COUNT(tr.object_id) AS post_count
                FROM {$this->wpdb->terms} t
                INNER JOIN {$this->wpdb->term_taxonomy} tx ON t.term_id = tx.term_id
                LEFT JOIN {$this->wpdb->term_relationships} tr ON tx.term_taxonomy_id = tr.term_taxonomy_id
                LEFT JOIN {$this->wpdb->posts} p ON tr.object_id = p.ID AND p.post_type = %s
                WHERE tx.taxonomy = 'series'
                GROUP BY t.term_id, t.name, t.slug, tx.taxonomy
                ORDER BY t.name ASC",
                $post_type
            )
        );

        return is_array($terms) ? $terms : [];
    }

    public function getSeriesTermsForPost(int $post_id, ?array $series_ids = null): array
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

    public function getTermTaxonomyId(int $term_id): ?int
    {
        $term_taxonomy_id = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT term_taxonomy_id FROM {$this->wpdb->term_taxonomy} WHERE term_id = %d",
                $term_id
            )
        );

        return $term_taxonomy_id ? (int) $term_taxonomy_id : null;
    }

    public function getOrderedPostIds(int $term_taxonomy_id): array
    {
        $this->ensureTermOrderColumn();

        $post_ids = $this->wpdb->get_col(
            $this->wpdb->prepare(
                "SELECT object_id FROM {$this->wpdb->term_relationships}
                 WHERE term_taxonomy_id = %d
                 ORDER BY term_order ASC, object_id ASC",
                $term_taxonomy_id
            )
        );

        return array_map('intval', $post_ids ?: []);
    }

    public function removePostFromSeries(int $term_taxonomy_id, int $post_id): void
    {
        $this->wpdb->delete(
            $this->wpdb->term_relationships,
            [
                'term_taxonomy_id' => $term_taxonomy_id,
                'object_id'        => $post_id,
            ],
            ['%d', '%d']
        );
    }

    public function getPostIdsByTerm(int $term_id): ?string
    {
        $post_ids = get_term_meta($term_id, 'series_post_ids', true);

        return is_string($post_ids) && '' !== $post_ids ? $post_ids : null;
    }

    /* =========================
     * Get all series terms
    ========================= */
    public function getAllSeries(): array
    {
        return get_terms([
            'taxonomy'   => 'series',
            'hide_empty' => false,
        ]);
    }

    /* =========================
     * Top series
    ========================= */
    public function getTopSeries(int $limit = 5): array
    {
        return get_terms([
            'taxonomy'   => 'series',
            'orderby'    => 'count',
            'order'      => 'DESC',
            'number'     => $limit,
            'hide_empty' => true,
        ]);
    }

    /* =========================
     * Series by user
    ========================= */
    public function getSeriesByUser(int $user_id): array
    {
        $terms = get_terms([
            'taxonomy'   => 'series',
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms)) {
            return [];
        }

        $user_series = [];

        foreach ($terms as $term) {
            $posts = get_posts([
                'post_type'      => 'any',
                'author'         => $user_id,
                'posts_per_page' => 1,
                'tax_query'      => [
                    [
                        'taxonomy' => 'series',
                        'terms'    => $term->term_id,
                        'field'    => 'term_id',
                    ],
                ],
            ]);

            if (!empty($posts)) {
                $user_series[] = $term;
            }
        }

        return $user_series;
    }

    /* =========================
     * Topics by user
    ========================= */
    public function getTopicsByUser(int $user_id): array
    {
        if (!taxonomy_exists('topics')) {
            return [];
        }

        $terms = get_terms([
            'taxonomy'   => 'topics',
            'hide_empty' => false,
            'meta_query' => [
                [
                    'key'     => 'user_id',
                    'value'   => $user_id,
                    'compare' => '=',
                ],
            ],
        ]);

        if (is_wp_error($terms)) {
            return [];
        }

        return $terms;
    }
}
