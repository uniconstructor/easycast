<?php

/**
 * Фильтр для обработки referal-ссылок при переходе на сайт
 */
class ECReferalFilter extends CFilter
{
    /**
     * Performs the pre-action filtering.
     * @see CFilter::preFilter()
     *
     * @param CFilterChain $filterChain the filter chain that the filter is on.
     * @return boolean
     */
    protected function preFilter($filterChain)
    {
        // обрабатываем все виды referal-ссылок:
        
        // коммерческое предложение
        $this->processOffer();
        // приглашение дополнить данные
        $this->processUserToken();
        // @todo приглашение на съемку
        // @todo приглашение на отбор актеров
        return parent::preFilter($filterChain);
    }

    /**
     * Performs the post-action filtering.
     *
     * @param CFilterChain $filterChain the filter chain that the filter is on.
     * @return void
     */
    protected function postFilter($filterChain)
    {
        // logic being applied after the action is executed
        parent::postFilter($filterChain);
    }
    
    /**
     * Пометить отправленное коммерческое предложение как использованное и запомнить данные заказчика в сессию
     * для подстановки в форму
     * 
     * @return void
     */
    protected function processOffer()
    {
        $key = Yii::app()->request->getParam('key');
        $id  = Yii::app()->request->getParam('offerId');
        
        if ( ! $id OR ! $key OR ! $offer = CustomerOffer::model()->findByPk($id) )
        {// это не реферальная ссылка коммерческого предложения
            return true;
        }
        // помечаем приглашение использованным
        $offer->markUsed();
        // запоминаем приглашение в сессию, для того чтобы использовать контакты заказчика во всех формах
        Yii::app()->session->add('activeOffer', $offer);
    }
    
    /**
     * Автоматически пустить на сайт пользователя без ввода логина и пароля 
     * (при переходе по ссылке с токеном из писем с приглашениями)
     * 
     * @return boolean
     */
    protected function processUserToken()
    {
        // подключаем модели проектов для работы с проектами - они пригодятся при работе с приглашением
        Yii::import('application.modules.projects.models.*');
        
        $key = Yii::app()->request->getParam('key');
        $id  = Yii::app()->request->getParam('inviteId');
        
        if ( ! $invite = EventInvite::model()->findByPk($id) )
        {/* @var EventInvite $invite */
            // приглашение не найдено - не производим вход
            return true;
        }
        if ( ! isset($invite->subscribekey) OR $invite->subscribekey != $key )
        {// ключ доступа не совпадает с указанным - не производим вход
            return true;
        }
        if ( Yii::app()->user->isGuest )
        {// автоматически авторизуем гостя по токену
            Yii::app()->module('user')->forceLogin($invite->questionary->user);
        }
        return true;
    }
}