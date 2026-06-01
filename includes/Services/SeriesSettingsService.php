<?php

namespace Service;

if (! defined('ABSPATH')) {
    exit;
}

class SeriesSettingsService
{
    public function getDefaultSettings(): array
    {
        return [
            'position' => 'bottom',
        ];
    }

    public function getSettings(int $termId): array
    {
        $settings = get_term_meta(
            $termId,
            'sm_series_settings',
            true
        );

        if (! is_array($settings)) {
            $settings = [];
        }

        return wp_parse_args(
            $settings,
            $this->getDefaultSettings()
        );
    }

    public function updateSettings(
        int $termId,
        array $settings
    ): bool {
        $current = $this->getSettings($termId);

        $settings = array_merge(
            $current,
            $settings
        );

        return (bool) update_term_meta(
            $termId,
            'sm_series_settings',
            $settings
        );
    }
}