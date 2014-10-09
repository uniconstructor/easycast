<?php

class m140908_030900_attachConfigData extends CDbMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        // создаем список для типов проектов
        $this->insert("{{easy_lists}}", array(
            'name'          => 'Все типы проекта',
            'description'   => 'Все проекты на сайте используют этот список для своего типа',
            'triggerupdate' => 'manual',
        ));
        // запоминаем id списка с типами - он дальше везде понадобится
        $listId = $this->dbConnection->lastInsertID;
        
        // добавляем в список все существующие типы проектов
        $sortOrder    = 1;
        $projectTypes = array(
            array(
                'easylistid'  => $listId,
                'name'        => 'Фотореклама',
                'value'       => 'ad',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Видеореклама',
                'value'       => 'videoad',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Художественный фильм',
                'value'       => 'film',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Документальный фильм',
                'value'       => 'documentary',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Сериал',
                'value'       => 'series',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Телепроект',
                'value'       => 'tvshow',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Показ',
                'value'       => 'expo',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Промо-акция',
                'value'       => 'promo',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Флешмоб',
                'value'       => 'flashmob',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Видеоклип',
                'value'       => 'video',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Видеоролик',
                'value'       => 'videoclip',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Реалити-шоу',
                'value'       => 'realityshow',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Докуреалити',
                'value'       => 'docureality',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Короткометражный фильм',
                'value'       => 'shortfilm',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Конференция',
                'value'       => 'conference',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Концерт',
                'value'       => 'concert',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Театральная постановка',
                'value'       => 'theatreperfomance',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Мюзикл',
                'value'       => 'musical',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Корпоратив',
                'value'       => 'corporate',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Фестиваль',
                'value'       => 'festival',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $listId,
                'name'        => 'Онлайн-кастинг',
                'value'       => 'onlinecasting',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
        );
        // запоминаем какие id каким типам проекта соответствуют 
        $itemIds = array();
        foreach ( $projectTypes as $projectType )
        {
            $this->insert("{{easy_list_items}}", $projectType);
            $itemIds[$projectType['value']] = $this->dbConnection->lastInsertID;
        }
        unset($sortOrder);
        
        
        // cоздаем список для значений по умолчанию в настройке "оповещения при изменении даты/времени события"
        $this->insert("{{easy_lists}}", array(
            'name'          => 'Условия SMS-оповещения участника при отмене/переносе события',
            'description'   => 'Список вариантов для SMS-оповещения участника о срочных изменениях в съемке: изменение времени, места, условий или отмене события',
            'triggerupdate' => 'manual',
        ));
        // запоминаем id списка вариантов оповещения
        $smsNotificationListId = $this->dbConnection->lastInsertID;
        // добавляем все возможные варианты для настройки оповещения по SMS
        $sortOrder           = 1;
        $smsNotificatonTypes = array(
            array(
                'easylistid'  => $smsNotificationListId,
                'name'        => 'Отключить',
                'value'       => 'no',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $smsNotificationListId,
                'name'        => 'Только для событий в которых моя заявка ожидает расмотрения',
                'value'       => 'yes',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $smsNotificationListId,
                'name'        => 'Только если я подал заявку и она прошла предварительный отбор',
                'value'       => 'from_pending',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $smsNotificationListId,
                'name'        => 'Только если моя заявка прошла окончательный отбор',
                'value'       => 'from_active',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
        );
        // запоминаем какие id каким вариантам соответствуют
        $smsNotificationItemIds = array();
        foreach ( $smsNotificatonTypes as $notificationType )
        {
            $this->insert("{{easy_list_items}}", $notificationType);
            $smsNotificationItemIds[$notificationType['value']] = $this->dbConnection->lastInsertID;
        }
        unset($sortOrder);
        
        // создаем список с интервалами времени (15 минут)
        $this->insert("{{easy_lists}}", array(
            'name'          => 'Список интервалов времени, разбитый по 15 минут',
            'description'   => 'Используется в настройках, связанных со временем суток',
            'triggerupdate' => 'never',
        ));
        $timeList15Id = $this->dbConnection->lastInsertID;
        // создаем список с интервалами времени (30 минут)
        $this->insert("{{easy_lists}}", array(
            'name'          => 'Список интервалов времени, разбитый по 30 минут',
            'description'   => 'Используется в настройках, связанных со временем суток',
            'triggerupdate' => 'never',
        ));
        $timeList30Id = $this->dbConnection->lastInsertID;
        // создаем список с интервалами времени (1 час)
        $this->insert("{{easy_lists}}", array(
            'name'          => 'Список интервалов времени, разбитый по 60 минут',
            'description'   => 'Используется в настройках, связанных со временем суток',
            'triggerupdate' => 'never',
        ));
        $timeList60Id = $this->dbConnection->lastInsertID;
        
        // создаем массив для каждого интервала
        $timeList15 = array();
        $seconds    = 0;
        // создаем итервалы времени в формате время/секунды
        $timezoneSettingBackup = date_default_timezone_get();
        date_default_timezone_set("GMT");
        for ( $i = 0; $i < 96; $i++ )
        {
            $item = array(
                'name'        => date('H:i', $seconds),
                'value'       => $seconds,
                'timecreated' => time(),
            );
            $timeList15[] = $item;
            // +15 min
            $seconds += 900;
        }
        
        // заполняем каждый список собственными интервалами времени
        $count15 = 0;
        $count30 = 0;
        $count60 = 0;
        foreach ( $timeList15 as $timeItem )
        {
            // привязываем элемент к списку
            $timeItem15 = array(
                'easylistid'  => $timeList15Id,
                'sortorder'   => $count15++,
            );
            $timeItem15 = CMap::mergeArray($timeItem, $timeItem15);
            // сохраняем элемент списка
            $this->insert("{{easy_list_items}}", $timeItem15);
            $timeList15Ids[$timeItem15['name']] = $this->dbConnection->lastInsertID;
            
            switch ( date('i', $timeItem['value']) )
            {// определяем списки каких временных интервалов заполнять
                case '30':
                    // привязываем элемент к списку
                    $timeItem30 = array(
                        'easylistid'  => $timeList30Id,
                        'sortorder'   => $count30++,
                    );
                    $timeItem30 = CMap::mergeArray($timeItem, $timeItem30);
                    // сохраняем элемент списка
                    $this->insert("{{easy_list_items}}", $timeItem30);
                break;
                case '00':
                    // привязываем элемент к списку
                    $timeItem30 = array(
                        'easylistid'  => $timeList30Id,
                        'sortorder'   => $count30++,
                    );
                    $timeItem30 = CMap::mergeArray($timeItem, $timeItem30);
                    // сохраняем элемент списка
                    $this->insert("{{easy_list_items}}", $timeItem30);
                    
                    // привязываем элемент к списку
                    $timeItem60 = array(
                        'easylistid'  => $timeList60Id,
                        'sortorder'   => $count60++,
                    );
                    $timeItem60 = CMap::mergeArray($timeItem, $timeItem60);
                    // сохраняем элемент списка
                    $this->insert("{{easy_list_items}}", $timeItem60);
                break;
            }
        }
        // возвращаем временную зону обратно
        date_default_timezone_set($timezoneSettingBackup);
        
        
        // cоздаем список для вариантов "скрыть/показать мою анкету в поиске"
        $this->insert("{{easy_lists}}", array(
            'name'          => 'Список "скрыть/показать"',
            'description'   => 'Список с двумя пунктами',
            'triggerupdate' => 'manual',
        ));
        // запоминаем id списка вариантов оповещения
        $qVisibleOptionsListId = $this->dbConnection->lastInsertID;
        // добавляем все возможные варианты для настройки оповещения по SMS
        $sortOrder           = 1;
        $qVisibleOptionTypes = array(
            array(
                'easylistid'  => $qVisibleOptionsListId,
                'name'        => 'Скрыть',
                'value'       => 'hide',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
            array(
                'easylistid'  => $qVisibleOptionsListId,
                'name'        => 'Показать',
                'value'       => 'show',
                'sortorder'   => $sortOrder++,
                'timecreated' => time(),
            ),
        );
        // запоминаем какие id каким вариантам соответствуют
        $qVisibleOptionTypeIds = array();
        foreach ( $qVisibleOptionTypes as $qVisibleOptionType )
        {
            $this->insert("{{easy_list_items}}", $qVisibleOptionType);
            $qVisibleOptionTypeIds[$qVisibleOptionType['value']] = $this->dbConnection->lastInsertID;
        }
        unset($sortOrder);
        
        
        // создаем системную настройку в которой хранится id списка с типами проекта
        // мы будем обращаться к ней каждый раз когда нам нужен будет список типов проекта
        $this->insert("{{config}}", array(
            'name'         => 'typeListId',
            'title'        => 'id списка для типов проекта',
            'title'        => 'Не меняйте эту настройку. Она установлена автоматически.',
            'type'         => 'text',
            'minvalues'    => 1,
            'maxvalues'    => 1,
            'objecttype'   => 'Project',
            'objectid'     => 0,
            'timecreated'  => time(),
            'timemodified' => time(),
            // добавляем значение, которое хранит id списка
            'valuetype'    => 'EasyList',
            'valuefield'   => 'id',
            'valueid'      => $listId,
        ));
        $typesListConfigId = $this->dbConnection->lastInsertID;
        
        // создаем системную настройку, которая будет прикреплена к каждому проекту 
        // и будет хранить его тип
        // значение по умолчанию для типа проекта не задаем потому что 
        // его невозможно определить автоматически.
        // Оно будет иметь тип option (пункт списка)
        $projectTypeConfig = array(
            'name'         => 'type',
            'title'        => 'Тип проекта',
            'type'         => 'select',
            'minvalues'    => 1,
            'maxvalues'    => 1,
            'objecttype'   => 'Project',
            'objectid'     => 0,
            'timecreated'  => time(),
            'timemodified' => time(),
            // список типов общий для всех проектов
            'easylistid'   => $listId,
        );
        $this->insert("{{config}}", $projectTypeConfig);
        $projectTypeConfigId = $this->dbConnection->lastInsertID;
        
        //////////////////////////////////////////////////////////////////////////
        // обновляем все старые проекты, прикрепляя к каждому из них по настройке
        $projects = $this->dbConnection->createCommand()->select('id,type')->
            from('{{projects}}')->queryAll();
        foreach ( $projects as $project )
        {// создаем и привязываем настройку к каждому проекту
            // определяем id типа проекта
            $itemId = $itemIds[$project['type']];
            // привязываем тип проета к самому проекту
            $projectData = array(
                'objecttype' => 'Project',
                'objectid'   => $project['id'],
                'parentid'   => $projectTypeConfigId,
                // задаем значение для типа проекта
                'valuetype'  => 'EasyListItem',
                'valuefield' => 'value',
                'valueid'    => $itemId,
            );
            $projectSetting = CMap::mergeArray($projectTypeConfig, $projectData);
            // сохраняем настройку и ее значение (тип проекта)
            $this->insert("{{config}}", $projectSetting);
            // запоминаем id настройки для этого проекта
            $settingId = $this->dbConnection->lastInsertID;
        }
        
        ////////////////////////////////////////////////////////////////////////////
        // обновляем старые анкеты, прикрепляя к каждой из них настройки оповещений
        
        // создаем настройку анкеты "типы проектов на которые вы хотите получать приглашения"
        $preferredProjectTypesConfig = array(
            'name'         => 'preferedProjectTypes',
            'title'        => 'Типы проектов для которых вы получаете приглашения',
            'description'  => 'Укажите типы проектов в которых вы хотели бы участвовать.',
            'type'         => 'multiselect',
            // как минимум один вариант должен быть выбран
            'minvalues'    => 1,
            // разрешаем пользователю выбрать любое количество типов проекта для участия 
            'maxvalues'    => 0,
            'objecttype'   => 'Questionary',
            'objectid'     => 0,
            'timecreated'  => time(),
            'timemodified' => time(),
            // список типов общий еще и для настроек участника
            'easylistid'   => $listId,
            // по умолчанию выбраны все типы, поэтому список выбранных вариантов 
            // совпадает со списком стандартных
            // задаем список значений как ссылку на связь (relation) в модели
            'valuetype'    => 'EasyList',
            'valuefield'   => 'listItems',
            'valueid'      => $itemId,
        );
        $this->insert("{{config}}", $preferredProjectTypesConfig);
        $preferredProjectTypesConfigId = $this->dbConnection->lastInsertID;
        // приглашения будут присылаться на все типы проектов,
        // участник может отключить лишние если захочет
        // Также этот выбор будет предлагаться ему каждый раз при открытии приглашения,
        // поэтому изначально просто добавляем все типы проектов в список разрешенных
        
        
        // создаем настройку анкеты "Присылать SMS не ранее"
        $smsMinTimeConfig = array(
            'name'         => 'holdSmsBefore',
            'title'        => 'Получать sms-обовещения начиная с',
            'description'  => 'Эта настройка действует для всех sms отправляемых нашим сайтом.
                Все SMS-оповещения будут доставлены вам не раньше этого времени.
                Ваш часовой пояс определяется по городу, который вы указали в анкете.
                Если город не указан - то отправка будет происходить по московскому времени (GMT+3).',
            'type'         => 'time',
            'minvalues'    => 1,
            'maxvalues'    => 1,
            'objecttype'   => 'Questionary',
            'objectid'     => 0,
            'timecreated'  => time(),
            'timemodified' => time(),
            // список временных интервалов
            'easylistid'   => $timeList15Id,
            // по умолчанию c 10:00
            'valuetype'    => 'EasyList',
            'valuefield'   => 'value',
            'valueid'      => $timeList15Ids['10:00'],
        );
        $this->insert("{{config}}", $smsMinTimeConfig);
        $smsMinTimeConfigId = $this->dbConnection->lastInsertID;
        
        // создаем настройку анкеты "Присылать SMS не позднее"
        $smsMaxTimeConfig = array(
            'name'         => 'holdSmsAfter',
            'title'        => 'Присылать SMS не позднее',
            'description'  => 'Эта настройка действует для всех sms отправляемых нашим сайтом.
                Все sms-оповещения будут отправлены вам не позже этого времени.
                Ваш часовой пояс определяется по городу, который вы указали в анкете.
                Если город не указан - то отправка будет происходить по московскому времени (GMT+3).
                Все sms-сообщения которые высылаются позднее указанного вами времени 
                будут ждать отправки до следующего дня.',
            'type'         => 'select',
            'minvalues'    => 1,
            'maxvalues'    => 1,
            'objecttype'   => 'Questionary',
            'objectid'     => 0,
            'timecreated'  => time(),
            'timemodified' => time(),
            // список временных интервалов
            'easylistid'   => $timeList15Id,
            // по умолчанию до 23:00
            'valuetype'    => 'EasyList',
            'valuefield'   => 'value',
            'valueid'      => $timeList15Ids['23:00'],
        );
        $this->insert("{{config}}", $smsMaxTimeConfig);
        $smsMaxTimeConfigId = $this->dbConnection->lastInsertID;
        
        // создаем настройку анкеты "Оповещение по СМС о переносе или отмене съемки"
        $smsNotificationTypesConfig = array(
            'name'         => 'sendSmsOnEventShift',
            'title'        => 'Включить оповещение по SMS о переносе или отмене съемки',
            'description'  => 'Если мероприятие на которое вы подали заявку перенесено или отменено  
                мы оповестим вас об этом по номеру телефона указанному в анкете.',
            'type'         => 'select',
            'minvalues'    => 1,
            'maxvalues'    => 1,
            'objecttype'   => 'Questionary',
            'objectid'     => 0,
            'timecreated'  => time(),
            'timemodified' => time(),
            // список вариантов условий при которых участник будет оповещен
            'easylistid'   => $smsNotificationListId,
            // по умолчанию: не оповещать
            'valuetype'    => 'EasyList',
            'valuefield'   => 'value',
            'valueid'      => $smsNotificationItemIds['no'],
        );
        $this->insert("{{config}}", $smsNotificationTypesConfig);
        $smsNotificationTypesConfigId = $this->dbConnection->lastInsertID;
        
        // создаем настройку анкеты "отображать/скрыть мои данные"
        $qVisibleConfig = array(
            'name'         => 'visible',
            'title'        => 'Скрыть/показать анкету в поиске',
            'description'  => 'Вы можете скрыть свою анкету из всех разделов каталога и не 
                выводить ее в поиске. Эта настройка не влияет на возможность участвовать в 
                съемках и кастингах: вы по прежнему сможете получать приглашения и отправлять заявки.',
            'type'         => 'select',
            'minvalues'    => 1,
            'maxvalues'    => 1,
            'objecttype'   => 'Questionary',
            'objectid'     => 0,
            'timecreated'  => time(),
            'timemodified' => time(),
            // список вариантов условий при которых участник будет оповещен
            'easylistid'   => $qVisibleOptionsListId,
            // по умолчанию: не оповещать
            'valuetype'    => 'EasyList',
            'valuefield'   => 'value',
            'valueid'      => $qVisibleOptionTypeIds['no'],
        );
        $this->insert("{{config}}", $qVisibleConfig);
        $qVisibleConfigId = $this->dbConnection->lastInsertID;
        
        $questionaries = $this->dbConnection->createCommand()->select('id')->
            from('{{questionaries}}')->queryAll();
        foreach ( $questionaries as $questionary )
        {// создаем по одной настройке каждого типа для каждого пользователя
            $configData = array(
                'objecttype' => 'Questionary',
                'objectid'   => $questionary['id'],
            );
            
            // типы проектов, для которых я получаю приглашения
            $configData['parentid'] = $preferredProjectTypesConfigId;
            $projectSetting = CMap::mergeArray($preferredProjectTypesConfig, $configData);
            $this->insert("{{config}}", $projectSetting);
            // Присылать SMS не ранее
            $configData['parentid'] = $smsMinTimeConfigId;
            $minTimeSetting = CMap::mergeArray($smsMinTimeConfig, $configData);
            $this->insert("{{config}}", $minTimeSetting);
            // Присылать SMS не позднее
            $configData['parentid'] = $smsMaxTimeConfigId;
            $maxTimeSetting = CMap::mergeArray($smsMaxTimeConfig, $configData);
            $this->insert("{{config}}", $maxTimeSetting);
            // Присылать SMS при отмене или переносе события если...
            $configData['parentid'] = $smsNotificationTypesConfigId;
            $notifiationSetting = CMap::mergeArray($smsNotificationTypesConfig, $configData);
            $this->insert("{{config}}", $notifiationSetting);
        }
    }
}