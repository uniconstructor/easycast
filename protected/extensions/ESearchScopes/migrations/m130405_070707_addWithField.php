<?php

/**
 * added "with" field, fixed errors
 */
class m130405_070707_addWithField extends CDbMigration
{
    protected $MySqlOptions = "ENGINE=InnoDB CHARSET=utf8";

    public function safeUp()
    {
        $table = "{{search_scopes}}";
        $this->dropIndex('idx_parentid', $table);
        $this->alterColumn($table, 'modelid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_parentid', $table, 'parentid');
        
        unset($table);


        $table = "{{scope_conditions}}";
        $this->addColumn($table, 'rawvalue', "tinyint(1) UNSIGNED DEFAULT 0");
        $this->createIndex('idx_rawvalue', $table, 'rawvalue');
        
        $this->addColumn($table, 'with', "varchar(255) DEFAULT NULL");
        $this->createIndex('idx_with', $table, 'with');
        
        $this->dropIndex('idx_combine', $table);
        $this->alterColumn($table, 'combine', "enum('and', 'or') DEFAULT NULL");
        $this->createIndex('idx_combine', $table, 'combine');
        
        unset($table);
    }
}