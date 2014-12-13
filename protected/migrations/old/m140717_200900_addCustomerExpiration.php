<?php

class m140717_200900_addCustomerExpiration extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{customer_invites}}";
        $this->addColumn($table, 'expire', 'int(11) UNSIGNED NOT NULL DEFAULT 0');
        $this->createIndex('idx_expire', $table, 'expire');
    }
}