<?php

class m140531_170200_addQFieldsTable extends CDbMigration
{
    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        $table   = "{{q_user_fields}}";
        $columns = array(
            'id'         => 'pk',
            'name'       => "varchar(50) NOT NULL",
            'storage'    => "varchar(100) NOT NULL default 'questionary'",
            'fillpoints' => "int(11) UNSIGNED NOT NULL DEFAULT 0",
        );
        $this->createTable($table, $columns, $tableOptions);
        
        $this->createIndex('idx_name', $table, 'name');
        $this->createIndex('idx_storage', $table, 'storage');
        $this->createIndex('idx_fillpoints', $table, 'fillpoints');
    }
}