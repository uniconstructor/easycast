<?php
/**
 * Виджет, отображающий главную информацию для пользователя:
 * Для гостей - "стать участником" (стать заказчиком)
 * Для тех у кого корзина - количество товаров
 * Для участников - новые приглашения и сообщения
 * 
 * @todo языковые строки
 */
class ECMainInformer extends CWidget
{
    /**
     * @var Questionary
     */
    protected $questionary;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        // публикуем нужные скрипты и стили
        $this->registerClientScript();
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->isCustomer() )
        {// гостю предлагаем стать участником
            $this->printCustomer();
        }elseif ( Yii::app()->user->isGuest )
        {// заказчикам показываем корзину
            $this->render('main');
        }elseif ( $this->isUser() )
        {// участникам показываем сообщения
            $this->questionary = Yii::app()->getModule('user')->user()->questionary;
            $this->printUser();
        }
    }
    
    /**
     * Подключить все CSS и JS
     * @todo сделать min-версию скриптов
     * 
     * @return null
     */
    public function registerClientScript()
    {
        $baseUrl = CHtml::asset(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
        $clientScript = Yii::app()->clientScript;
        
        // register jQuery
        $clientScript->registerCoreScript('jquery');
    
        if ( YII_DEBUG )
        {
            $script = '/ecmaininformer.js';
        }else
        {
            $script = '/ecmaininformer.js';
        }
        // register main js lib
        $clientScript->registerScriptFile($baseUrl . $script);
    
        // register CSS skin
        $clientScript->registerCssFile($baseUrl . DIRECTORY_SEPARATOR . 'ecmaininformer.css');
    }
    
    /**
     * Определить, является ли пользователь заказчиком (он вошел как заказчик
     * или добавил хотя бы 1 анкету в заказ)
     * 
     * @return bool
     */
    protected function isCustomer()
    {
        $users = FastOrder::getPendingOrderUsers();
        if ( is_array($users) AND ! empty($users) )
        {// у пользователя есть хотя бы 1 приглашенный человек - значит на сайте заказчик 
            return true;
        }
        return (Yii::app()->user->isGuest OR Yii::app()->user->checkAccess('Customer'));
    }
    
    /**
     * определить, является ли пользователь участником
     * 
     * @return bool
     */
    protected function isUser()
    {
        return ( Yii::app()->user->checkAccess('User') AND ! Yii::app()->user->checkAccess('Admin') );
    }
    
    /**
     * Отобразить информационный виджет для пользователя: приглашения, заявки и предстоящие съемки
     * 
     * @return null
     */
    protected function printUser()
    {
        // получаем приглашения
        $invites  = $this->getInvites();
        // получаем заявки (ждущие, и отклоненные)
        $requests = $this->getRequests();
        // получаем количество предстоящих съемок
        $events   = $this->getUpcomingEvents();
        
        // выводим виджет
        $this->render('user', array(
            'invites'  => $invites, 
            'requests' => $requests, 
            'events'   => $events,
        ));
    }
    
    /**
     * Отобразить информер заказчика: корзина и кнопка "оформить заказ"
     * 
     * @return null
     */
    protected function printCustomer()
    {
        // получаем содержимое корзины (оно точно есть) и считаем количество людей
        $users = FastOrder::getPendingOrderUsers();
        // выводим виджет
        $this->render('customer', array(
            'users' => $users,
        ));
    }
    
    /**
     * Получить количество приглашений на съемки для текущего участника
     * 
     * @return int
     */
    protected function getInvites()
    {
        return $this->questionary->invitesCount;
    }
    
    /**
     * Получить все активные заявки пользователя
     * 
     * @return number
     */
    protected function getRequests()
    {
        return $this->questionary->requestsCount;
    }
    
    /**
     * Получить количество предстоящих съемок
     * 
     * @return number
     */
    protected function getUpcomingEvents()
    {
        return $this->questionary->upcomingEventsCount;
    }
}