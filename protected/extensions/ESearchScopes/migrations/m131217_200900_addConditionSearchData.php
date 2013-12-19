<?php

/**
 * 
 */
class m131217_200900_addConditionSearchData extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{scope_conditions}}";
        
        $this->addColumn($table, 'searchdata', "VARCHAR(4095) NOT NULL DEFAULT ''");
    }
}