<?php

/**
 * Модель для отчета "вызывной лист"
 */
class RCallList extends Report
{
    /**
     * Именованная группа условий: извлечь только вызывные листы для определенного события (мероприятия в проекте)
     * @param int $eventId - id события (модель ProjectEvent)
     */
    public function forEvent($eventId)
    {
        $eventId = (int)$eventId;
        $this->getDbCriteria()->mergeWith(array(
            'with' => array(
                'links' => array(
                    'condition' => "`links`.`linktype` = 'source' AND 
                        `links`.`objecttype` = 'event' AND 
                        `links`.`objectid` = :eventId",
                    'params'    => array(':eventId' => $eventId),
                ),
            )
        ));
        return $this;
    }
    
    /**
     * @see Report::beforeSave()
     */
    public function beforeSave()
    {
        if ( $this->isNewRecord )
        {
            $this->type = 'callList';
        }
        return parent::beforeSave();
    }
    
    /**
     * @see Report::collectData()
     * @var array $options
     */
    public function collectData($options)
    {
        // ранее сохраненные данные отчета
        $data        = $this->getData();
        // мероприятие для которого создается фотовызывной
        $event       = $options['event'];
        // статусы заявок, которые попадают в фотовызывной
        $statuses    = $options['statuses'];
        $vacancies   = array();
        // информация по проекту и мероприятию: getAttributes() используется для того чтобы сериализовывать
        // меньше данных, иначе отчет не всегда помещается даже в поле TEXT длиной 64 Kb
        $eventInfo   = $event->getAttributes();
        $projectInfo = $event->project->getAttributes();
        
        if ( ! is_array($data) )
        {// дополнительные данные еще не были присоеденины к фотовызывному
            $data = array();
        }
        
        foreach ( $event->vacancies as $vacancy )
        {// собираем все подтвержденные заявки для каждой роли в переданном событии
            if ( ! ProjectMember::model()->forVacancy($vacancy->id)->exists() )
            {// если на роль нет ни одного участника - не помещаем ее в вызывной
                continue;
            }
            
            $vacancyInfo = $vacancy->getAttributes();
            $element     = array(
                'vacancy' => (object)$vacancyInfo,
                'members' => $this->getMembersInfo($vacancy, $statuses),
            );
            $vacancies[$vacancy->id] = $element;
            unset($element);
        }
        
        $newData = array(
            'project'   => (object)$projectInfo,
            'event'     => (object)$eventInfo,
            'vacancies' => $vacancies,
            'statuses'  => $statuses,
        );
        
        return CMap::mergeArray($data, $newData);
    }
    
    /**
     * Вручную добавить участника в фотовызывной. Эта операция добавляет к фотовызывному дополнительные
     * данные, добавленные вручную участники никак не связаны с нашими анкетами (Questionary)
     * Эта функция дает возможность вручную редактировать фотовызывной, добавляя в него людей, 
     * которых нет в нашей базе
     * 
     * @param int $vacancyId - id роли к которой прикрепляется участник
     * @param array $member - данные участника
     *                       'hash' - уникальный id участника в списке (нужен чтобы
     *                                вручную добавленную запись можно было удалить)
     *                       'firstname'
     *                       'lastname'
     *                       'age' - возраст, целым числом (не unixtime)
     *                       'bages' - список характеристик участника (заполняется вручную)
     *                       'phone'
     *                       'comment'
     * @return bool
     * 
     * @deprecated не используется, удалить при рефакторинге как и все связанные функции
     */
    public function addExternalMember($vacancyId, $member)
    {
        
    }
    
    /**
     * @see Report::createLinks()
     */
    protected function createLinks()
    {
        $link = new ReportLink;
        $link->reportid   = $this->id;
        $link->linktype   = 'source';
        $link->objecttype = 'event';
        $link->objectid   = $this->reportData['event']->id;
        
        return $link->save();
    }
    
    /**
     * Получить информацию об участниках для выбранной роли
     * @param EventVacancy $vacancy - роль для которой получается список участников со всей информацией
     * @return array
     */
    protected function getMembersInfo($vacancy, $statuses)
    {
        $result  = array();
        $members = ProjectMember::model()->forVacancy($vacancy->id)->withStatus($statuses)->findAll();
        
        foreach ( $members as $member )
        {
            $memberInfo = $member->getAttributes();
            $result[$member->questionary->id] = (object)$memberInfo;
        }
        return $result;
    }
}