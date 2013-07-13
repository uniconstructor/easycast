<?php

class m130416_082000_increaseUsernameField extends CDbMigration
{
    protected $MySqlOptions = 'ENGINE=InnoDB CHARSET=utf8';
    
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{users}}';
        $this->dropIndex('user_username', $table);
        $this->alterColumn($table, 'username', "varchar(255) NOT NULL");
        $this->createIndex('user_username', $table, 'username');
    }
}