<?php
/**
 * Виджет главного меню сайта, который содержит 2 вкладки: для пользователей и для заказчиков
 * @todo перенести алгоритм составления данных для меню пользователя и заказчика в одну функцию
 */
class ECMainMenu extends CWidget
{
    /**
     * @var string - открытая в данный момент вкладка меню 
     *               'customers' - заказчикам
     *               'users'     - участникам
     */
    public $activeTab;
    
    /**
     * @var array - список отображаемых вкладок
     */
    public $displayedTabs = array('users', 'customers');

    /**
     * @var int сколько пунктов меню помещать в одной строке
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
        if ( ! $this->activeTab )
        {// определим, какая вкладка должна быть открыта по умолчанию
            $this->activeTab = 'users';
            if ( Yii::app()->user->isGuest OR Yii::app()->user->checkAccess('User') )
            {// гостям и участникам по умолчанию показывается вкладка участников
                $this->activeTab = 'users';
            }
            if ( Yii::app()->user->checkAccess('Customer') OR Yii::app()->user->checkAccess('Admin') )
            {// заказчикам - их вкладка
                $this->activeTab = 'customers';
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
        
        // Используем стандартный виджет из Twitter Bootstrap чтобы отобразить вкладки
        $this->widget('bootstrap.widgets.TbTabs', array(
               'type'        => 'tabs',
               'placement'   => 'above',
               'tabs'        => $this->getMenuTabs(),
               'htmlOptions' => array('class' => 'easycast-tab-menu easycast-main-menu'),
          ));
    }

    /**
     * Получить содержимое всех вкладок виджета, и установить нужную вкладку активной
     * @return array
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

    /**
     * Получить html-код содержимого вкладки "участникам"
     * @return string
     */
    protected function getUserMenu()
    {
        // Получаем кнопку регистрации (или ссылку на страницу участника)
        $newUser = $this->getNewUserData();

        $items     = array();
        $rowNum    = 0;
        $itemCount = 0;
        
        // Получаем все пункты меню участника
        $customerItems = ECMenuItem::model()->findAll('type = :type', array(':type' => 'user'));
        foreach ( $customerItems as $customerItem )
        {// для каждого пункта меню устанавливаем картинку, ссылку и описание
            $imageOptions = array('id' => 'mainmenu_uitem_'.$customerItem->label.'_image');
            $itemCount++;
            $item = new stdClass();
            $item->link    = $customerItem->link;
            $item->label   = Yii::t('coreMessages', 'mainmenu_item_'.$customerItem->label);
            $item->image   = CHtml::image($this->_assetUrl.'/images/menuitems/'.$customerItem->image, $item->label, $imageOptions);
            $item->target  = $customerItem->linkTarget;
            $item->linkid  = 'mainmenu_item_user_'.$customerItem->label.'_link';
            $item->imageid = 'mainmenu_item_user_'.$customerItem->label.'_image';
            $item->visible = $customerItem->visible;
            
            if ( $itemCount > $this->itemsInRow-1 )
            {// меню пользователя короче чем меню заказчика - одна колонка слева всегда занята
                $rowNum++;
                $itemCount = 0;
            }
            $items[$rowNum][] = $item;
        }
        
        return $this->render('user', array('items'=>$items, 'newUser' => $newUser), true);
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
            $newUser->link   = Yii::app()->createUrl(current(Yii::app()->getModule('user')->registrationUrl));
            $newUser->image  = CHtml::image($this->_assetUrl.'/images/menuitems/new_user.png');
            $newUser->label  = Yii::t('coreMessages', 'mainmenu_item_new_user');
            $newUser->linkid = 'mainmenu_item_newuser_link';
            $newUser->imageid = 'mainmenu_item_newuser_image';
        }else
       {
           $newUser->link   = Yii::app()->getModule('questionary')->profileUrl;
           $newUser->image  = CHtml::image(Yii::app()->getModule('user')->user()->questionary->avatarUrl);
           $newUser->label  = Yii::t('coreMessages', 'my_page');
           $newUser->linkid = 'mainmenu_item_profile_link';
           $newUser->imageid = 'mainmenu_item_profile_image';
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
        
        $items  = array();
        $rowNum = 0;
        $itemCount = 0;
        // Получаем все пункты меню заказчика
        $customerItems = ECMenuItem::model()->findAll('type = :type', array(':type' => 'customer'));
        foreach ( $customerItems as $customerItem )
        {// для каждого пункта меню устанавливаем картинку, ссылку и описание
            if ( $customerItem->label == 'fast_order' )
            {
                $imageOptions = array(
                    'id' => 'mainmenu_item_'.$customerItem->label.'_image',
                    'data-toggle' => 'modal',
                    'data-target' => "#fastOrderModal");
            }else
            {
                $imageOptions = array('id' => 'mainmenu_item_'.$customerItem->label.'_image');
            }
            $itemCount++;
            $item = new stdClass();
            $item->link    = $customerItem->link;
            $item->label   = Yii::t('coreMessages', 'mainmenu_item_'.$customerItem->label);
            $item->image   = CHtml::image($this->_assetUrl.'/images/menuitems/'.$customerItem->image, $item->label, $imageOptions);
            $item->target  = $customerItem->linkTarget;
            $item->linkid  = 'mainmenu_item_'.$customerItem->label.'_link';
            $item->imageid = 'mainmenu_item_'.$customerItem->label.'_image';
            $item->visible = $customerItem->visible;
            
            if ( $itemCount > $this->itemsInRow )
            {
                $rowNum++;
                $itemCount = 0;
            }
            if ( ($customerItem->label == 'my_choice') AND ($orderUsersCount = FastOrder::countPendingOrderUsers()) )
            {// если заказчик выбрал актеров для съемки - покажем их количество
                $item->label .= ' ('.$orderUsersCount.')';
            }
            $items[$rowNum][] = $item;
        }
        
        // отображаем меню заказчика
        $result .= $this->render('customer', array('items'=>$items), true);
        // Добавляем скрипт для кнопки "срочный заказ"
        $result .= $this->widget('ext.ECMarkup.ECFastOrder.ECFastOrder', array(), true);
        
        return $result;
    }
}