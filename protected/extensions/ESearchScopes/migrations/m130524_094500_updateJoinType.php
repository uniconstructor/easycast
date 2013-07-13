<?php

class m130524_094500_updateJoinType extends CDbMigration
{
    public function safeUp()
    {
        $table = "{{scope_conditions}}"; 
        
        $this->alterColumn($table, 'join', 'varchar(128) DEFAULT NULL');
        $this->renameColumn($table, 'join', 'jointype');
        $this->dropIndex('idx_join', $table);
        $this->createIndex('idx_jointype', $table, 'jointype');
    }
}