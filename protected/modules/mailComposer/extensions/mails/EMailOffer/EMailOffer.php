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
     *                      в поле user хранится имя того кому адресовано предложение
     *                      в поле comment хранится дополнительный комментарий к отправленному предложению
     */
    public $offer;
    /**
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
        
        if ( ! $this->manager OR $this->manager->username === 'admin' )
        {// Если менеджер не указан - по умолчанию возьмем Колю
            $this->manager = Yii::app()->getModule('user')->user(104);
        }
        
        $this->mailOptions['contentPadding']      = 0;
        $this->mailOptions['mainHeaderType']      = 'text';
        $this->mailOptions['contactPhone']        = Yii::app()->params['customerPhone'];
        $this->mailOptions['contactEmail']        = 'order@easycast.ru';
        $this->mailOptions['manager']             = $this->manager;
        $this->mailOptions['showTopServiceLinks'] = true;
        $this->mailOptions['showContactPhone']    = true;
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
        
        $greeting         = $this->createOfferGreeting();
        $managerName      = $this->manager->questionary->fullname;
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
            'greeting'         => $greeting,
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
            'application.modules.mailComposer.extensions.widgets.EMailManagerInfo.EMailManagerInfo', array(
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
        $result .= CHtml::link('посмотрите полную версию на нашем сайте', $url, array(
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
        return Yii::app()->createAbsoluteUrl('/sale/index', $this->getReferalParams());
    }
    
    /**
     * Получить ссылку на страницу оформления заказа
     * @return string
     */
    protected function getOrderPageUrl($service=null)
    {
        $params = $this->getReferalParams();
        if ( $service )
        {
            $params['service'] = $service;
        }
        return Yii::app()->createAbsoluteUrl('/order/index', $params);
    }
    
    /**
     * Получить ссылку на страницу поиска
     * @return string
     */
    protected function getSearchPageUrl()
    {
        return Yii::app()->createAbsoluteUrl('/search/index', $this->getReferalParams());
    }
    
    /**
     * Получить ссылку на страницу онлайн-кастинга
     * @return string
     */
    protected function getCastingPageUrl()
    {
        return Yii::app()->createAbsoluteUrl('/onlineCasting/index', $this->getReferalParams());
    }
    
    /**
     * Получить ссылку на страницу расчета стоимости
     * @param string $service - короткое название сервиса
     * @return string
     */
    protected function getCalculationPageUrl($service=null)
    {
        $params = $this->getReferalParams();
        if ( $service )
        {
            $params['service'] = $service;
        }
        return Yii::app()->createAbsoluteUrl('/calculation/index', $params);
    }
    
    /**
     * Получить get-параметры для создания реферальной ссылки из с коммерческим предложением
     * 
     * @return array
     */
    protected function getReferalParams()
    {
        return array(
            'offerid'  => $this->offer->id,
            // защищаем свой реферал ключом, чтобы никто не смог выдать себя за заказчика
            // просто перебрав все id приглашений
            'key'      => $this->offer->key,
            'newState' => 'customer',
        );
    }
    
    /**
     * Получить фотографию с изображением одной услуги
     * @param string $name - короткое имя услуги (для разделов каталога - совпадает с 
     *                       коротким именем раздела в таблице catalog_sections)
     * @return string - фотография услуги со ссылкой на соответствующий раздел каталога
     *                  или на страницу оформления заказа, если нужного раздела нет
     */
    protected function createServicePhoto($name)
    {
        $photo        = '';
        $photoOptions = array(
            'style' => 'max-width:127px;',
            'width' => '127',
        );
        $alt          = '';
        $imagesFolder = '/images/offer/services/';
        
        switch ( $name )
        {
            case 'media_actors':
                $alt   = 'Медийные артисты';
                $link  = $this->getSectionUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s1.png');
            break;
            case 'actors':
                $alt   = 'Актеры';
                $link  = $this->getSectionUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s2.png');
            break;
            case 'models':
                $alt   = 'Модели';
                $link  = $this->getSectionUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s3.png');
            break;
            case 'children_section':
                $alt   = 'Дети';
                $link  = $this->getSectionUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s4.png');
            break;
            case 'castings':
                $alt   = 'Кастинги';
                $link  = $this->getOrderPageUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s5.png');
            break;
            case 'mass_actors':
                $alt   = 'Артисты массовых сцен';
                $link  = $this->getSectionUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s6.png');
            break;
            case 'emcees':
                $alt   = 'Ведущие';
                $link  = $this->getSectionUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s7.png');
            break;
            case 'singers':
                $alt   = 'Вокалисты и коллективы';
                $link  = $this->getSectionUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s8.png');
            break;
            case 'dancers':
                $alt   = 'Танцоры и коллективы';
                $link  = $this->getSectionUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s9.png');
            break;
            case 'musicians':
                $alt   = 'Музыканты и коллективы';
                $link  = $this->getSectionUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s10.png');
            break;
            case 'circus_actors':
                $alt   = 'Артисты циркового жанра';
                $link  = $this->getOrderPageUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s11.png');
            break;
            case 'sportsmen':
                $alt   = 'Спортсмены';
                $link  = $this->getOrderPageUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s12.png');
            break;
            case 'types':
                $alt   = 'Типажи';
                $link  = $this->getOrderPageUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s13.png');
            break;
            case 'animals':
                $alt   = 'Животные';
                $link  = $this->getOrderPageUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s14.png');
            break;
            case 'transport':
                $alt   = 'Транспорт';
                $link  = $this->getOrderPageUrl($name);
                $imageUrl = $this->getImageUrl($imagesFolder.'s15.png');
            break;
        }
        
        $photo = CHtml::image($imageUrl, $alt, $photoOptions);
        
        return CHtml::link($photo, $link);
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
            case 'media_actors':     $params['sectionid'] = 2; break;
            case 'actors':           $params['sectionid'] = 4; break;
            case 'children_section': $params['sectionid'] = 5; break;
            case 'models':           $params['sectionid'] = 3; break;
            case 'mass_actors':      $params['sectionid'] = 17; break;
            case 'emcees':           $params['sectionid'] = 8; break;
            case 'singers':          $params['sectionid'] = 9; break;
            case 'dancers':          $params['sectionid'] = 11; break;
            case 'musicians':        $params['sectionid'] = 10; break;
            default: return Yii::app()->createAbsoluteUrl('/search', $params);
        }
        
        return Yii::app()->createAbsoluteUrl('/catalog/catalog/index', $params);
    }
    
    /**
     * Получить лого проекта со ссылкой на страницу проекта
     * @param Project $project
     * @return string
     */
    protected function createProjectLogo($project)
    {
        $logoUrl    = ECPurifier::getImageProxyUrl($project->getAvatarUrl('small', true));
        $projectUrl = Yii::app()->createAbsoluteUrl('/projects/projects/view', array('id' => $project->id));
        $image      = CHtml::image($logoUrl, '', array('style' => 'width:64px;height:64px;border-radius:5px;'));
        $logo       = CHtml::link($image, $projectUrl);
        
        return $logo;
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
    
    /**
     * Создать приветствие для заказчика от имени руководителя проектов 
     * @return string
     */
    protected function createOfferGreeting()
    {
        $linkParams = $this->getReferalParams();
        $linkParams['newState'] = 'customer';
        
        $text = '<span style="font-size:20px;">'.$this->createGreeting($this->offer->name).'</span>';
        if ( $this->manager )
        {
            $position = 'руководитель проектов';
            if ( $this->manager->email === 'ceo@easycast.ru' )
            {// @todo такого свойства как "должность" у нас пока еще нет и неизвестно понадобится ли оно
                // поэтому проверяем email. Коля  -управляющий партнер, все остальные - руководители проектов
                $position = 'управляющий партнер';
            } 
            $text .= 'Меня зовут '.$this->manager->questionary->fullname.
                ', я '.$position.' кастингового агенства easyCast. Прошу вас ';
        }else
        {
            $text .= 'Предлагаем вам ';
        }
        $text .= 'рассмотреть наше коммерческое предложение и при желании оценить
                нашу систему подбора актеров, расположенную на сайте '.
                CHtml::link('easycast.ru', Yii::app()->createAbsoluteUrl('/site/index', $linkParams)).'.<br><br>';
        return $text;
    }
}