<?php
namespace frontend\designes;

if (! defined('ABSPATH')) {
    exit;
}

class AccordionLayout
{
    public static function render($attributes = [])
    {
        // Fallback to the list layout until accordion markup is implemented.
        return ListLayout::render($attributes);
    }
}
