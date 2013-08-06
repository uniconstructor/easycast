<?php

class m130804_113100_addVacancySalary extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{event_vacancies}}';
        
        $this->alterColumn($table, 'autoconfirm', "tinyint(1) UNSIGNED NOT NULL DEFAULT 0");
        
        $this->addColumn($table, 'salary', "int(11) UNSIGNED NOT NULL DEFAULT 0");
        $this->createIndex('idx_salary', $table, 'salary');
    }
}