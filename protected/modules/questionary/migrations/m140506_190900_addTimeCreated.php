<?php

/**
 * Добавляет недостающие поля времени время создания и изменения к оставшимся таблицам анкеты 
 */
class m140506_190900_addTimeCreated extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table  = "{{q_theatres}}";
        $this->addColumn($table, 'timecreated', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->addColumn($table, 'timemodified', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_timecreated', $table, 'timecreated');
        $this->createIndex('idx_timemodified', $table, 'timemodified');
        
        $table  = "{{q_universities}}";
        $this->addColumn($table, 'timecreated', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->addColumn($table, 'timemodified', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_timecreated', $table, 'timecreated');
        $this->createIndex('idx_timemodified', $table, 'timemodified');
    }
}