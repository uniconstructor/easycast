<?php

/**
 * Класс для работы с заказами для незарегистрированных пользователей
 * 
 * @package easycast
 * @todo перенести сюда все функции для работы с заказами из siteController
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
        $this->render('index', array('order' => $order));
    }
}