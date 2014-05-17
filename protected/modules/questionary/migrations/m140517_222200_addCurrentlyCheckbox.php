<?php

class m140517_222200_addCurrentlyCheckbox extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{q_tvshow_instances}}";
        
        $this->addColumn($table, 'timemodified', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->addColumn($table, 'currently', 'tinyint(1) DEFAULT NULL');
        $this->createIndex('idx_timemodified', $table, 'timemodified');
        $this->createIndex('idx_currently', $table, 'currently');
        
        $table = "{{q_theatre_instances}}";
        $this->addColumn($table, 'currently', 'tinyint(1) DEFAULT NULL');
        $this->createIndex('idx_currently', $table, 'currently');
    }
}