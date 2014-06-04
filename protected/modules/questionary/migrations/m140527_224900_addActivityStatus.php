<?php

class m140527_224900_addActivityStatus extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{q_activities}}";
        
        $this->alterColumn($table, 'uservalue', "varchar(500) DEFAULT NULL");
        $this->addColumn($table, 'comment', "varchar(4095) DEFAULT NULL");
        $this->createIndex('idx_comment', $table, 'comment');
        $this->addColumn($table, 'status', "varchar(50) NOT NULL DEFAULT 'draft'");
        $this->createIndex('idx_status', $table, 'status');
    }
}