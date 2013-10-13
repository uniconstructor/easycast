<?php

class m131014_020000_addVacancyTime extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{event_vacancies}}';
        
        $this->addColumn($table, 'timestart', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_timestart', $table, 'timestart');
        $this->addColumn($table, 'timeend', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_timeend', $table, 'timeend');
    }
}