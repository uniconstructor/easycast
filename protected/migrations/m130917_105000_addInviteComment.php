<?php

class m130917_105000_addInviteComment extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{customer_invites}}';
        
        $this->alterColumn($table, 'key', "varchar(40) NOT NULL DEFAULT ''");
        $this->alterColumn($table, 'key2', "varchar(40) NOT NULL DEFAULT ''");
        
        $this->addColumn($table, 'comment', "varchar(4095) NOT NULL DEFAULT ''");
        
        $this->addColumn($table, 'userid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_userid', $table, 'userid');
    }
}