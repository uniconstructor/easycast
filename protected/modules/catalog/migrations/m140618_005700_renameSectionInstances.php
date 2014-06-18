<?php

class m140618_005700_renameSectionInstances extends CDbMigration
{
    public function up()
    {
        $table = "{{section_instances}}";
        $this->alterColumn($table, 'newname', 'varchar(255) DEFAULT NULL');
        
        $this->renameTable($table, "{{catalog_section_instances}}");
    }
}