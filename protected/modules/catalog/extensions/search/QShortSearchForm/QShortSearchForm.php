<?php

// подключаем родительский класс
Yii::import('catalog.extensions.search.QSearchForm.QSearchForm');

/**
 * Сокращенная форма поиска на главной странице
 * @deprecated больше не используется, удалить при рефакторинге
 */
class QShortSearchForm extends QSearchForm
{
    /**
     * @var bool - экспериментальная функция: обновлять результаты поиска по мере выбора критериев
     * @todo отключить, если сервер не будет справляться
     */
    public $refreshDataOnChange = false;
    
    /**
     * 
     * @return void
     */
    public function run()
    {
        $this->render('form');
    }
}