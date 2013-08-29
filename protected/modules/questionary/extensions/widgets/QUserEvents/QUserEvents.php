<?php

// подключаем класс-родитель виджета
Yii::import('application.modules.questionary.extensions.widgets.QUserApplications.QUserApplications');
/**
 * Виджет "мои съемки" - выводит все мероприятия в которых участвует пользователь
 */
class QUserEvents extends QUserApplications
{
    /**
     * (non-PHPdoc)
     * @see QUserApplications::getHeader()
     */
    protected function getHeader()
    {
        return 'Мои съемки';
    }
    
    /**
     * (non-PHPdoc)
     * @see QUserApplications::initItems()
     */
    protected function initItems()
    {
        $this->items = $this->questionary->activememberinstances;
    }
}