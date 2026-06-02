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
            'layout' => 'list',
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

    public function sanitizeSettings(array $settings): array
    {
        $sanitized = [];

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeSettings($value);
                continue;
            }

            if (is_bool($value) || is_int($value) || is_float($value)) {
                $sanitized[$key] = $value;
                continue;
            }

            $sanitized[$key] = sanitize_text_field((string) $value);
        }

        return $sanitized;
    }

    public function updateSettings(int $termId, array $settings): bool
    {
        $settings = $this->sanitizeSettings($settings);
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
