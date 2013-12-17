<?php

class m131217_135400_hideUnusedSections extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{catalog_sections}}";
        // скрываем неиспользуемые разделы
        $this->update($table, array('visible' => 0), 
            "`shortname` IN ('student_actors', 'singers', 'nopro_actors')");
        
        $table = "{{catalog_filter_instances}}";
        // скрываем фильтр поиска "категории" для поиска по всей базе
        $this->update($table, array('visible' => 0), 
            "`linktype` = 'section' AND `linkid` = '1' AND `filterid` = '32'");
    }
}