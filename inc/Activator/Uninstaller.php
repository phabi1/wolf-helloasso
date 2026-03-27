<?php

namespace Wolf\HelloAsso\Activator;

class Uninstaller
{
    public function run()
    {
        $this->dropTables();
    }

    private function dropTables()
    {
        global $wpdb;

        $sqls = file_get_contents(__DIR__ . '/../sql/install.sql');
        $queries = array_filter(array_map('trim', explode(';', $sqls)));
        foreach ($queries as $query) {
            if (!empty($query)) {
                $wpdb->query(str_replace('{prefix}', $wpdb->prefix, $query));
            }
        }
    }
}