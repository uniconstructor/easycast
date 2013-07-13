<?php

class m130129_145600_installFastOrderTable extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';

    public function safeUp()
    {
        $table = '{{fast_orders}}';
        
        $fields = array(
            "id" => "pk",
            "timecreated" => "int(11) UNSIGNED DEFAULT NULL",
            "timemodified" => "int(11) UNSIGNED DEFAULT NULL",
            "name" => "varchar(128) DEFAULT NULL",
            "phone" => "varchar(20) DEFAULT NULL",
            "email" => "varchar(255) DEFAULT NULL",
            "status" => "enum('active', 'closed') DEFAULT 'active'",
            "comment" => "varchar(255) DEFAULT NULL",
            "ourcomment" => "varchar(255) DEFAULT NULL",
            "solverid" => "int(11) UNSIGNED DEFAULT 0",
            "customerid" => "int(11) UNSIGNED DEFAULT 0",
        );
        
        $this->createTable($table, $fields, $this->MySqlOptions);
        
        // получаем все имена полей чтобы потом создать индексы по каждому из них
        $fieldNames = array_keys($fields);
        
        // перечисляем поля, которые мы не будем индексировать
        $noIndex = array('id');
        
        // оставляем только те поля, которые будем индексировать
        $indexedFields = array_diff($fieldNames, $noIndex);
        
        foreach ( $indexedFields as $field )
        {
            $this->createIndex('idx_'.$field, $table, $field);
        }
    }

}