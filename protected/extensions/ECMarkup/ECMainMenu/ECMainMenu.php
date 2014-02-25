<?php
/**
 * Виджет главного меню сайта, который содержит 2 вкладки: для пользователей и для заказчиков
 * @todo удалить таблицу с пунктами меню - мы от казались от идеи добавлять их вручную
 */
class ECMainMenu extends CWidget
{
    /**
     * @var string - открытая в данный момент вкладка меню 
     *               'customer' - заказчикам
     *               'user'     - участникам
     */
    public $activeTab = 'user';
    /**
     * @var array - список отображаемых вкладок
     * @deprecated больше не используется - теперь у нас отображаются разные главные страницы
     *             для заказчика и для участника
     * @todo удалить при рефакторинге
     */
    public $displayedTabs = array('user', 'customer');
    /**
     * @var int сколько пунктов меню помещать в одной строке
     * @deprecated больше не используется
     * @todo удалить при рефакторинге
     */
    public $itemsInRow = 5;
    /**
     * @var string - ссылка на папку с ресурсами расширения
     */
    protected $_assetUrl;
    
    /**
     * Подготавливает виджет к запуску
     */
    public function init()
    {
        // загружаем модель для работы с пунктами меню
        Yii::import('ext.ECMarkup.ECMainMenu.models.ECMenuItem');
        // Загружаем стили главного меню
        $this->_assetUrl = Yii::app()->assetManager->publish(
            Yii::app()->extensionPath . DIRECTORY_SEPARATOR .
                'ECMarkup' . DIRECTORY_SEPARATOR .
                'ECMainMenu' . DIRECTORY_SEPARATOR .
                'assets'   . DIRECTORY_SEPARATOR);
        Yii::app()->clientScript->registerCssFile($this->_assetUrl.'/css/mainmenu.css');
        // устанавливаем активную вкладку по умолчанию
        $this->setActiveTab();
    }
    
    /**
     * Установить активную вкладку по умолчанию
     */
    protected function setActiveTab()
    {
        if ( $lastState = Yii::app()->getModule('user')->getViewMode() )
        {
            $this->activeTab = $lastState;
        }
        if ( ! $this->activeTab )
        {// определим, какая вкладка должна быть открыта по умолчанию
            $this->activeTab = 'user';
            if ( Yii::app()->user->isGuest OR Yii::app()->user->checkAccess('User') )
            {// гостям и участникам по умолчанию показывается вкладка участников
                $this->activeTab = 'user';
            }
            if ( Yii::app()->user->checkAccess('Customer') OR Yii::app()->user->checkAccess('Admin') )
            {// заказчикам - их вкладка
                $this->activeTab = 'customer';
            }
        }
    }
    
    /**
     * Определить, нужно ли показывать содержимое вкладки
     * @param string $tab - название вкладки (users/customers)
     * @return bool
     */
    protected function shoudDisplayTab($tab)
    {
        if ( Yii::app()->user->isGuest OR Yii::app()->user->checkAccess('Admin') )
        {// гостям и админам всегда показываются обе вкладки
            return true;
        }
        
        switch ( $tab )
        {
            case 'users':
                if ( Yii::app()->user->checkAccess('Customer') )
                {// заказчикам не показывается вкладка участников
                    return false;
                }
            break;
            case 'customers':
                if ( Yii::app()->user->checkAccess('User') )
                {// участникам не показывается вкладка заказчиков
                    return false;
                }
            break;
        }
        // во всех не учтенных случаях лучше на всякий случай показать меню - хуже не будет
        return true;
    }

    /**
     * Отображает меню пользователя или заказчика
     */
    public function run()
    {
        switch ( $this->activeTab )
        {
            case 'user':     $this->getUserMenu(); break;
            case 'customer': $this->getCustomerMenu(); break;
        }
    }

    /**
     * Получить html-код содержимого вкладки "участникам"
     * @return string
     */
    protected function getUserMenu()
    {
        // Получаем кнопку регистрации (или ссылку на страницу участника)
        $newUser = $this->getNewUserData();
                
        echo $this->render('user', array('newUser' => $newUser), true);
    }
    
    /**
     * Получить данные для отображения картинки пользователя или кнопки регистрации.
     * Если пользователь не авторизован - то вместо его аватара и ссылки на его страницу
     * показывается кнопка "Стать участником" и ссылка на регистрацию
     * 
     * @return stdClass
     */
    protected function getNewUserData()
    {
        $newUser = new stdClass();
        $newUser->target = '_self';
        if ( Yii::app()->user->isGuest )
        {
            $newUser->link    = Yii::app()->createUrl(current(Yii::app()->getModule('user')->registrationUrl));
            $newUser->image   = CHtml::image($this->_assetUrl.'/images/menuitems/new_user.png');
            $newUser->label   = Yii::t('coreMessages', 'mainmenu_item_new_user');
            $newUser->linkid  = 'mainmenu_item_newuser_link';
            $newUser->imageid = 'mainmenu_item_newuser_image';
            $newUser->modalOptions = ' data-toggle="modal" data-target="#registration-modal"';
        }else
        {
            $newUser->link    = Yii::app()->getModule('questionary')->profileUrl;
            $newUser->image   = CHtml::image(Yii::app()->getModule('user')->user()->questionary->avatarUrl);
            $newUser->label   = Yii::t('coreMessages', 'my_page');
            $newUser->linkid  = 'mainmenu_item_profile_link';
            $newUser->imageid = 'mainmenu_item_profile_image';
            $newUser->modalOptions = '';
        }
        
        return $newUser;
    }

    /**
     * Получить html-код содержимого вкладки "заказчикам"
     * @return string
     * 
     * @todo извлекать записи с сортировкой по order
     */
    protected function getCustomerMenu()
    {
        $result = '';
        
        // отображаем меню заказчика
        $result .= $this->render('customer', array(), true);
        // Добавляем скрипт для кнопки "срочный заказ"
        $result .= $this->widget('ext.ECMarkup.ECFastOrder.ECFastOrder', array(), true);
        
        echo $result;
    }
    
    /**
     * Получить содержимое всех вкладок виджета, и установить нужную вкладку активной
     * @return array
     * @deprecated
     * @todo удалить при рефакторинге
     */
    protected function getMenuTabs()
    {
        $tabs = array();
    
        // Вкладка "участникам"
        if ( $this->shoudDisplayTab('users') )
        {
            $tabs['users'] = array(
                'label' => Yii::t('coreMessages', 'user_menu_label'),
                'content' => $this->getUserMenu(),
            );
        }
    
        // Вкладка "заказчикам"
        if ( $this->shoudDisplayTab('customers') )
        {
            $tabs['customers'] = array(
                'label' => Yii::t('coreMessages', 'customer_menu_label'),
                'content' => $this->getCustomerMenu(),
            );
        }
    
        // Делаем нужную вкладку активной
        $tabs[$this->activeTab]['active'] = true;
    
        return $tabs;
    }
}