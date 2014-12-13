<?php

class m140617_065300_modifyStatusHistory extends CDbMigration
{
    public function safeUp()
    {
        $table = "{{status_history}}";
        
        $this->renameColumn($table, 'userid', 'sourceid');
        $this->addColumn($table, 'sourcetype', "varchar(50) NOT NULL DEFAULT 'user'");
        $this->createIndex('idx_sourcetype', $table, 'sourcetype');
    }
}