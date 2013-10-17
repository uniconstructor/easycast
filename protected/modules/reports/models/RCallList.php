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
        //Yii::import('application.extensions.ESearchScopes.behaviors.*');
        //Yii::import('application.extensions.ESearchScopes.models.*');
        //Yii::import('application.extensions.ESearchScopes.*');
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
        $vacancies = array();
        foreach ( $event->vacancies as $vacancy )
        {// собираем все подтвержденные заявки для каждой роли в переданном событии
            $element = array(
                'vacancy' => $vacancy,
                'members' => $vacancy->members,
            );
            $vacancies[$vacancy->id] = $element;
        }
        return array(
            'event'     => $event,
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
}