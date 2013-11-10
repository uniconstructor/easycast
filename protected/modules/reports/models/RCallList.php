<?php

/**
 * Модель для отчета "вызывной лист"
 */
class RCallList extends Report
{
    /**
     * (non-PHPdoc)
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
        $vacancies   = array();
        $eventInfo   = $event->getAttributes();
        $projectInfo = $event->project->getAttributes();
        
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
        
        return array(
            'project'   => (object)$projectInfo,
            'event'     => (object)$eventInfo,
            'vacancies' => $vacancies,
        );
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
        $link->save();
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