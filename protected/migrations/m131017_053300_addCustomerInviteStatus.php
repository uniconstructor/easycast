<?php

/**
 * Добавляет статус и отзыв к одноразовому приглашению заказчика
 */
class m131017_053300_addCustomerInviteStatus extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{customer_invites}}";
        
        $this->addColumn($table, 'timefinished', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_timefinished', $table, 'timefinished');
        $this->addColumn($table, 'feedback', "VARCHAR(4095) NOT NULL DEFAULT ''");
        $this->createIndex('idx_feedback', $table, 'feedback');
        $this->addColumn($table, 'status', "VARCHAR(50) NOT NULL DEFAULT 'draft'");
        $this->createIndex('idx_status', $table, 'status');
    }
}