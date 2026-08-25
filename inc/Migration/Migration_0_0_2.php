<?php

namespace Wolf\HelloAsso\Migration;

use Wolf\Core\Migration\MigrationInterface;

class Migration_0_0_2 implements MigrationInterface
{
    public function up()
    {
        $this->dropMappingTable();
    }

    public function down()
    {
    }

    private function dropMappingTable()
    {
        global $wpdb;
        $tableName = $wpdb->prefix . 'wolf_helloasso_mapping';
        $wpdb->query("DROP TABLE IF EXISTS $tableName");
    }
}