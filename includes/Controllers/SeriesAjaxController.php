<?php

if (! defined('ABSPATH')) {
    exit;
}



class SeriesAjaxController
{
    private SeriesRepository $repository;
    private \Service\SeriesService $service;
    private SeriesFormatter $formatter;
    private \Service\SeriesLayoutService $layoutService;

    public function __construct(SeriesRepository $repository, \Service\SeriesService $service, SeriesFormatter $formatter, \Service\SeriesLayoutService $layoutService)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->formatter = $formatter;
        $this->layoutService = $layoutService;

        add_action('wp_ajax_sm_get_series_terms', [$this, 'ajaxGetSeriesTerms']);
        add_action('wp_ajax_sm_create_series_term', [$this, 'ajaxCreateSeriesTerm']);
        add_action('wp_ajax_sm_get_series_posts', [$this, 'ajaxGetSeriesPosts']);
        add_action('wp_ajax_sm_update_series_order', [$this, 'ajaxUpdateOrder']);
        add_action('wp_ajax_sm_update_series_layout_settings', [$this, 'ajaxUpdateSeriesLayoutSettings']);
        add_action('wp_ajax_sm_remove_post_from_series', [$this, 'ajaxRemovePostFromSeries']);
    }

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
        $terms = $this->repository->getSeriesTermsForPostType($post_type);

        $formatted_terms = array_map(function ($term) {
            return [
                'id' => (int) $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'taxonomy' => $term->taxonomy,
                'count' => (int) $term->post_count,
                'layoutPosition' => $this->layoutService->getLayoutPosition((int) $term->term_id),
            ];
        }, $terms);

        wp_send_json_success($formatted_terms);
    }

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
            'id' => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
            'taxonomy' => $term->taxonomy,
            'count' => $term->count,
            'layoutPosition' => $this->layoutService->getLayoutPosition((int) $term->term_id),
        ];

        wp_send_json_success($response);
    }

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
        if (! $term_id) {
            wp_send_json_error(['message' => 'Invalid term ID']);
        }

        $term = get_term($term_id);
        if (! $term || is_wp_error($term)) {
            wp_send_json_error(['message' => 'Term not found']);
        }

        $posts = $this->repository->getOrderedPosts($term->term_taxonomy_id);
        $formatted = $this->formatter->formatPosts($posts);

        wp_send_json_success($formatted);
    }

    public function ajaxUpdateOrder()
    {
        if (! current_user_can('edit_posts')) {
            wp_send_json_error(['message' => 'No permission']);
        }

        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (! wp_verify_nonce($nonce, 'sm_series_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        $term_id = isset($_POST['term_id']) ? absint(wp_unslash($_POST['term_id'])) : 0;
        $post_ids_str = isset($_POST['post_ids']) ? sanitize_text_field(wp_unslash($_POST['post_ids'])) : '';
        $post_ids = $this->service->parsePostIds($post_ids_str);

        if (! $term_id || empty($post_ids)) {
            wp_send_json_error(['message' => 'Invalid data']);
        }

        $term = get_term($term_id, 'series');
        if (! $term || is_wp_error($term)) {
            wp_send_json_error(['message' => 'Invalid term']);
        }

        $this->repository->updateOrder($term->term_taxonomy_id, $post_ids);
        $this->repository->persistOrderMeta($term->term_id, $post_ids);
        wp_update_term_count_now([$term->term_taxonomy_id], 'series');

        wp_send_json_success(['message' => 'Order updated successfully']);
    }

    public function ajaxUpdateSeriesLayoutSettings()
    {
        if (! current_user_can('manage_categories')) {
            wp_send_json_error(['message' => 'No permission']);
        }

        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (! wp_verify_nonce($nonce, 'sm_series_nonce')) {
            wp_send_json_error(['message' => 'Invalid nonce']);
        }

        $term_id = isset($_POST['term_id']) ? absint(wp_unslash($_POST['term_id'])) : 0;
        $position = isset($_POST['layout_position']) ? sanitize_text_field(wp_unslash($_POST['layout_position'])) : 'bottom';

        if (! $term_id || ! in_array($position, ['top', 'bottom'], true)) {
            wp_send_json_error(['message' => 'Invalid layout settings']);
        }

        $term = get_term($term_id, 'series');
        if (! $term || is_wp_error($term)) {
            wp_send_json_error(['message' => 'Invalid term']);
        }

        if (! $this->layoutService->saveLayoutPosition($term_id, $position)) {
            wp_send_json_error(['message' => 'Unable to save layout settings']);
        }

        wp_send_json_success([
            'message' => 'Layout settings updated successfully',
            'termId' => $term_id,
            'layoutPosition' => $position,
        ]);
    }

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

        if (! $term_id || ! $post_id) {
            wp_send_json_error(['message' => 'Invalid data']);
        }

        $term = get_term($term_id);
        if (! $term || is_wp_error($term)) {
            wp_send_json_error(['message' => 'Invalid term']);
        }

        $removed = wp_remove_object_terms($post_id, $term_id, 'series');
        if (is_wp_error($removed)) {
            wp_send_json_error(['message' => 'Error removing term']);
        }

        $this->repository->removePostFromSeries($term->term_taxonomy_id, $post_id);

        wp_send_json_success(['message' => 'Post removed from series']);
    }
}
