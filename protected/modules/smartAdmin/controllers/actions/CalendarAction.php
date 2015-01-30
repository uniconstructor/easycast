<?php

/**
 * Страница календаря съемок в админке
 */
class CalendarAction extends AjaxAction
{
    /**
     * @return void
     */
    public function run()
    {
        $this->controller->renderText('cal');
    }
}
