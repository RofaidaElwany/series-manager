<?php

if (! defined('ABSPATH')) {
    exit;
}

class SavePostSubscriber
{
    private SeriesRepository $repository;
    private \Service\SeriesService $service;

    public function __construct(SeriesRepository $repository, \Service\SeriesService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function register(): void
    {
        add_action('save_post', [$this, 'handle'], 20, 3);
    }

    /**
     * @param int $post_id
     * @param \WP_Post $post
     * @param bool $update
     */
    public function handle($post_id, $post, $update): void
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

        foreach ($terms as $term_id) {
            $term_taxonomy_id = $this->repository->getTermTaxonomyId((int) $term_id);
            if (! $term_taxonomy_id) {
                continue;
            }

            $order_str = get_term_meta($term_id, 'sm_series_order', true);
            if ($order_str) {
                $post_ids = $this->service->parsePostIds($order_str);
                $index = array_search($post_id, $post_ids, true);

                if ($index === false) {
                    $post_ids[] = $post_id;
                    $this->repository->updateOrder($term_taxonomy_id, $post_ids);
                    $this->repository->persistOrderMeta((int) $term_id, $post_ids);
                    continue;
                }

                $this->repository->updateOrder($term_taxonomy_id, $post_ids);
                continue;
            }

            $existing_post_ids = $this->repository->getOrderedPostIds($term_taxonomy_id);
            if (empty($existing_post_ids)) {
                continue;
            }

            if (! in_array($post_id, $existing_post_ids, true)) {
                $existing_post_ids[] = $post_id;
            }

            $this->repository->updateOrder($term_taxonomy_id, $existing_post_ids);
            $this->repository->persistOrderMeta((int) $term_id, $existing_post_ids);
        }
    }
}
