<?php

class m130518_233900_addConditionComparison extends CDbMigration
{
    protected $MySqlOptions = "ENGINE=InnoDB CHARSET=utf8";
    
    public function safeUp()
    {
        $table = "{{scope_conditions}}";
        // add new condition type
        $this->alterColumn($table, 'type', "enum('field', 'scope', 'sort', 'condition') NOT NULL");
        $this->dropIndex('idx_type', $table);
        $this->createIndex('idx_type', $table, 'type');
        
        // increase field sizes
        $this->alterColumn($table, 'field', "varchar(4095) DEFAULT NULL");
        $this->dropIndex('idx_field', $table);
        $this->createIndex('idx_field', $table, 'type');
        
        $this->alterColumn($table, 'value', "varchar(4095) DEFAULT NULL");
        $this->dropIndex('idx_value', $table);
        $this->createIndex('idx_value', $table, 'value');
    }
}