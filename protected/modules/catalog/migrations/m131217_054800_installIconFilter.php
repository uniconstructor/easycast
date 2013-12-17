<?php

class m131217_054800_installIconFilter extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{catalog_filters}}";
        
        $this->insert($table, array(
            'shortname'    => 'iconlist',
            'widgetclass'  => 'QSearchFilterIconList',
            'handlerclass' => 'QSearchHandlerSections',
            'name'         => 'Разделы каталога (иконки)',
        ));
    }
}