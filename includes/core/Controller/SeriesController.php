<?php

if (! defined('ABSPATH')) {
    exit;
}

class SeriesController
{
    /**
     * @var SeriesRepository
     */
    private $repository;

    /**
     * @var \Service\SeriesService
     */
    private $service;

    /**
     * @var SeriesFormatter
     */
    private $formatter;

    /**
     * @param SeriesRepository $repository
     * @param \Service\SeriesService $service
     * @param SeriesFormatter $formatter
     */
    public function __construct($repository, $service, $formatter)
    {
        $this->repository = $repository;
        $this->service    = $service;
        $this->formatter  = $formatter;

        // AJAX
        add_action('wp_ajax_sm_get_series_terms', [$this, 'ajaxGetSeriesTerms']);
        add_action('wp_ajax_sm_create_series_term', [$this, 'ajaxCreateSeriesTerm']);
        add_action('wp_ajax_sm_get_series_posts', [$this, 'ajaxGetSeriesPosts']);
        add_action('wp_ajax_sm_update_series_order', [$this, 'ajaxUpdateOrder']);
        add_action('wp_ajax_sm_remove_post_from_series', [$this, 'ajaxRemovePostFromSeries']);

        // Hook
        add_action('save_post', [$this, 'syncPostOrderOnSave'], 20, 3);
    }

    /* =========================
     * AJAX: Get Series Terms
     ========================= */
    public function ajaxGetSeriesTerms()
    {
        if (! current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'No permission']);
        }

        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (! wp_verify_nonce($nonce, 'sm_series_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        $post_type = isset($_POST['post_type']) ? sanitize_key(wp_unslash($_POST['post_type'])) : 'post';

        // Get all series terms for this post type, regardless of whether they have posts
        global $wpdb;
        $terms = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT t.term_id, t.name, t.slug, tx.taxonomy,
                COUNT(tr.object_id) as post_count
                FROM {$wpdb->terms} t
                INNER JOIN {$wpdb->term_taxonomy} tx ON t.term_id = tx.term_id
                LEFT JOIN {$wpdb->term_relationships} tr ON tx.term_taxonomy_id = tr.term_taxonomy_id
                LEFT JOIN {$wpdb->posts} p ON tr.object_id = p.ID AND p.post_type = %s
                WHERE tx.taxonomy = 'series'
                GROUP BY t.term_id, t.name, t.slug, tx.taxonomy
                ORDER BY t.name ASC",
                $post_type
            )
        );

        if (is_wp_error($terms)) {
            wp_send_json_error(['message' => 'Failed to fetch series']);
        }

        // Format terms
        $formatted_terms = array_map(function ($term) {
            return [
                'id'   => (int) $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'taxonomy' => $term->taxonomy,
                'count' => (int) $term->post_count,
            ];
        }, $terms);

        wp_send_json_success($formatted_terms);
    }

    /* =========================
     * AJAX: Create Series Term
     ========================= */
    public function ajaxCreateSeriesTerm()
    {
        if (! current_user_can('manage_categories')) {
            wp_send_json_error(['message' => 'No permission']);
        }

        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (! wp_verify_nonce($nonce, 'sm_series_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        if (empty($name)) {
            wp_send_json_error(['message' => 'Series name is required']);
        }

        $result = wp_insert_term($name, 'series');
        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        $term = get_term($result['term_id'], 'series');
        $response = [
            'id'   => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
            'taxonomy' => $term->taxonomy,
            'count' => $term->count,
        ];

        wp_send_json_success($response);
    }

    /* =========================
     * AJAX: Get Series Posts
     ========================= */
    public function ajaxGetSeriesPosts()
    {
        if (! current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'No permission']);
        }

        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (! wp_verify_nonce($nonce, 'sm_series_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        $term_id = isset($_POST['term_id']) ? absint(wp_unslash($_POST['term_id'])) : 0;

        if (!$term_id) {
            wp_send_json_error(['message' => 'Invalid term ID']);
        }

        $term = get_term($term_id);
        if (!$term || is_wp_error($term)) {
            wp_send_json_error(['message' => 'Term not found']);
        }


        $posts = $this->repository->getOrderedPosts($term->term_taxonomy_id);

        $formatted = $this->formatter->formatPosts($posts);

        wp_send_json_success($formatted);
    }

    /* =========================
     * AJAX: Update Order
     ========================= */
    public function ajaxUpdateOrder()
    {
        if (! current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'No permission']);
        }

        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (! wp_verify_nonce($nonce, 'sm_series_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        $term_id      = isset($_POST['term_id']) ? absint(wp_unslash($_POST['term_id'])) : 0;
        $post_ids_str = isset($_POST['post_ids']) ? sanitize_text_field(wp_unslash($_POST['post_ids'])) : '';

        $post_ids = $this->service->parsePostIds($post_ids_str);

        if (!$term_id || empty($post_ids)) {
            wp_send_json_error(['message' => 'Invalid data']);
        }

        $term = get_term($term_id, 'series');
        if (!$term || is_wp_error($term)) {
            wp_send_json_error(['message' => 'Invalid term']);
        }

        $this->repository->updateOrder($term->term_taxonomy_id, $post_ids);
        $this->repository->persistOrderMeta($term->term_id, $post_ids);
        wp_update_term_count_now([$term->term_taxonomy_id], 'series');

        wp_send_json_success(['message' => 'Order updated successfully']);
    }

    /* =========================
     * AJAX: Remove Post
     ========================= */
    public function ajaxRemovePostFromSeries()
    {
        if (! current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'No permission']);
        }

        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (! wp_verify_nonce($nonce, 'sm_series_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        $term_id = isset($_POST['term_id']) ? absint(wp_unslash($_POST['term_id'])) : 0;
        $post_id = isset($_POST['post_id']) ? absint(wp_unslash($_POST['post_id'])) : 0;

        if (!$term_id || !$post_id) {
            wp_send_json_error(['message' => 'Invalid data']);
        }

        $term = get_term($term_id);
        if (!$term || is_wp_error($term)) {
            wp_send_json_error(['message' => 'Invalid term']);
        }

        $removed = wp_remove_object_terms($post_id, $term_id, 'series');

        if (is_wp_error($removed)) {
            wp_send_json_error(['message' => 'Error removing term']);
        }

        global $wpdb;
        $wpdb->delete(
            $wpdb->term_relationships,
            [
                'term_taxonomy_id' => $term->term_taxonomy_id,
                'object_id'        => $post_id
            ],
            ['%d', '%d']
        );

        wp_send_json_success(['message' => 'Post removed from series']);
    }

    /* =========================
     * Sync Order On Save
     ========================= */
    /**
     * @param int $post_id
     * @param \WP_Post $post
     * @param bool $update
     */
    public function syncPostOrderOnSave($post_id, $post, $update)
    {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        if (! in_array($post->post_type, \Service\SeriesService::getSupportedPostTypes(), true)) {
            return;
        }

        $terms = wp_get_post_terms($post_id, 'series', ['fields' => 'ids']);

        if (is_wp_error($terms) || empty($terms)) {
            return;
        }

        global $wpdb;

        foreach ($terms as $term_id) {
            $term_taxonomy_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE term_id = %d",
                    $term_id
                )
            );

            if (! $term_taxonomy_id) {
                continue;
            }

            $order_str = get_term_meta($term_id, 'sm_series_order', true);
            if ($order_str) {
                $post_ids = $this->service->parsePostIds($order_str);
                $index    = array_search($post_id, $post_ids, true);

                if ($index === false) {
                    $post_ids[] = $post_id;
                    $this->repository->updateOrder($term_taxonomy_id, $post_ids);
                    $this->repository->persistOrderMeta($term_id, $post_ids);
                    continue;
                }

                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->term_relationships}
                         SET term_order = %d
                         WHERE term_taxonomy_id = %d AND object_id = %d",
                        $index,
                        $term_taxonomy_id,
                        $post_id
                    )
                );

                continue;
            }

            $existing_post_ids = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT object_id FROM {$wpdb->term_relationships}
                     WHERE term_taxonomy_id = %d
                     ORDER BY term_order ASC, object_id ASC",
                    $term_taxonomy_id
                )
            );

            if (empty($existing_post_ids)) {
                continue;
            }

            $existing_post_ids = array_map('intval', $existing_post_ids);
            if (! in_array($post_id, $existing_post_ids, true)) {
                $existing_post_ids[] = $post_id;
            }

            $this->repository->updateOrder($term_taxonomy_id, $existing_post_ids);
            $this->repository->persistOrderMeta($term_id, $existing_post_ids);
        }
    }
}
