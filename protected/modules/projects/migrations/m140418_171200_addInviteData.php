<?php

/**
 * 
 */
class m140418_171200_addInviteData extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{customer_invites}}";
        
        $this->addColumn($table, 'data', "text");
    }
}