<?php

class m140609_193600_addTopModelFields extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $table  = "{{extra_fields}}";
        $fields = array(
            array(
                'name'        => 'tm_tatoo',
                'type'        => 'textarea',
                'label'       => 'Есть ли у вас татуировки или пирсинг?',
                'description' => '(перечислить)',
            ),
            array(
                'name'        => 'tm_currentcity',
                'type'        => 'textarea',
                'label'       => 'С кем и в каком городе вы проживаете сейчас?',
                'description' => '',
            ),
        );
        foreach ( $fields as $field )
        {
            $this->insert($table, $field);
        }
    }
} 