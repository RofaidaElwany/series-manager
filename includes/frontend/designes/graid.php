<?php
namespace frontend\designes;

if (! defined('ABSPATH')) {
    exit;
}

class GraidLayout
{
    public static function render($attributes = [])
    {
        // Fallback to the list layout until grid markup is implemented.
        return ListLayout::render($attributes);
    }
}
