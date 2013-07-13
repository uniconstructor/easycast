<?php

/**
 * Добавляет тип заказа (обычный, срочный) и данные заказа
 */
class m130506_122400_addUsersToOrder extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    private $_tableName = "{{fast_orders}}";
    
    public function safeUp()
    {
        // добавляем поле для хранения сериализованных данных заказа
        $this->addColumn($this->_tableName, 'orderdata', "TEXT DEFAULT NULL");
        
        // Добавляем версию заказа (позже формат хранения будет меняться - мы должны будем отличать их)
        $this->addColumn($this->_tableName, 'version', "int(11) NOT NULL DEFAULT 0");
        $this->createIndex('idx_version', $this->_tableName, 'version');
        
        // Добавляем тип заказа (обычный/срочный)
        $this->addColumn($this->_tableName, 'type', "VARCHAR(20) NOT NULL DEFAULT 'normal'");
        $this->createIndex('idx_type', $this->_tableName, 'type');
    }
}