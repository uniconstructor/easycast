<?php

class m130731_070000_installSectionsFilter extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{catalog_filters}}";
        
        $this->insert($table, array(
            'shortname'    => 'sections',
            'widgetclass'  => 'QSearchFilterSections',
            'handlerclass' => 'QSearchHandlerSections',
            'name'         => 'Разделы каталога',
        ));
    }
}