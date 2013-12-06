<?php

// подключаем базовый класс для шаблонов писем
Yii::import('application.modules.mailComposer.extensions.mails.EMailBase.EMailBase');

/**
 * Коммерческое предложение (для отправки в виде письма, вертикальная верстка)
 */
class EMailOffer extends EMailBase
{
    /**
     * @var ROffer - отчет класса "коммерческое предложение"
     */
    public $offer;
    /**
     *
     * @var User - руководитель проекта, от имени которого отправляется коммерческое предложение
     */
    public $manager;
    
    /**
     *
     * @see EMailBase::init()
     */
    public function init()
    {
        Yii::import('ext.ESearchScopes.behaviors.*');
        Yii::import('ext.ESearchScopes.models.*');
        Yii::import('ext.ESearchScopes.*');
        Yii::import('ext.galleryManager.*');
    
        parent::init();
        
        if ( ! $this->manager )
        {// @todo в верстке готова только фотография Иры, поэтому пока что будем брать только ее
            $this->manager = Yii::app()->getModule('user')->user(775);
        }
        
        $this->mailOptions['contentPadding'] = 0;
        $this->mailOptions['mainHeaderType'] = 'text';
        $this->mailOptions['contactPhone'] = Yii::app()->params['customerPhone'];
        $this->mailOptions['contactEmail'] = 'order@easycast.ru';
        $this->mailOptions['manager'] = $this->manager;
        $this->mailOptions['showTopServiceLinks'] = true;
        $this->mailOptions['topBarOptions']['displayWebView'] = true;
        $this->mailOptions['topBarOptions']['webViewLink']    = $this->getWebViewLink();
    }
    
    /**
     * @see EMailBase::run()
     */
    public function run()
    {
        // альтернативная шапка
        $this->addSegment($this->getOfferHeaderBlock());
        // слоган
        $this->addSegment($this->getSloganBlock());
        // разделы каталога
        $this->addSegment($this->getSectionsBlock());
        // кнопка заказа
        $this->addSegment($this->getOrderButtonBlock());
        // кнопка расчета стоимости
        $this->addSegment($this->getPriceButtonBlock());
        // наши услуги
        $this->addSegment($this->getServicesBlock());
        // видеотур
        $this->addSegment($this->getTourButtonBlock());
        // 100 причин работать с нами
        $this->addSegment($this->get100ReasonsBlock());
        // персонализация
        $this->addSegment($this->getPersonalizationBlock());
        // выводим виджет со всеми данными
        parent::run();
    }
    
    /**
     * Выводит альтернативную шапку коммерческого предложения
     * (с правильным фоном, ссылкой и кнопкой "сделать заказ")
     * @return array
     */
    protected function getOfferHeaderBlock()
    {
        $block = array();
    
        $block['type']         = 'image640';
        $block['imageStyle']   = 'border:0px;';
        $block['imageLink']    = Yii::app()->createAbsoluteUrl('/images/offer/top.gif');
        $block['imageTarget']  = $this->getSalePageUrl();
    
        return $block;
    }
    
    /**
     * 
     * @return array 
     */
    protected function getSloganBlock()
    {
        $block = array();
        
        $block['type']       = 'image640';
        $block['imageLink']  = Yii::app()->createAbsoluteUrl('/images/offer/slogan.png');
        
        return $block;
    }
    
    /**
     * Получить блок письма с разделами каталога
     * @return array
     */
    protected function getSectionsBlock()
    {
        $block = array();
        
        $block['type'] = 'text640';
        $block['text'] = $this->render('_sections', null, true);
        
        return $block;
    }
    
    /**
     * Получить блок письма с кнопкой "сделать заказ"
     * @return array
     */
    protected function getOrderButtonBlock()
    {
        $block = array();
        
        $block['type'] = 'text640';
        $block['text'] = $this->render('_orderButton', null, true);
        
        return $block;
    }
    
    /**
     * Получить блок письма с кнопкой "рассчитать стоимость"
     * @return array
     */
    protected function getPriceButtonBlock()
    {
        $block = array();
        
        $block['type'] = 'text640';
        $block['text'] = $this->render('_priceButton', null, true);
        
        return $block;
    }
    
    /**
     * Получить блок письма с описанием онлайн-сервисов
     * @return array
     */
    protected function getServicesBlock()
    {
        $block = array();
        
        $block['type']       = 'image640';
        $block['imageLink']  = Yii::app()->createAbsoluteUrl('/images/offer/lp2.gif');
        
        return $block;
    }
    
    /**
     * Получить блок письма с кнопкой видео-тура
     * @return array
     */
    protected function getTourButtonBlock()
    {
        $block = array();
        
        $block['type'] = 'text640';
        $block['text'] = $this->render('_tourButton', null, true);
        
        return $block;
    }
    
    /**
     * Получить блок письма "100 причин работать с нами"
     * @return array
     */
    protected function get100ReasonsBlock()
    {
        $block = array();
        
        $block['type']       = 'image640';
        $block['imageLink']  = Yii::app()->createAbsoluteUrl('/images/offer/lp3.png');
        
        return $block;
    }
    
    /**
     * Получить блок с информацией о человеке команды, от имени которого отправляется коммерческое предложение
     * @return array
     */
    protected function getPersonalizationBlock()
    {
        $block = array();
        
        $block['type']       = 'image640';
        $block['imageLink']  = Yii::app()->createAbsoluteUrl('/images/offer/ibuzaeva.png');
        
        return $block;
    }
    
    /**
     * Получить ссылку на просмотр коммерческого предложения на сайте
     * @return string
     */
    protected function getWebViewLink()
    {
        $url = $this->getSalePageUrl();
        
        $result = '<div style="color:#fff">(Если вы не видите текст или письмо отображается неправильно нажмите "показать картинки" или ';
        $result .= CHtml::link('посмотрите полную версию письма на нашем сайте', $url, array(
            'style' => 'color:#fff;font-weight:bold;text-decoration:underline;',
        ));
        $result .= ').</div>';
        
        return $result;
    }
    
    /**
     * Получить ссылку на страницу с коммерческим предложением у нас на сайте
     * (и добавить реферал, чтобы знать кто из заказчиков воспользовался ссылкой, когда это произошло,
     * и кто из команды отправил это коммерческое изначально)
     * @return string
     */
    protected function getSalePageUrl()
    {
        return Yii::app()->createAbsoluteUrl('/sale', array('offerid' => $this->offer->id));
    }
    
    /**
     * Получить ссылку на раздел каталога
     * @param string $name
     * @return string
     */
    protected function getSectionUrl($name)
    {
        $params = array('offerid' => $this->offer->id);
        switch ( $name )
        {
            case 'actors': $params = array('sectionid' => 4); break;
            case 'ams':    $params = array('sectionid' => 17); break;
            case 'models': $params = array('sectionid' => 3); break;
            case 'types':  $params = array('sectionid' => 1); break;
        }
        
        return Yii::app()->createAbsoluteUrl('/catalog/catalog/index', $params);
    }
}