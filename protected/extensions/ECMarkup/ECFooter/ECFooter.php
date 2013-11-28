<?php
/**
 * Виджет отображающий подвал страницы
 * @todo вынести настройки всех соцсетей в widgetFactory
 */
class ECFooter extends CWidget
{
    /**
     * @var string - ссылка на папку с ресурсами расширения
     */
    protected $_assetUrl;

    /**
     * @var string ссылка на страницу facebook
     */
    protected $_faceBookPage = '';

    /**
     * @var string API IP для виджета "мне нравится" вконтакте
     */
    protected $_vkontakteApiId = '';

    /**
     * Подготавливает виджет к запуску
     */
    public function init()
    {
        $this->_vkontakteApiId = Yii::app()->params['vkontakteApiId'];
    }

    /**
     * Отображает подвал страницы
     */
    public function run()
    {
        echo '<div id="footer">';
        // Используем стили Twitter Bootstrap для того чтобы сделать резиновую верстку блоков в подвале
        echo '<div class="row">';
        // Выводим горизонтальную полоску
        echo '<div class="span12"><hr noshade size="2" style="border-color:white;"></div>';
        echo '</div>';
        // Выводим кнопки социальных сетей
        echo '<div class="row-fluid show-grid">';
        $this->printSocialButtons();
        // Выводим счетчик Яндекса
        $this->printYandexCounter();
        // Выводим контакты еще раз
        $this->printContacts();
        echo '</div>';
        
        // выводим копирайт
        echo '<div class="row-fluid show-grid">';
        $this->printCopyright();
        echo '</div>';
		
	    echo '</div><!-- footer -->';
    }

    /**
     * Вывести правовую информацию
     */
    public function printCopyright()
    {
        echo '<div class="span12 easycast-copyright">';
        echo '&copy; 2005-' . date('Y').'&nbsp;';
        $easyCast = '&laquo;' . CHtml::link('EasyCast', Yii::app()->getBaseUrl(true)) . '&raquo;';
        echo Yii::t('coreMessages', 'copyright_notice', array('{easycast}' => $easyCast));
        echo '&nbsp;<br>';
        echo CHtml::link('<small>Пользовательское соглашение</small>', Yii::app()->createAbsoluteUrl('/site/page/view/license') );
        ///echo Yii::powered();
        echo '</div>';
    }

    /**
     * Отобразить все like-кнопки всех социальных сетей
     */
    public function printSocialButtons()
    {
        echo '<div class="span9">';
        if ( YII_DEBUG )
        {// не показываем кнопки соцсетей на машине разработчика - так удобнее
            echo '</div>';
            return;
        }
        $this->widget('application.extensions.ESocial.ESocial', array(
             'style'=>'horizontal',
             'networks' => array(
                 // g+
                 'googleplusone'=>array(
                     "size"=>"medium",
                     "annotation"=>"bubble",
                 ),
                 // В контакте
                 'vkontakte' => array(
                     'apiid' => $this->_vkontakteApiId,
                     'containerid' => 'vk_like', 
                     'scriptid'    => 'vkontakte-init-script',
                     'type'        => 'button',
                 ),
                 // mail.ru и одноклассники (добавляются одной кнопкой)
                 'mailru' => array(
                     'type' => 'combo'
                 ),
                 // Твиттер
                 'twitter'=>array(
                     'data-via'=>'', //http://twitter.com/#!/YourPageAccount if exists else leave empty
                 ),
                 // Facebook
                 'facebook'=>array(
                     'href'=>'http://easycast.ru/',//asociate your page http://www.facebook.com/page 
                     'action'=>'recommend',//recommend, like
                     'colorscheme'=>'light',
                     'width'=>'140px',
                 )
             )
        ));
        echo '</div>';
    }

    /**
     * Вывести блок с контактами
     */
    public function printContacts()
    {
        /*echo '<div class="span2 offset1">';
        $this->widget('application.extensions.ECMarkup.ECContacts.ECContacts',
            array(
                 'displayItems' => array('phone', 'email'),
            ));
        echo '</div>';*/
    }
    
    /**
     * Вывести счетчик посещений Яндекса, чтобы работала Яндекс.Метрика
     * 
     * @return null
     */
    protected function printYandexCounter()
    {
        if ( YII_DEBUG )
        {// не показываем счетчик на машине разработчика, чтобы не сбивать метрику
            return;
        }
        $this->render('yandexCounter');
    }
}