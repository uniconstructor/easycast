<?php

/**
 * Добавляет возможность объединять события в группы
 */
class m130731_063100_addEventGroups extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{event_invites}}';
        
        $this->addColumn($table, 'subscribekey', "VARCHAR(40) NOT NULL DEFAULT ''");
        $this->createIndex('idx_subscribekey', $table, 'subscribekey');

        
        $table = '{{project_events}}';
        
        $this->addColumn($table, 'type', "VARCHAR(20) NOT NULL DEFAULT 'event'");
        $this->createIndex('idx_type', $table, 'type');
        
        $this->addColumn($table, 'parentid', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_parentid', $table, 'parentid');
        
        $this->addColumn($table, 'memberinfo', "VARCHAR(4095) DEFAULT NULL");
        $this->createIndex('idx_memberinfo', $table, 'memberinfo');
        
        $this->addColumn($table, 'showtimestart', "tinyint(1) UNSIGNED NOT NULL DEFAULT 1");
        $this->createIndex('idx_showtimestart', $table, 'showtimestart');
        // Estimated Time Arrival
        $this->addColumn($table, 'eta', "int(6) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_eta', $table, 'eta');
        
        $this->addColumn($table, 'salary', "VARCHAR(32) NOT NULL DEFAULT ''");
        $this->createIndex('idx_salary', $table, 'salary');
        
        
        $table = '{{event_vacancies}}';
        
        $this->addColumn($table, 'autoconfirm', "tinyint(1) UNSIGNED NOT NULL DEFAULT 1");
        $this->createIndex('idx_autoconfirm', $table, 'autoconfirm');
    }
}