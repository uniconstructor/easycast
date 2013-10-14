<?php

/**
 * Модель для отчета "вызывной лист"
 */
class RCallList extends Report
{
    /**
     * (non-PHPdoc)
     * @see Report::collectData()
     * @var ProjectEvent $event
     */
    public function collectData($event)
    {
        $vacancies = array();
        foreach ( $event->vacancies as $vacancy )
        {// собираем все подтвержденные заявки для каждой роли
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
}