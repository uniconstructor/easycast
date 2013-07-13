<?php

class m130509_090600_increaseCommentLength extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    private $_tableName = "{{fast_orders}}";
    
    public function safeUp()
    {
        $this->alterColumn($this->_tableName, 'status', "VARCHAR(20) NOT NULL DEFAULT 'active'");
        $this->dropIndex('idx_status', $this->_tableName);
        $this->createIndex('idx_status', $this->_tableName, 'status');
        
        $this->alterColumn($this->_tableName, 'comment', "VARCHAR(4095) DEFAULT NULL");
        $this->dropIndex('idx_comment', $this->_tableName);
        $this->createIndex('idx_comment', $this->_tableName, 'comment');
        
        $this->alterColumn($this->_tableName, 'ourcomment', "VARCHAR(4095) DEFAULT NULL");
        $this->dropIndex('idx_ourcomment', $this->_tableName);
        $this->createIndex('idx_ourcomment', $this->_tableName, 'ourcomment');
        
        $this->alterColumn($this->_tableName, 'name', "VARCHAR(255) DEFAULT NULL");
        $this->dropIndex('idx_name', $this->_tableName);
        $this->createIndex('idx_name', $this->_tableName, 'name');
    }
}