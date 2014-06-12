<?php

class m140612_101900_addTopModelFields extends CDbMigration
{
    public function safeUp()
    {
        $table  = "{{extra_fields}}";
        $fields = array(
            array(
                'name'        => 'tm_use4u',
                'type'        => 'checkbox',
                'label'       => 'Использовать данные для регистрации на сайте u-tv.ru',
                'description' => '',
            ),
            array(
                'name'        => 'tm_news4u',
                'type'        => 'checkbox',
                'label'       => 'Получать новости от телеканала u-tv.ru',
                'description' => '',
            ),
            array(
                'name'        => 'tm_policyagreed',
                'type'        => 'checkbox',
                'label'       => 'Разрешить автоматическую обработку моих данных при проведении кастинга
                    в соответствии с <a href="http://easycast.ru/site/page/view/license">пользовательским соглашением</a>.',
                'description' => '',
            ),
        );
        foreach ( $fields as $field )
        {
            $this->insert($table, $field);
        }
        
        $table = "{{extra_field_instances}}";
        $this->addColumn($table, 'default', 'varchar(255) DEFAULT NULL');
        $this->createIndex('idx_default', $table, 'default');
    }
}