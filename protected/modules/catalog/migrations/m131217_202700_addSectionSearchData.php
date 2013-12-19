<?php

class m131217_202700_addSectionSearchData extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{catalog_sections}}";
        
        $this->addColumn($table, 'searchdata', "VARCHAR(4095) NOT NULL DEFAULT ''");
    }
}