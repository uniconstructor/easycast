<?php

class m130821_095300_installSalaryFilter extends CDbMigration
{
    /**
     * (non-PHPdoc)
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table = "{{catalog_filters}}";
    
        $this->insert($table, array(
            'shortname'    => 'salary',
            'widgetclass'  => 'QSearchFilterSalary',
            'handlerclass' => 'QSearchHandlerSalary',
            'name'         => 'Оплата за день',
        ));
    }
}