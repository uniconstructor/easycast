<?php

class m140818_090600_addWizartStartStatus extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{wizard_steps}}";
        $this->addColumn($table, 'startstatus', "VARCHAR(50) NOT NULL DEFAULT 'none'");
        $this->createIndex('idx_startstatus', $table, 'startstatus');
        unset($table);
        
        $table = "{{wizard_step_instances}}";
        $this->addColumn($table, 'startstatus', "VARCHAR(50) NOT NULL DEFAULT 'none'");
        $this->createIndex('idx_startstatus', $table, 'startstatus');
    }
}