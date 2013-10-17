<?php

/**
 * Убирает enum-поля из таблиц проектов и мероприятий
 */
class m131017_070000_newProjectType extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{projects}}';
        $this->alterColumn($table, 'status', "VARCHAR(50) NOT NULL DEFAULT 'draft'");
        $this->alterColumn($table, 'type', "VARCHAR(50) NOT NULL DEFAULT 'project'");
        
        $table = '{{project_events}}';
        $this->alterColumn($table, 'status', "VARCHAR(50) NOT NULL DEFAULT 'draft'");
        $this->alterColumn($table, 'type', "VARCHAR(50) NOT NULL DEFAULT 'event'");
        
        $table = '{{event_vacancies}}';
        $this->alterColumn($table, 'status', "VARCHAR(50) NOT NULL DEFAULT 'draft'");
    }
}