<?php

/**
 * Контроллер для работы с коммерческим предложением и всем что связано с продажами
 * @todo перенести сюда страницу коммерческого предложения
 */
class SaleController extends Controller
{
    /**
     * 
     * @return void
     */
    public function actionIndex()
    {
        $this->redirect(Yii::app()->createAbsoluteUrl('/lp/sale/sale.html'));
    }
}