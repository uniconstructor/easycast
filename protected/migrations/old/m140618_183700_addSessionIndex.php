<?php

class m140618_183700_addSessionIndex extends CDbMigration
{
    public function safeUp()
    {
        $table = 'YiiSession';
        $this->createIndex('idx_id', $table, 'id');
        $this->createIndex('idx_expire', $table, 'expire');
    }
}