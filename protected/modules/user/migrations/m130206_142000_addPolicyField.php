<?php

class m130206_142000_addPolicyField extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    private $_tableName = "{{users}}";
    public function safeUp()
    {
        // согласие с политикой сайта
        $this->addColumn($this->_tableName, 'policyagreed', "tinyint(1) NOT NULL DEFAULT 1");
        $this->createIndex('idx_policyagreed', $this->_tableName, 'policyagreed');
        // ключ для отписки от новостей
        $this->addColumn($this->_tableName, 'unsubscribekey', "VARCHAR(128) DEFAULT NULL");
        $this->createIndex('idx_unsubscribekey', $this->_tableName, 'unsubscribekey');
    }
}