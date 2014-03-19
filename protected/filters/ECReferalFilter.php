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
     *
     * @return boolean
     */
    protected function preFilter($filterChain)
    {
        // обрабатываем все виды referal-ссылок:
        
        // коммерческое предложение
        $this->processOffer();
        // @todo приглашение на съемку
        // @todo приглашение на отбор актеров
        
        return true;
    }

    /**
     * Performs the post-action filtering.
     *
     * @param CFilterChain $filterChain the filter chain that the filter is on.
     *
     * @return void
     */
    protected function postFilter($filterChain)
    {
        // logic being applied after the action is executed
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
            return;
        }
        // помечаем приглашение использованным
        $offer->markUsed();
        // запоминаем приглашение в сессию, для того чтобы использовать контакты заказчика во всех формах
        Yii::app()->session->add('activeOffer', $offer);
    }
}