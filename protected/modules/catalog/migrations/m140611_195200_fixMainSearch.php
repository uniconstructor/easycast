<?php

class m140611_195200_fixMainSearch extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // скрываем фильтр по email во всех разделах
        $instancesTable = "{{catalog_filter_instances}}";
        $this->update($instancesTable, array('visible' => 0), "`filterid` = 36");
        
        // устанавливаем новый фильтр "статус анкеты"
        $filtersTable = "{{catalog_filters}}";
        $this->insert($filtersTable, array(
            'shortname'    => 'status',
            'widgetclass'  => 'QSearchFilterStatus',
            'handlerclass' => 'QSearchHandlerStatus',
            'name'         => 'Статус анкеты',
        ));
        $this->insert($filtersTable, array(
            'shortname'    => 'addchar',
            'widgetclass'  => 'QSearchFilterAddChar',
            'handlerclass' => 'QSearchHandlerAddChar',
            'name'         => 'Особенности внешности',
        ));
        
        $this->update($filtersTable , array('name' => 'Дополнительные фильтры'), "`shortname` = 'system'");
    }
}