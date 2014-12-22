<?php

/**
 * Действие для отображения главной страницы админки (dashboard)
 */
class DashboardAction extends AjaxAction
{
    /**
     * @return void
     */
    public function run()
    {
        if ( ! Yii::app()->request->isAjaxRequest )
        {
            $this->controller->renderText('dashboard');
        }else
        {
            echo 'dashboard';
        }
    }
}