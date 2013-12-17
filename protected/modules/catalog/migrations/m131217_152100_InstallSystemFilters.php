<?php

class m131217_152100_InstallSystemFilters extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{catalog_filters}}";
        
        $this->insert($table, array(
            'shortname'    => 'system',
            'widgetclass'  => 'QSearchFilterSystem',
            'handlerclass' => 'QSearchHandlerSystem',
            'name'         => 'СЛУЖЕБНЫЕ ФИЛЬТРЫ',
        ));
    }
}