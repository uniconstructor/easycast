<?php
/**
 * Виджет, отображающий шапку страницы
 */
class ECHeader extends CWidget
{
    /**
     * @var bool - отображать ли блок с контактами
     */
    public $displayContacts = true;
    /**
     * @var bool - отображать ли логотип сайта со ссылкой на главную
     */
    public $displayLogo = true;
    /**
     * @var bool - отображать ли виджет входа на сайт
     */
    public $displayloginTool = true;
    /**
     * @var bool - отображать ли информер участника/заказчика
     */
    public $displayInformer = true;
    /**
     * @var bool - отображать ли информер участника/заказчика
     */
    public $displaySwitch   = true;
    /**
     * @var string - новое состояние переключателя "участник/заказчик"
     */
    public $newState;
    /**
     * @var string - ссылка на папку с ресурсами расширения
     */
    protected $_assetUrl;
    /**
     * @var int
     */
    protected $defaultSwitchMargin; 
    /**
     * @var int
     */
    protected $newSwitchMargin; 
    
    /**
     * Подготавливает виджет к запуску
     */
    public function init()
    {
        if ( Yii::app()->user->isGuest AND ! Yii::app()->user->checkAccess('Admin') )
        {// если пользователь уже зашел на сайт и он не админ - то не отображаем переключатель
            $this->displaySwitch = false;
        }
        $this->defineNewState();
        // Загружаем стили шапки страницы
        $this->_assetUrl = Yii::app()->assetManager->publish(
            Yii::app()->extensionPath . DIRECTORY_SEPARATOR . 
            'ECMarkup' . DIRECTORY_SEPARATOR .
            'ECHeader' . DIRECTORY_SEPARATOR . 
            'assets'   . DIRECTORY_SEPARATOR);
        Yii::app()->clientScript->registerCssFile($this->_assetUrl.'/css/header.css');
    }
    
    /**
     * 
     * @return void
     */
    protected function defineNewState()
    {
        $mode = Yii::app()->getGlobalState('userMode', 'user');
        switch ( $mode )
        {
            case 'user': 
                $this->newState = 'customer'; 
                $this->defaultSwitchMargin = 0;
                $this->newSwitchMargin     = 100;
            break;
            case 'customer':
                $this->newState = 'user';
                $this->defaultSwitchMargin = 100;
                $this->newSwitchMargin     = 0;
            break;
        }
    }
    
    /**
     * 
     * @return void
     */
    protected function getSwitchScript()
    {
        return "$('#switch').click(function() {
            $('#switch_but').animate({marginLeft: '{$this->newSwitchMargin}'}, 550);
            window.setTimeout(function(){document.location.href='?newState={$this->newState}';}, 550);
            //return false;
        });";
    }

    /**
     * Отображает шапку страницы
     */
    public function run()
    {
        $this->render('header');
    }

    /**
     * Отображает виджет входа на сайт, который для зарегистрированых пользователей отображает
     * их фотографию
     */
    public function printLoginTool()
    {
        echo '<div class="span2 easycast-menu-item text-center">';
        if ( $this->displayloginTool )
        {
            $this->widget('application.extensions.ECMarkup.ECLoginWidget.ECLoginWidget');
        }
        echo '</div>';
    }

    /**
     * Вывести главный логотип со ссылкой
     */
    protected function printLogo()
    {
        if ( ! $this->displayContacts AND ! $this->displayloginTool AND ! $this->displayInformer )
        {
            echo '<div class="span6 offset3">';
        }else
        {
            echo '<div class="span4 offset2">';
        }
        echo '<div id="logo" class="easycast-logo">';
        echo CHtml::link(CHtml::image('/images/logo-75x330.png').'', Yii::app()->getBaseUrl(true));
        echo '</div>';
        echo '</div>';
    }

    /**
     * Вывести блок с контактами
     */
    public function printContacts()
    {
        if ( $this->displayContacts )
        {
            $this->widget('application.extensions.ECMarkup.ECContacts.ECContacts');
        }
    }
    
    /**
     * Добавляет в правый верхний угол виджет с информацией для пользователя
     * 
     * @return null
     */
    protected function printInformer()
    {
        if ( $this->displayInformer )
        {
            $this->widget('application.extensions.ECMarkup.ECMainInformer.ECMainInformer');
        }
    }
    
    /**
     * Отобразить переключатель участник/заказчик
     * @return null
     */
    protected function printSwitch()
    {
        if ( ! $this->displaySwitch )
        {
            return;
        }
        $switchScript = $this->getSwitchScript();
        Yii::app()->clientScript->registerScript('#ecHeaderSwitchScript', $switchScript, CClientScript::POS_READY);
        
        $this->render('_switch');
    }
}