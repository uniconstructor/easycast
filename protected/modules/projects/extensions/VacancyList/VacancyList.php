<?php

/**
 * Список ролей для мероприятия, проекта или участника
 * 
 * @todo добавить проверку входных параметров
 * @todo добавить параметр objectId, сделать получение ролей по objectType + objectId
 *       оставить параметр event как public. Добавить параметр project. Проверять сначала начилие project 
 *       и event, а только потом objectType и objectId
 * @todo настройка: выводить ли сообщение если нет подходящих ролей
 */
class VacancyList extends CWidget
{
    /**
     * @var string - тип объекта для которого отображается список ролей
     *               event
     *               project
     *               questionary
     */
    public $objectType;
    /**
     * @var ProjectEvent
     */
    public $event;
    /**
     * @var Project
     */
    public $project;
    /**
     * @var Questionary - анкета просматривающего роль участника или участника для которого должен быть отображен
     *                    список ролей
     */
    public $questionary;
    /**
     * @var bool - отображать ли те роли, которые не доступны участнику?
     */
    public $displayNotAvailable = true;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        switch ( $this->objectType )
        {
            case 'event': 
                if ( ! ($this->event instanceof ProjectEvent) )
                {
                    throw new CException('Не передано мероприятие для отображения списка ролей');
                }
            break;
            default: throw new CException('Не передан тип отображения для списка ролей');
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $vacancies = $this->getVacancies() )
        {// @todo получать активные и завершенные роли (после написания условий с параметров в модели роли)
            return;
        }
        foreach ( $vacancies as $vacancy )
        {// по каждой роли выводим краткую информацию + кнопки действий
            $this->widget('projects.extensions.VacancyInfo.VacancyInfo', array(
                'vacancy'             => $vacancy,
                'questionary'         => $this->questionary,
                'displayNotAvailable' => $this->displayNotAvailable,
                'displayNotAvailable' => false,
                'isAjaxRequest'       => true,
                'isAvailable'         => $vacancy->isAvailableForUser($this->questionary->id, true),
                'buttonSize'          => 'large',
            ));
        }
    }
    
    /**
     * Получить список ролей для отображения
     * @return EventVacancy[]
     */
    protected function getVacancies()
    {
        switch ( $this->objectType )
        {
            case 'event': return $this->event->activevacancies; break;
        }
    }
}