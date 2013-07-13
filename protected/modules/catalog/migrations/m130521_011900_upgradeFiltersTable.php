<?php

class m130521_011900_upgradeFiltersTable extends CDbMigration
{
    protected $MySqlOptions = "ENGINE=InnoDB CHARSET=utf8";
    
    public function safeUp()
    {
        $table = "{{catalog_filters}}";
        $this->renameColumn($table, 'name', 'shortname');
        
        $this->dropColumn($table, 'field');
        $this->dropColumn($table, 'lang');
    }
}