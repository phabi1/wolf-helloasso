<?php

namespace Wolf\HelloAsso\Activator;

class Installer
{
    public function run()
    {
        $this->createTables();

        $this->setCredentials();
    }

    private function createTables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sqls = file_get_contents(__DIR__ . '/../sql/install.sql');
        $queries = array_filter(array_map('trim', explode(';', $sqls)));
        foreach ($queries as $query) {
            if (!empty($query)) {
                $wpdb->query(str_replace('{prefix}', $wpdb->prefix, $query));
            }
        }
    }

    private function setCredentials()
    {
        set_option('wolf_helloasso_credentials', [
            'api_key' => '',
            'api_secret' => '',
        ]);
    }
}