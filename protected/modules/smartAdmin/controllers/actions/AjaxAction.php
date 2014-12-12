<?php

/**
 * Базовый класс для всех AJAX-действий темы SmartAdmin
 * 
 * При отрисовке страницы использует нужные layout-макеты, в конце всегда добавляет 
 * скрипты инициализации страницы
 */
abstract class AjaxAction extends CAction
{
    /**
     * Raised right before the action invokes the render method.
     * Event handlers can set the {@link CEvent::handled} property
     * to be true to stop further view rendering.
     * 
     * @param CEvent $event event parameter
     */
    public function onBeforeRender($event)
    {
        $this->raiseEvent('onBeforeRender', $event);
    }
    
    /**
     * Raised right after the action invokes the render method.
     * 
     * @param CEvent $event event parameter
     */
    public function onAfterRender($event)
    {
        $this->raiseEvent('onAfterRender', $event);
    }
}