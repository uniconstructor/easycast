<?php

class m140526_084400_addEmailFilter extends CDbMigration
{
    public function safeUp()
    {
        $table = "{{catalog_filters}}";
        
        $this->insert($table, array(
            'shortname'    => 'email',
            'widgetclass'  => 'QSearchFilterEmail',
            'handlerclass' => 'QSearchHandlerEmail',
            'name'         => 'email',
        ));
    }
}