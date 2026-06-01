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
require_once __DIR__ . '/HookLoader.php';

class SM_Series_Plugin
{
    public static function init(): void
    {
        global $wpdb;

        $repository = new SeriesRepository($wpdb);
        $service = new \Service\SeriesService();
        $layoutService = new \Service\SeriesLayoutService();
        $formatter = new SeriesFormatter();

        new SeriesAjaxController($repository, $service, $formatter, $layoutService);

        $hook_loader = new HookLoader();
        $hook_loader->register(new SavePostSubscriber($repository, $service));
    }
}
