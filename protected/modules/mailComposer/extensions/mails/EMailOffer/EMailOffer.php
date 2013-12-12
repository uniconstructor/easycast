<?php

// подключаем базовый класс для шаблонов писем
Yii::import('application.modules.mailComposer.extensions.mails.EMailBase.EMailBase');

/**
 * Коммерческое предложение (для отправки в виде письма, вертикальная верстка)
 */
class EMailOffer extends EMailBase
{
    /**
     * @var CustomerOffer - приглашение для заказчика класса "коммерческое предложение"
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
        {// Если менеджер не указан - по умолчанию возьмем Иру
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
        // добавляем основное содержимое страницы
        $this->addSegment($this->getOfferContent());
        
        $this->addSegment($this->getPersonalizationBlock());
        
        // выводим виджет со всеми данными
        parent::run();
    }
    
    /**
     * Добавить одним блоком все основное содержимое коммерческого предложения
     * @return void
     */
    protected function getOfferContent()
    {
        $block = array();
        
        $orderUrl         = Yii::app()->createAbsoluteUrl('/order/index', $this->getReferalParams());
        $calculationUrl   = Yii::app()->createAbsoluteUrl('/calculation/index', $this->getReferalParams());
        $tourUrl          = Yii::app()->createAbsoluteUrl('/tour/index', $this->getReferalParams());
        $onlineCastingUrl = Yii::app()->createAbsoluteUrl('/onlineCasting/index', $this->getReferalParams());
        
        $block['type'] = 'text640';
        $block['text'] = $this->render('offer', array(
            'orderUrl'         => $orderUrl,
            'calculationUrl'   => $calculationUrl,
            'tourUrl'          => $tourUrl,
            'onlineCastingUrl' => $onlineCastingUrl,
        ), true);
        
        return $block;
    }
    
    /**
     * Получить блок с информацией о человеке команды, от имени которого отправляется коммерческое предложение
     * @return array
     */
    protected function getPersonalizationBlock()
    {
        $block = array();
        
        $block['type'] = 'text640';
        $block['text'] = $this->widget(
            'application.modules.mailComposer.extensions.widgets.EMailManagerInfo.EMailManagerInfo',
            array(
                'manager' => $this->manager,
            ), true);
        
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
        return Yii::app()->createAbsoluteUrl('/sale', $this->getReferalParams());
    }
    
    /**
     * Получить get-параметры для создания реферальной ссылки из с коммерческим предложением
     * 
     * @return array
     */
    protected function getReferalParams()
    {
        return array(
            'offerid' => $this->offer->id,
            // защищаем свой реферал ключом, чтобы никто не смог выдать себя за заказчика
            // просто перебрав все id приглашений
            'key'     => $this->offer->key,
        );
    }
    
    /**
     * Получить ссылку на раздел каталога
     * @param string $name
     * @return string
     */
    protected function getSectionUrl($name)
    {
        $params = $this->getReferalParams();
        switch ( $name )
        {
            case 'actors': $params['sectionid'] = 4; break;
            case 'ams':    $params['sectionid'] = 17; break;
            case 'models': $params['sectionid'] = 3; break;
            case 'types':  $params['sectionid'] = 1; break;
        }
        
        return Yii::app()->createAbsoluteUrl('/catalog/catalog/index', $params);
    }
    
    /**
     * Получить путь к изображению через прокcи сервера google
     * @param string $path - фрагмент пути к изображению
     * @return string
     */
    protected function getImageUrl($path)
    {
        return ECPurifier::getImageProxyUrl(Yii::app()->createAbsoluteUrl($path));
    }
}