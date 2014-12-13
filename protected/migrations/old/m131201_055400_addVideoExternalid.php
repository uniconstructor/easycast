<?php


class m131201_055400_addVideoExternalid extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{video}}";
        
        $this->addColumn($table, 'externalid', "varchar(255) NOT NULL DEFAULT ''");
        $this->createIndex('idx_externalid', $table, 'externalid');
    }
}