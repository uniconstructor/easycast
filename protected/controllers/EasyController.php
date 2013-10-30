<?php

class EasyController extends Controller
{
    /**
     * Быстрая регистрация массовки
     * @return void
     */
    public function actionIndex()
    {
        $this->redirect('/user/registration');
    }
}