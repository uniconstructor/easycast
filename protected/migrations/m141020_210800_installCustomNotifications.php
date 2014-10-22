<?php

class m141020_210800_installCustomNotifications extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // создаем список для стандартных оповещений системы
        $messageList = array(
            'name'          => 'Список для стандартных оповещений системы',
            'description'   => 'Содержит все стандартные оповещения. На элементы из этого списка 
                ссылаются все настройки системы, связанные со стандартными оповещениями.',
            'triggerupdate' => 'manual',
            'unique'        => 1,
        );
        $messageList['id'] = $this->createListItem($messageList);
        // сохраняем полный список оповещений системы
        $this->createNotificationsList($messageList);
        
        $configMessagesList = array(
            'name'         => 'defaultSystemMessages',
            'title'        => 'Полный список стандартных оповещений системы',
            'description'  => 'Содержит все стандартные оповещения. На элементы из этого списка ссылаются
                все настройки системы, связанные со стандартными оповещениями',
            'type'         => 'multiselect',
            'minvalues'    => 0,
            'maxvalues'    => 0,
            'objecttype'   => 'system',
            'objectid'     => 0,
            'easylistid'   => $messageList['id'],
            'valuetype'    => 'EasyList',
            'valuefield'   => null,
            'valueid'      => $messageList['id'],
        );
        $configMessagesList['id'] = $this->createConfig($configMessagesList);
        
        // текст оповещениея о приглашении на съемку
        $configInviteText = array(
            'name'         => 'newInviteMailText',
            'title'        => 'Текст письма с приглашением',
            'description'  => 'Текст который будет отправлен участникам при запуске проекта',
            'type'         => 'redactor',
            'minvalues'    => 1,
            'maxvalues'    => 1,
            'objecttype'   => 'ProjectEvent',
            'objectid'     => 0,
            'easylistid'   => 0,
            'valuetype'    => 'EasyListItem',
            'valueid'      => $this->createDataItem('EventVacancy'),
            'userlistid'   => 0,
        );
        // привязываем настройку к каждой модели класса
        $this->createRootConfig($$configInviteText, "{{project_events}}");
        
        // добавляем настройку "баннер проекта для письма" к роли
        $configBanner = array(
            'name'         => 'emailBannerUrl',
            'title'        => 'Баннер меньшего размера для вставки в письмо',
            'description'  => 'Рекомендуемая ширина 600px',
            'type'         => 'file',
            'minvalues'    => 0,
            'maxvalues'    => 1,
            'objecttype'   => 'ProjectEvent',
            'objectid'     => 0,
            'easylistid'   => 0,
            'valuetype'    => 'ExternalFile',
            'valuefield'   => 'url',
            'valueid'      => 0,
        );
        // привязываем настройку к каждой модели класса
        $this->createRootConfig($configBanner, "{{project_events}}");
    }
    
    /**
     * 
     * @param array $messageList
     * @return void
     */
    protected function createNotificationsList($messageList)
    {
        $notifications = array(
            array(
                'name'        => 'Приглашение при запуске проекта',
                'value'       => 'newInvite',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $messageList['id'],
            ),
            array(
                'name'        => 'Письмо с подтверждением заявки',
                'value'       => 'approveMember',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $messageList['id'],
            ),
            array(
                'name'        => 'Письмо с отклонением заявки',
                'value'       => 'rejectMember',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $messageList['id'],
            ),
            array(
                'name'        => 'Письмо с предварительным одобрением заявки',
                'value'       => 'pendingMember',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $messageList['id'],
            ),
            array(
                'name'        => 'Приглашение на отбор актеров (для заказчика)',
                'value'       => 'customerInvite',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $messageList['id'],
            ),
            array(
                'name'        => 'Приглашение активировать анкету для участников из нашей базы, при создании анкеты админом',
                'value'       => 'ECRegistration',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $messageList['id'],
            ),
            array(
                'name'        => 'Вызывной лист',
                'value'       => 'callList',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $messageList['id'],
            ),
            array(
                'name'        => 'Кастинг-лист',
                'value'       => 'castingList',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $messageList['id'],
            ),
            array(
                'name'        => 'Коммерческое предложение',
                'value'       => 'offer',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $messageList['id'],
            ),
            array(
                'name'        => 'Заказ, онлайн-кастинг или расчет стоимости',
                'value'       => 'newOrder',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $messageList['id'],
            ),
        );
        foreach ( $notifications as $notification )
        {
            $this->createListItem($notification);
        }
    }
}