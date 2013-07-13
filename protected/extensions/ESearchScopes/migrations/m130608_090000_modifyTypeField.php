<?php

class m130608_090000_modifyTypeField extends CDbMigration
{
    public function safeUp()
    {
        $table = "{{scope_conditions}}";
        
        $this->alterColumn($table, 'type', 'varchar(16) DEFAULT NULL');
        $this->dropIndex('idx_type', $table);
        $this->createIndex('idx_type', $table, 'type');
    }
}