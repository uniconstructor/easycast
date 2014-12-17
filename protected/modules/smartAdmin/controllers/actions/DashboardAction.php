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
        
        $this->controller->render('dashboard');
    }
}