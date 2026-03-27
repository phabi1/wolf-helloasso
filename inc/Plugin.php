<?php

namespace Wolf\HelloAsso;

class Plugin
{
    public function run()
    {
        add_action('init', [$this, 'init']);
    }

    public function init()
    {
        $admin = new Admin();
        $admin->setup();

        $api = new Api();
        $api->setup();

        $webhooks = new Webhooks();
        $webhooks->setup();
    }
}