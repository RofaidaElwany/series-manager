<?php

namespace Service;

if (! defined('ABSPATH')) {
    exit;
}

class SeriesLayoutService
{
    public function getLayoutPosition(int $term_id): string
    {
        $position = get_term_meta($term_id, 'sm_series_layout_position', true);

        if (! in_array($position, ['top', 'bottom'], true)) {
            $position = get_option('navigation_position', 'bottom');
        }

        return in_array($position, ['top', 'bottom'], true) ? $position : 'bottom';
    }

    public function saveLayoutPosition(int $term_id, string $position): bool
    {
        $position = $this->normalizePosition($position);

        if ($position === '') {
            return false;
        }

        return (bool) update_term_meta($term_id, 'sm_series_layout_position', $position);
    }

    private function normalizePosition(string $position): string
    {
        $position = sanitize_text_field($position);

        return in_array($position, ['top', 'bottom'], true) ? $position : '';
    }
}
