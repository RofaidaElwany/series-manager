<?php

if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/../Repositories/SeriesRepository.php';
require_once __DIR__ . '/../Formatters/SeriesFormatter.php';
require_once __DIR__ . '/../Controllers/SeriesAjaxController.php';
require_once __DIR__ . '/../Hooks/SavePostSubscriber.php';
require_once __DIR__ . '/../Services/SeriesLayoutService.php';
require_once __DIR__ . '/../Services/SeriesService.php';
require_once __DIR__ . '/../Services/SeriesSettingsService.php';
require_once __DIR__ . '/HookLoader.php';

class SM_Series_Plugin
{
    public static function init(): void
    {
        global $wpdb;

        // 1. Repository - Database access layer
        $repository = new SeriesRepository($wpdb);

        // 2. Settings Service - Dependency for layout service
        $settingsService = new \Service\SeriesSettingsService();

        // 4. Series Service - Business logic for series operations
        $service = new \Service\SeriesService();

        // 5. Formatter - Output formatting only
        $formatter = new SeriesFormatter();

        // 6. Controller - Thin request handler that delegates to services
        new SeriesAjaxController($repository, $service, $formatter, $settingsService);

        // Register hooks
        $hook_loader = new HookLoader();
        $hook_loader->register(new SavePostSubscriber($repository, $service));
    }
}
