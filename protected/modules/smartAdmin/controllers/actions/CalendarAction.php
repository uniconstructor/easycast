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
        CVarDumper::dump(Yii::app()->request->isAjaxRequest);
        echo 'CALENDAR';
    }
}
