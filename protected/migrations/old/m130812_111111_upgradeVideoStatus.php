<?php

class m130812_111111_upgradeVideoStatus extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{video}}';
        
        $this->alterColumn($table, 'status', "VARCHAR(50) NOT NULL DEFAULT 'pending'");
        $this->dropIndex('idx_status', $table);
        $this->createIndex('idx_status', $table, 'status');
        
        $this->addColumn($table, 'timemodified', 'int(11) UNSIGNED DEFAULT 0');
        $this->createIndex('idx_timemodified', $table, 'timemodified');
        
        $this->alterColumn($table, 'type', "VARCHAR(20) NOT NULL DEFAULT 'link'");
        
        $this->update($table, array('type' => 'link'), "(`type` = '') OR (`type` IS NULL)");
        $this->update($table, array('status' => 'pending'), "(`status` = '') OR (`status` IS NULL)");
    }
}