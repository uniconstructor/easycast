<?php

class m141022_214200_addYesNoConfig extends EcMigration
{
    /**
     * @see CDbMigration::safeUp()
     */
    public function safeUp()
    {
        $yesNoList = array(
            'name'           => 'Список для стандартных оповещений системы',
            'description'    => 'Содержит все стандартные оповещения. На элементы из этого списка
                ссылаются все настройки системы, связанные со стандартными оповещениями.',
            'triggerupdate'  => 'manual',
            'triggercleanup' => 'never',
            'unique'         => 1,
        );
        $yesNoList['id'] = $this->createList($yesNoList);
        
        $items = array(
            array(
                'name'        => 'Да',
                'value'       => '1',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $yesNoList['id'],
            ),
            array(
                'name'        => 'Нет',
                'value'       => '0',
                'objecttype'  => 'EasyListItem',
                'objectfield' => 'value',
                'easylistid'  => $yesNoList['id'],
            ),
        );
        foreach ( $items as $item )
        {
            $this->createListItem($item);
        }
        
        // делаем настройку чтобы навсегда запомнить этот потрясающий список
        $yesNoListConfig = array(
            'name'         => 'yesNoListId',
            'title'        => 'Список с вариантами да/нет',
            'description'  => 'Используется чаще всего :)',
            'type'         => 'text',
            'minvalues'    => 0,
            'maxvalues'    => 0,
            'objecttype'   => 'system',
            'objectid'     => 0,
            'easylistid'   => $yesNoList['id'],
            'valuetype'    => 'EasyList',
            'valuefield'   => 'listItems',
            'valueid'      => $yesNoList['id'],
        );
        $yesNoListConfig['id'] = $this->createConfig($yesNoListConfig);
        
        /////////////////////////////////////////////
        // cписок используемых стандартных оповещений 
        // наследуемые элементы могут содержать другие тексты 
        
        // извлекаем ранее созданный список типов
        $condition = "objecttype='system' AND objectid=0 AND name='systemNotificationsList'";
        $oldConfig = $this->dbConnection->createCommand()->select('*')->
            from('{{config}}')->where($condition)->queryRow();
        unset($oldConfig['id']);
        
        // создаем настройку со списком используемых стандартных оповещений:
        // проект
        $projectNotificationsConfig = array();
        $projectNotificationsConfig['objecttype']   = 'Project';
        $projectNotificationsConfig['timecreated']  = time();
        $projectNotificationsConfig['timemodified'] = 0;
        $projectNotificationsConfig = CMap::mergeArray($oldConfig, $projectNotificationsConfig);
        $this->createConfig($projectNotificationsConfig);
        
        // мероприятие
        $eventNotificationsConfig = array();
        $eventNotificationsConfig['objecttype']   = 'ProjectEvent';
        $eventNotificationsConfig['timecreated']  = time();
        $eventNotificationsConfig['timemodified'] = 0;
        $eventNotificationsConfig = CMap::mergeArray($oldConfig, $eventNotificationsConfig);
        $this->createConfig($eventNotificationsConfig);
        
        // роль
        $vacancyNotificationsConfig = array();
        $vacancyNotificationsConfig['objecttype']   = 'EventVacancy';
        $vacancyNotificationsConfig['timecreated']  = time();
        $vacancyNotificationsConfig['timemodified'] = 0;
        $vacancyNotificationsConfig = CMap::mergeArray($oldConfig, $vacancyNotificationsConfig);
        $this->createConfig($vacancyNotificationsConfig);
    }
}