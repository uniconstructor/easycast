<?php

/**
 * Этот класс позволяет запускать отображение виджетов из консоли
 */
class ConsoleWidgetRenderBehavior extends CBehavior
{
    /**
     * 
     * @return null
     */
    public function getViewRenderer()
    {
        return null;
    }
    
    /**
     * 
     * @return null
     */
    public function getTheme()
    {
        return null;
    }
    
    /**
     * Returns the widget factory.
     * @return IWidgetFactory the widget factory
     * @since 1.1
     */
    public function getWidgetFactory()
    {
        return Yii::createComponent(array('class' => 'CWidgetFactory'));
    }
}