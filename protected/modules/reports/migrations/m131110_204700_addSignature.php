<?php

class m131110_204700_addSignature extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = '{{reports}}';
        
        $this->addColumn($table, 'key', "VARCHAR(128) NOT NULL DEFAULT ''");
        $this->createIndex('idx_key', $table, 'key');
    }
}