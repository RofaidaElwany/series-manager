<?php

if (! defined('ABSPATH')) {
    exit;
}

class HookLoader
{

    /**
     * @var object[]
     */
    private array $subscribers = [];

    public function register(object $subscriber): void
    {
        $this->subscribers[] = $subscriber;

        if (method_exists($subscriber, 'register')) {
            $subscriber->register();
        }
    }
}
