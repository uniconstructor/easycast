<?php

class m131017_014400_upgradeReportLinks extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{report_links}}";
        
        $this->dropColumn($table, 'linkid');
        
        $this->addColumn($table, 'objecttype', "varchar(50) NOT NULL DEFAULT ''");
        $this->createIndex('idx_objecttype', $table, 'objecttype');
        $this->addColumn($table, 'objectid', "INT(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_objectid', $table, 'objectid');
    }
}