<?php

/**
 * Контроллер для заказа рассчета стоимости
 * @todo перенести в главный контроллер сайта после того как станет ясно как настроить rewriteRule
 *       таким образом, чтобы обращение по короткому адресу easycast.ru/calculation
 *       приводило к выполнению действия SiteController 
 */
class CalculationController extends Controller
{
    /**
     * Отобразить и обработать форму рассчета стоимости
     * @return void
     */
    public function actionIndex()
    {
        $this->render('calculation');
    }
}