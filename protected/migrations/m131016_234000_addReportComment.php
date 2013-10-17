<?php

class m131016_234000_addReportComment extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{reports}}";
        
        $this->addColumn($table, 'comment', "VARCHAR(4095) NOT NULL DEFAULT ''");
        $this->createIndex('idx_comment', $table, 'comment');
    }
}