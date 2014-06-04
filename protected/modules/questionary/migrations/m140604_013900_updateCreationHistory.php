<?php

class m140604_013900_updateCreationHistory extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{q_creation_history}}';
        
        $this->renameColumn($table, 'userid', 'objectid');
        $this->addColumn($table, 'objecttype', "varchar(50) NOT NULL DEFAULT 'user'");
        $this->createIndex('idx_objecttype', $table, 'objecttype');
    }
}