<?php

class m141021_081300_addVacancyGreeting extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $defaultListData = array(
            'name'          => 'Список модели по умолчанию',
            'description'   => 'Нужнен чтобы не плодить списки для тех настроек, которые 
                не имеют стандартных значений, но данные все равно хранят в EasyListItem',
            'triggerupdate' => 'manual',
            'unique'        => 0,
        );
        $defaultList['id'] = $this->createList($defaultListData);
        
        // особое описание для страницы динамической формы с баннером
        $defaultVacancyListId = array(
            'name'         => Yii::app()->params['defaultListConfig'],
            'title'        => 'Список по умолчанию для одиночных значений настроек этой модели',
            'description'  => 'Эта настройка нужна чтобы не плодить списки для тех настроек, 
                которые не имеют стандартных значений. Не изменяйте ее.',
            'type'         => 'text',
            'minvalues'    => 0,
            'maxvalues'    => 0,
            'objecttype'   => 'EventVacancy',
            'objectid'     => 0,
            'easylistid'   => 0,
            'valuetype'    => 'EasyListItem',
            'valuefield'   => 'id',
            'valueid'      => $defaultList['id'],
        );
        // привязываем настройку к каждой модели класса
        $this->createRootConfig($defaultVacancyListId, "{{event_vacancies}}");
        
        // особое описание для страницы динамической формы с баннером
        $configGreeting = array(
            'name'         => 'customGreeting',
            'title'        => 'Собственное пояснение перед формой регистрации',
            'description'  => 'Отображается вместо описания роли',
            'type'         => 'redactor',
            'minvalues'    => 0,
            'maxvalues'    => 1,
            'objecttype'   => 'EventVacancy',
            'objectid'     => 0,
            'easylistid'   => $defaultList['id'],
            'valuetype'    => 'EasyListItem',
            'valuefield'   => 'value',
            'valueid'      => $this->createDataItem('EventVacancy'),
        );
        // привязываем настройку к каждой модели класса
        $this->createRootConfig($configGreeting, "{{event_vacancies}}");
        
        $configCountdown = array(
            'name'         => 'countdownTime',
            'title'        => 'Собственное пояснение перед формой регистрации',
            'description'  => 'Отображается вместо описания роли',
            'type'         => 'datetime',
            'minvalues'    => 0,
            'maxvalues'    => 1,
            'objecttype'   => 'EventVacancy',
            'objectid'     => 0,
            'easylistid'   => $defaultList['id'],
            'valuetype'    => 'EasyListItem',
            'valuefield'   => 'value',
            'valueid'      => $this->createDataItem('EventVacancy', 0),
        );
        // привязываем настройку к каждой модели класса
        $this->createRootConfig($configCountdown, "{{event_vacancies}}");
    }
}