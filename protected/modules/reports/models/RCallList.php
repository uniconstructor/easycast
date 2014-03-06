<?php

/**
 * Модель для отчета "вызывной лист"
 */
class RCallList extends Report
{
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        parent::init();
    }
    
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
     * (non-PHPdoc)
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
     * (non-PHPdoc)
     * @see CActiveRecord::afterSave()
     */
    public function afterSave()
    {
        parent::afterSave();
    }
    
    /**
     * (non-PHPdoc)
     * @see Report::collectData()
     * @var ProjectEvent $event
     */
    public function collectData($event)
    {
        $data        = $this->getData();
        $vacancies   = array();
        $eventInfo   = $event->getAttributes();
        $projectInfo = $event->project->getAttributes();
        if ( ! is_array($data) )
        {// дополнительные данные (например вручную добавленные участники) еще не были присоеденины к фотовызывному
            $data = array();
        }
        
        foreach ( $event->vacancies as $vacancy )
        {// собираем все подтвержденные заявки для каждой роли в переданном событии
            if ( ! $vacancy->members )
            {// если на роль нет ни одного участника - не помещаем ее в вызывной
                continue;
            }
            
            $vacancyInfo = $vacancy->getAttributes();
            $element = array(
                'vacancy' => (object)$vacancyInfo,
                'members' => $this->getMembersInfo($vacancy),
            );
            $vacancies[$vacancy->id] = $element;
            unset($element);
        }
        
        $newData = array(
            'project'   => (object)$projectInfo,
            'event'     => (object)$eventInfo,
            'vacancies' => $vacancies,
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
     */
    public function addExternalMember($vacancyId, $member)
    {
        
    }
    
    /**
     * (non-PHPdoc)
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
     *
     * @param EventVacancy $vacancy
     * @return stdClass
     */
    protected function getMembersInfo($vacancy)
    {
        $result = array();
        
        foreach ( $vacancy->members as $member )
        {
            $memberInfo = $member->getAttributes();
            $result[$member->questionary->id] = (object)$memberInfo;
        }
        
        return $result;
    }
}