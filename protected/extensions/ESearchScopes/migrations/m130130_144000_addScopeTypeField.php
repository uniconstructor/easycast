<?php

/**
 * Adds 'type' field to the scopes table
 */
class m130130_144000_addScopeTypeField extends CDbMigration
{
    protected $MySqlOptions = "ENGINE=InnoDB CHARSET=utf8";
    
    /**
     * @var string search scopes table name
     */
    protected $_scopesTable = "{{search_scopes}}";
    
    public function safeUp()
    {
        // scope type (optional)
        $this->addColumn($this->_scopesTable, 'type', "VARCHAR(128) DEFAULT NULL");
        $this->createIndex('idx_type', $this->_scopesTable, 'type');
    }
}