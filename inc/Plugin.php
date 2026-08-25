<?php

namespace Wolf\HelloAsso;

use Wolf\Core\Migration\Migrator;

class Plugin
{
    public function run()
    {
        add_action('init', [$this, 'init']);
    }

    public function init()
    {
        Migrator::upgrade('wolf-helloasso', WOLF_HELLOASSO_PLUGIN_DIR, 'Wolf\HelloAsso', WOLF_HELLOASSO_PLUGIN_VERSION);
        $admin = new Admin();
        $admin->setup();

        $api = new Api();
        $api->setup();
    }
}