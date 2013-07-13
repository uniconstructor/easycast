<?php

class m130519_023100_removeParentId extends CDbMigration
{
    protected $MySqlOptions = "ENGINE=InnoDB CHARSET=utf8";
    
    public function safeUp()
    {
        $table = "{{search_scopes}}";
        $this->dropColumn($table, 'parentid');
    }
}