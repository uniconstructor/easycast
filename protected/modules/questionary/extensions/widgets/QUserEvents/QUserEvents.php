<?php

// подключаем класс-родитель виджета
Yii::import('application.modules.questionary.extensions.widgets.QUserApplications.QUserApplications');

/**
 * Виджет "мои съемки" - выводит все мероприятия в которых участвует пользователь
 */
class QUserEvents extends QUserApplications
{
    /**
     * @see QUserApplications::getHeader()
     */
    protected function getHeader()
    {
        return 'Мои съемки';
    }
    
    /**
     * @see QUserApplications::initItems()
     */
    protected function initItems()
    {
        $this->items = array();
        $instances = ProjectMember::model()->forQuestionary($this->questionary->id)->
            withStatus(ProjectMember::STATUS_ACTIVE)->findAll();
        foreach ( $instances as $key => $instance )
        {// Убираем из предстоящих съемок прошедшие события
            // @todo переписать при рефакторинге, после того как будет введено изменение статуса по крону 
            if ( (time() < $instance->vacancy->event->timeend) OR ($instance->vacancy->event->nodates) )
            {
                $this->items[$key] = $instance;
            }
        }
    }
    
    /**
     * Отобразить сообщение о том, что заявок нет
     * @param string $mode - какие заявки пытаемся вывести
     * @return string
     *
     * @todo выводить разные сообщения в зависимости от режима отображения
     */
    protected function displayEmptyMessage($mode=null)
    {
        $text = 'Нет предстоящих съемок';
        $this->render('message', array('text' => $text));
    }
}