<?php

/**
 * Эта миграция добавляет статусы приглашениям
 */
class m130728_064500_addInviteStatus extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{event_invites}}';
        
        $this->addColumn($table, 'timemodified', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_timemodified', $table, 'timemodified');
        
        $this->addColumn($table, 'status', "VARCHAR(20) NOT NULL DEFAULT 'pending'");
        $this->createIndex('idx_status', $table, 'status');
        
        $this->renameColumn($table, 'checked', 'deleted');
        $this->dropIndex('idx_checked', $table);
        $this->createIndex('idx_deleted', $table, 'deleted');
    }
}