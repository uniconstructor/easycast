<?php

class m141020_210800_installCustomNotifications extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // список стандартных блоков для оповещений
        $mailBlocksList = array(
            'name'          => 'Стандартные блоки для составления оповещений',
            'description'   => 'Их можно добавлять и удалять, но их содержимое берется из описания '.
                'проекта мероприятия и роли, поэтому для того чтобы отредактировать текст этих '.
                'блоков нужно править описание проекта или мероприятия. Не трогайте значения в фигурных '.
                'скобках - это может привести к повреждению текста.',
            'triggerupdate' => 'manual',
            'unique'        => 1,
        );
        // сами блоки письма
        $mailBlocks = array(
            array(
                'name'        => 'Приветствие',
                'value'       => '{$userGreeting}',
                'description' => 'Стандартное приветствие. Если имя участника неизвестно - текст будет заменен на безличное обращение',
            ),
            array(
                'name'        => 'О проекте',
                'value'       => '{$projectDescription}',
                'description' => 'Блок "О проекте". Будет скопировано описание из проекта в котором находится роль',
            ),
            array(
                'name'        => 'Где и когда',
                'value'       => '{$eventDescription}',
                'description' => 'По умолчанию заголовком этого блока будет название мероприятия в котором содержится роль',
            ),
            array(
                'name'        => 'Список ролей',
                'value'       => '{$vacancies}',
                'description' => 'Список ролей. Будут перечислены все роли которые подходят участнику. '.
                    'Будут перечислены все роли события если письмо отправляется участнику не содержащемуся в базе.',
            ),
            array(
                'name'        => 'Кнопка подачи заявки',
                'value'       => '{$button}',
                'description' => 'Кнопка подачи заявки. Отдельный блок с кнопкой во всю ширину письма',
            ),
        );
        // сохраняем список блоков и заполняем его значениями
        $mailBlocksList['id'] = $this->createList($mailBlocksList, 'mailBlocksList', $mailBlocks);
        
        // список выбранных по умолчанию блоков письма с приглашением
        $selectedInviteBlocksList = array(
            'name'          => 'Стандартный состав письма с приглашением',
            'description'   => 'Из этих блоков будут состоять все приглашения отправляемые участникам',
            'triggerupdate' => 'manual',
            'unique'        => 0,
        );
        // извлекаем сохраненные элементы списка чтобы обратиться к ним еще раз для создания ссылок
        $mailBlockRecords = $this->loadListItems($mailBlocksList['id']);
        // выбранные элементы хранятся как ссылки на элементы первого списка
        $selectedMailBlocks = array();
        foreach ( $mailBlockRecords as $mailBlock )
        {
            $selectedMailBlocks[] = array(
                'name'        => $mailBlock['name'],
                'description' => $mailBlock['description'],
                'objecttype'  => 'EasyListItem',
                'objectid'    => $mailBlock['id'],
                'objectfield' => 'value',
            );
        }
        // сохраняем список блоков и заполняем его значениями
        $selectedInviteBlocksList['id'] = $this->createList($selectedInviteBlocksList, 
            'inviteBlocksList', $selectedMailBlocks);
        
        // создаем список для введенных участником значений
        $userInviteBlocksList = array(
            'name'          => 'Список вручную созданных блоков письма',
            'description'   => 'Текст этих блоков можно свободно править, но их содержимое будет '.
                'одинаковым для всех получателей.',
            'triggerupdate' => 'manual',
            'unique'        => 0,
        );
        $userInviteBlocksList['id'] = $this->createList($userInviteBlocksList, 'userBlocks');
        
        // оповещение о приглашении на съемку
        $configNotifications = array(
            'name'         => 'inviteNotificationList',
            'title'        => 'Состав письма с приглашением',
            'description'  => 'Список блоков из которых состоит письмо, отправляемое всем подходящим '.
                'участникам при запуске проекта',
            'type'         => 'multiselect',
            'minvalues'    => 1,
            'maxvalues'    => 0,
            'objecttype'   => 'EventVacancy',
            'objectid'     => 0,
            // список стандартных блоков письма
            'easylistid'   => $mailBlocksList['id'],
            'valuetype'    => 'EasyList',
            // выбранные по умолчанию блоки
            'valueid'      => $selectedInviteBlocksList['id'],
            'userlistid'   => $userInviteBlocksList['id'],
        );
        // привязываем настройку к каждой модели класса
        $this->createRootConfig($configNotifications, "{{event_vacancies}}");
        
        // добавляем настройку "баннер проекта для письма" к роли
        $configBanner = array(
            'name'         => 'inviteBannerUrl',
            'title'        => 'Баннер меньшего размера для вставки в письмо с приглашением',
            'description'  => 'Рекомендуемая ширина 600px',
            'type'         => 'file',
            'minvalues'    => 0,
            'maxvalues'    => 1,
            'objecttype'   => 'EventVacancy',
            'objectid'     => 0,
            'easylistid'   => 0,
            'valuetype'    => 'ExternalFile',
            'valuefield'   => 'url',
            'valueid'      => 0,
        );
        // привязываем настройку к каждой модели класса
        $this->createRootConfig($configBanner, "{{event_vacancies}}");
    }
}