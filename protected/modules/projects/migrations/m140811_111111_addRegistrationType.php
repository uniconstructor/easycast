<?php

class m140811_111111_addRegistrationType extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{event_vacancies}}";
        $this->addColumn($table, 'regtype', "VARCHAR(50) NOT NULL DEFAULT 'form'");
        $this->createIndex('idx_regtype', $table, 'regtype');
    }
}