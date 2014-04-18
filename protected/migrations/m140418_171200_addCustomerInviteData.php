<?php

/**
 * 
 */
class m140418_171200_addCustomerInviteData extends CDbMigration
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