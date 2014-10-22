<?php

class m141021_081300_addVacancyGreeting extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $defaultListData = array(
            'name'          => 'Список для значений модели по умолчанию',
            'description'   => 'Нужнен чтобы не плодить списки для тех настроек, которые 
                не имеют стандартных значений, но данные все равно хранят в EasyListItem',
            'triggerupdate' => 'manual',
            'unique'        => 0,
        );
        $defaultList['id'] = $this->createList($defaultListData);
        
        $defaultVacancyListId = array(
            'name'         => Yii::app()->params['defaultListConfig'],
            'title'        => 'Список по умолчанию для одиночных значений настроек этой модели',
            'description'  => 'Эта настройка нужна чтобы не плодить списки для тех настроек, 
                которые не имеют стандартных значений. Не изменяйте ее.',
            'type'         => 'text',
            'minvalues'    => 0,
            'maxvalues'    => 1,
            'objecttype'   => 'EventVacancy',
            'objectid'     => 0,
            'easylistid'   => 0,
            'valuetype'    => 'EasyList',
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
        
        // обратный отсчет до окончания приема заявок
        $configCountdown = array(
            'name'         => 'countdownTime',
            'title'        => 'Обратный отсчет до окончания приема заявок',
            'description'  => 'Отображается на странице динамической формы',
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
        
        
        //////////////////////////////////////////////////////////////////
        // id списка моделей роли
        $defaultEventList = array(
            'name'          => 'Список для значений модели по умолчанию',
            'description'   => 'Нужнен чтобы не плодить списки для тех настроек, которые
                не имеют стандартных значений, но данные все равно хранят в EasyListItem',
            'triggerupdate' => 'manual',
            'unique'        => 0,
        );
        $defaultEventList['id'] = $this->createList($defaultEventList);
        
        // Настройка с id списка моделей роли
        $defaultEventListId = array(
            'name'         => Yii::app()->params['defaultListConfig'],
            'title'        => 'Список по умолчанию для одиночных значений настроек этой модели',
            'description'  => 'Эта настройка нужна чтобы не плодить списки для тех настроек,
                которые не имеют стандартных значений. Не изменяйте ее.',
            'type'         => 'text',
            'minvalues'    => 0,
            'maxvalues'    => 1,
            'objecttype'   => 'ProjectEvent',
            'objectid'     => 0,
            'easylistid'   => 0,
            'valuetype'    => 'EasyList',
            'valuefield'   => 'id',
            'valueid'      => $defaultEventList['id'],
        );
        // привязываем настройку к каждой модели класса
        $this->createRootConfig($defaultEventListId, "{{project_events}}");
        
        // текст оповещениея о приглашении на съемку
        $configInviteText = array(
            'name'         => 'newInviteMailText',
            'title'        => 'Текст письма с приглашением',
            'description'  => 'Текст который будет отправлен участникам при запуске проекта',
            'type'         => 'redactor',
            'minvalues'    => 0,
            'maxvalues'    => 1,
            'objecttype'   => 'ProjectEvent',
            'objectid'     => 0,
            'easylistid'   => 0,
            'valuetype'    => 'EasyListItem',
            'valueid'      => $this->createDataItem('ProjectEvent', 0),
            'userlistid'   => $defaultEventList['id'],
        );
        // привязываем настройку к каждой модели класса
        $this->createRootConfig($configInviteText, "{{project_events}}");
    }
}