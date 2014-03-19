<?php

/**
 * Класс для работы с заказами для незарегистрированных пользователей
 * 
 * @package easycast
 * @todo перенести сюда все функции для работы с заказами из siteController
 * @todo настроить права доступа
 */
class OrderController extends Controller
{
    /**
     * Действие по умолчанию - отобразить форму срочного заказа на отдельной странице
     * @return void
     */
    public function actionIndex()
    {
        $order = new FastOrder();
        if ( $offer = Yii::app()->session->get('activeOffer') )
        {/* @var $offer CustomerOffer */
            if ( $offer->email AND ! $order->email )
            {
                $order->email = $offer->email;
            }
            if ( $offer->name AND ! $order->name )
            {
                $order->name = $offer->name;
            }
        }
        $this->render('index', array('order' => $order));
    }
}