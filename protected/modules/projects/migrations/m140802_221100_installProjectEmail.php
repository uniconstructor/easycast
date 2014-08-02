<?php

class m140802_221100_installProjectEmail extends CDbMigration
{
    public function safeUp()
    {
        $table = "{{projects}}";
        $this->addColumn($table, 'email', "VARCHAR(255) DEFAULT NULL");
        $this->createIndex('idx_email', $table, 'email');
    }
}