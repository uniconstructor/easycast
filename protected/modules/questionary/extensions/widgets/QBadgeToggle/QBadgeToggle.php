<?php

/**
 * Виджет для редактирования характеристик участника
 * В зависимости от поля анкеты определяет, что нужно скрыть а что показать в форме анкеты
 */
class QBadgeToggle extends ECToggleInput
{
    /**
     * @see ECToggleInput::init()
     */
    public function init()
    {
        parent::init();
        
        $this->afterOn  .= $this->createJsEvent($field, 'on');
        $this->afterOff .= $this->createJsEvent($field, 'off');
    }
    
    /**
     * Получить JS для запускающий JS-событие при изменении значения поля 
     * @return string
     */
    protected function createJsEvent($field, $type)
    {
        $eventName = $field.'_'.$type;
        return '$("body").trigger("'.$eventName.'");';
    }
}