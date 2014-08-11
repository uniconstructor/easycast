<?php

class m140810_070000_addFieldsSortorder extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{q_field_instances}}";
        $this->addColumn($table, 'sortorder', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_sortorder', $table, 'sortorder');
        unset($table);
         
        $table = "{{extra_field_instances}}";
        $this->addColumn($table, 'sortorder', "int(11) UNSIGNED NOT NULL DEFAULT 0"); 
        $this->createIndex('idx_sortorder', $table, 'sortorder');
        unset($table);
    }
}