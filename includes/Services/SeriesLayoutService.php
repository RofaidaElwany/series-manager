<?php

namespace Service;

if (! defined('ABSPATH')) {
    exit;
}

class SeriesLayoutService
{
    private SeriesSettingsService $settingsService;

    public function __construct(SeriesSettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function getLayoutPosition(int $term_id): string
    {
        $settings = $this->settingsService->getSettings($term_id);
        $position = $settings['position'] ?? 'bottom';

        return in_array($position, ['top', 'bottom'], true) ? $position : 'bottom';
    }

    public function saveLayoutPosition(int $term_id, string $position): bool
    {
        $position = $this->normalizePosition($position);

        if ($position === '') {
            return false;
        }

        return $this->settingsService
            ->updateSettings(
                $term_id,
                [
                    'position' => $position,
                ]
            );
    }

    private function normalizePosition(string $position): string
    {
        $position = sanitize_text_field($position);

        return in_array($position, ['top', 'bottom'], true) ? $position : '';
    }
}
