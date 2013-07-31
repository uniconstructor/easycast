<?php

class m130731_200000_updateProjectMemberStatus extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{project_members}}';
        
        $this->alterColumn($table, 'status', "varchar(20) NOT NULL DEFAULT 'draft'");
        $this->dropIndex('idx_status', $table);
        $this->createIndex('idx_status', $table, 'status');
    }
}