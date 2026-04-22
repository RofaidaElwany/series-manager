<?php

class SeriesRepository
{
    private $wpdb;

    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /* =========================
     * Ensure DB Column
    ========================= */
    public function ensureTermOrderColumn()
    {
        $table = $this->wpdb->term_relationships;
        $exists = $this->wpdb->get_results(
            "SHOW COLUMNS FROM `$table` LIKE 'term_order'"
        );
        if (empty($exists)) {
            $this->wpdb->query(
                "ALTER TABLE `$table`
                 ADD COLUMN `term_order` INT(11) NOT NULL DEFAULT 0
                 AFTER `term_taxonomy_id`"
            );
        }
    }

    /* =========================
     * READ: Get ordered posts
    ========================= */
    public function getOrderedPosts(int $term_taxonomy_id): array
    {
        $this->ensureTermOrderColumn();

        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT p.ID, p.post_title, p.post_status, p.post_type, tr.term_order
                 FROM {$this->wpdb->posts} p
                 INNER JOIN {$this->wpdb->term_relationships} tr
                     ON p.ID = tr.object_id
                 WHERE tr.term_taxonomy_id = %d
                 AND p.post_status IN ('publish', 'draft', 'pending')
                 ORDER BY tr.term_order ASC, p.ID ASC",
                $term_taxonomy_id
            )
        );
    }

    /* =========================
     * WRITE: Update order
    ========================= */
    public function updateOrder(int $term_taxonomy_id, array $post_ids)
    {
        $this->ensureTermOrderColumn();

        foreach ($post_ids as $index => $post_id) {
            $this->wpdb->replace(
                $this->wpdb->term_relationships,
                [
                    'object_id'        => (int) $post_id,
                    'term_taxonomy_id' => (int) $term_taxonomy_id,
                    'term_order'       => (int) $index,
                ],
                ['%d', '%d', '%d']
            );
        }
    }

    /* =========================
     * WRITE: Persist order meta
    ========================= */
    public function persistOrderMeta(int $term_id, array $post_ids)
    {
        update_term_meta((int) $term_id, 'sm_series_order', implode(',', $post_ids));
    }

    public function getPostIdsByTerm(int $term_id): ?string
    {
        return $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT meta_value FROM {$this->wpdb->termmeta}
                 WHERE term_id = %d AND meta_key = %s",
                $term_id,
                'series_post_ids'
            )
        );
    }

    /* =========================
     * Get all series terms
    ========================= */
    public function getAllSeries(): array
    {
        return get_terms([
            'taxonomy' => 'series',
            'hide_empty' => false,
        ]);
    }

    /* =========================
     * Get top series by post count
    ========================= */
    public function getTopSeries(int $limit = 5): array
    {
        return get_terms([
            'taxonomy' => 'series',
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => $limit,
            'hide_empty' => true,
        ]);
    }

    /* =========================
     * Get series by user (author of posts in series)
    ========================= */
    public function getSeriesByUser(int $user_id): array
    {
        $terms = get_terms([
            'taxonomy' => 'series',
            'hide_empty' => false,
        ]);

        $user_series = [];
        foreach ($terms as $term) {
            $posts = get_posts([
                'post_type' => 'any',
                'tax_query' => [
                    [
                        'taxonomy' => 'series',
                        'terms' => $term->term_id,
                    ],
                ],
                'author' => $user_id,
                'posts_per_page' => 1,
            ]);

            if (!empty($posts)) {
                $user_series[] = $term;
            }
        }

        return $user_series;
    }

    /* =========================
     * Get topics/CSCs by user (assuming topics is another taxonomy or custom logic)
     * For now, assuming 'topics' is a custom taxonomy related to user
    ========================= */
    public function getTopicsByUser(int $user_id): array
    {
        // Check if topics taxonomy exists
        if (!taxonomy_exists('topics')) {
            return []; // Return empty array if taxonomy doesn't exist
        }

        // Assuming 'topics' is a taxonomy. Adjust if it's different.
        $terms = get_terms([
            'taxonomy' => 'topics', // Change if different
            'hide_empty' => false,
            'meta_query' => [
                [
                    'key' => 'user_id',
                    'value' => $user_id,
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
