<?php
// подключение родительского класса
Yii::import('ext.CdCustomDropDown.CdCustomDropDown');

/**
 * Виджет-информер, используется в шапке на всех страницах
 * 
 * @todo для заказчика выводить полный список выбранных участников, с возможностью просмотреть и удалить каждого
 * @todo для участника цифрами выводить количество приглашений, заявок и съемок
 * @todo писать что анкета ждет проверки или требует указания дополнительных данных, если нужно дополнить данные
 *       то выводить в первом пункте меню сообщение от администратора после проверки
 * @todo пункт меню для гостя-заказчика "срочный заказ"
 * @todo пункт меню для гостя-заказчика "заявка на расчет стоимости"
 * @todo пункт меню для участника "настройки"
 * @todo сделать так чтобы при нажатии на пункт "регистрация" появлялась modal-форма
 * 
 * @todo языковые строки
 */
class ECUserInformer extends CdCustomDropDown
{
    /**
     * @var Questionary
     */
    protected $questionary;
    /**
     * @var string параметры, добавляемые к каждому li пользовательского меню по умолчанию
     */
    protected $defaultItemOptions = array('style' => 'line-height:45px;');
    
    /**
     * @see parent::init()
     */
    public function init()
    {
        if ( ! Yii::app()->user->isGuest )
        {
            $this->questionary = Yii::app()->getModule('user')->user()->questionary;
        }
        // дополнительная настройка стилей меню: ФИО пользователя по центру
        $this->wrapperOptions['class'] .= ' text-center';
        // получаем пункты меню и тутульный пункт
        $this->mainContent = $this->getMainContent();
        $this->items       = $this->getMenuItems();
        // родительский init публикует все нужные скрипты и стили
        parent::init();
    }
    
    /**
     * Получить содержимое информера, которое отображается до раскрытия выпадающего списка
     * @return string
     */
    protected function getMainContent()
    {
        if ( ! Yii::app()->user->isGuest )
        {// если участник зашел на сайт - выводим его имя и аватар
            return $this->getUserLabel();
        }elseif ( $this->isCustomer() )
        {// если заказчик начал набирать актеров - покажем их количество
            return $this->getCustomerLabel();
        }else
        {// для гостей:  
            if ( Yii::app()->getModule('user')->viewMode === 'user' )
            {// участникам пишем слово "гость (участник)"
                return Yii::t('coreMessages', 'guest').' ('.Yii::t('coreMessages', 'user').')';
            }else
            {//заказчикам пишем "гость (заказчик)"
                return Yii::t('coreMessages', 'guest').' ('.Yii::t('coreMessages', 'customer').')';
            }
        }
    }
    
    /**
     * Получить имя и аватар вошедшего пользователя
     * @return void
     */
    protected function getUserLabel()
    {
        $content = '';
        $note    = '';
        $image   = $this->questionary->avatarUrl;
        $avatarOptions = array(
            'style' => 'height:50px;width:50px;border-radius:50%;',
            'class' => 'pull-left',
        );
        
        $avatar  = CHtml::image($image, '', $avatarOptions);
        $content = $avatar.' '.Yii::app()->getModule('user')->user()->fullname;
        
        if ( Yii::app()->user->checkAccess('Admin') )
        {// для админов показываем специальную пометку, и не выводим статус анкеты - он не важен
            $note .= '<small class="text-error">[Администратор]</small>';
        }else
        {// для участников показываем статус анкеты и количество новых приглашений
            switch ( $this->questionary->status )
            {
                case Questionary::STATUS_ACTIVE: 
                    $note .= '<small class="text-success">[Анкета одобрена]</small>';
                break;
                // @todo пока не включаем, чтобы участники не звонили постоянно
                /*case Questionary::STATUS_PENDING: 
                    $note = '<small class="muted">[Анкета ждет проверки]</small>';
                break;
                case Questionary::STATUS_REJECTED: 
                    $note = '<small class="text-warning">[Нужно дополнить анкету]</small>';
                break;*/
            }
        }
        if ( ! $this->questionary->visible )
        {// для скрытых анкет подтвердим лишний раз что она скрыта 
            $note .= '<small class="muted">[Анкета скрыта]</small>';
        }
        if ( $note )
        {
            $content .= '<br>'.$note;
        }
        return $content;
    }
    
    /**
     * Получить количество приглашенных пользователей для гостя-заказчика
     * @return string
     */
    protected function getCustomerLabel()
    {
        $content = '';
        $number  = '<b>'.count(FastOrder::getPendingOrderUsers()).'</b>';
        $counterOptions = array(
            'class' => 'badge badge-info',
        );
        
        $content = CHtml::tag('span', $counterOptions, $number).' Мой выбор';
        return $content.'<br><small class="text-success">Нажмите сюда чтобы закончить отбор</small>';
    }
    
    /**
     * Определить, является ли пользователь заказчиком (он вошел как заказчик
     * или добавил хотя бы 1 анкету в заказ)
     * @return bool
     */
    protected function isCustomer()
    {
        $users = FastOrder::getPendingOrderUsers();
        if ( is_array($users) AND ! empty($users) )
        {// у пользователя есть хотя бы 1 приглашенный человек - значит на сайте заказчик
            return true;
        }
        return Yii::app()->user->checkAccess('Customer');
    }
    
    /**
     * Получить пункты выпадающего меню (в зависимости пользователя)
     * @return array
     */
    protected function getMenuItems()
    {
        if ( Yii::app()->user->checkAccess('Admin') )
        {// показываем меню администратора
            return $this->getAdminItems();
        }elseif ( Yii::app()->user->checkAccess('User') )
        {// показываем меню участника
            return $this->getUserItems();
        }elseif ( $this->isCustomer() )
        {// показываем список выбранных актеров для заказчика
            return $this->getCustomerItems();
        }else
        {// гостю предлагаем вход и регистрацию
            return $this->getGuestItems();
        }
    }
    
    /**
     * Получить пункты меню админа
     * @return array
     */
    protected function getAdminItems()
    {
        $items = array(
            array(
                'url'         => Yii::app()->createUrl('//admin'),
                'icon'        => '<i class="icon-gear"></i>',
                'text'        => 'Панель управления',
                'itemOptions' => $this->defaultItemOptions,
            ),
            array(
                'url'         => Yii::app()->createUrl('//site/index', array('selectState' => 1)),
                'icon'        => '<i class="icon-repeat"></i>',
                'text'        => 'Вернуться на страницу выбора',
                'itemOptions' => $this->defaultItemOptions,
            ),
        );
        $userItems = $this->getUserItems();
        
        return CMap::mergeArray($items, $userItems);
    }
    
    /**
     * Получить пункты меню участника
     * @return array
     */
    protected function getUserItems()
    {
        // ссылка на просмотр своей анкеты
        $profileUrl = Yii::app()->getModule('questionary')->profileUrl;
        $profileUrl = Yii::app()->createUrl($profileUrl, array('id' => $this->questionary->id));
        // ссылка на редактирование своей анкеты
        $editUrl = Yii::app()->createUrl('//questionary/questionary/update', array('id' => $this->questionary->id));
        
        return array(
            array(
                'url'         => $profileUrl,
                'icon'        => '<i class="icon-user"></i>',
                'text'        => Yii::t('coreMessages', 'my_page'),
                'itemOptions' => $this->defaultItemOptions,
            ),
            array(
                'url'         => $editUrl,
                'icon'        => '<i class="icon-edit"></i>',
                'text'        => Yii::t('coreMessages', 'edit'),
                'itemOptions' => $this->defaultItemOptions,
            ),
            array(
                'url'         => Yii::app()->createUrl('//user/logout'),
                'icon'        => '<i class="icon-remove"></i>',
                'text'        => Yii::t('coreMessages', 'logout'),
                'itemOptions' => $this->defaultItemOptions,
            ),
        );
    }
    
    /**
     * Получить пункты меню заказчика
     * @return array
     */
    protected function getCustomerItems()
    {
        $guestItems    = $this->getGuestItems();
        $customerItems = array(
            array(
                'url'         => Yii::app()->createUrl('//catalog/catalog/myChoice'),
                'html'        => '<i class="icon-flag-checkered"></i>Перейти к заказу',
                'itemOptions' => $this->defaultItemOptions,
            ),
        );
        
        return CMap::mergeArray($guestItems, $customerItems);
    }
    
    /**
     * Получить пункты меню гостя
     * @return array
     */
    protected function getGuestItems()
    {
        $loginUrl        = Yii::app()->createUrl(current(Yii::app()->getModule('user')->loginUrl));
        $registrationUrl = Yii::app()->createUrl('//easy');
        $selectStateUrl  = Yii::app()->createUrl('//site/index', array('selectState' => 1));
        
        if ( Yii::app()->getModule('user')->viewMode === 'user' )
        {// участнику предлагаем вход и регистрацию
            $items = array(
                array(
                    'url'         => $loginUrl,
                    'icon'        => '<i class="icon-lock"></i>',
                    'text'        => Yii::t('coreMessages', 'sign_in'),
                    'itemOptions' => $this->defaultItemOptions,
                ),
                array(
                    'url'         => $registrationUrl,
                    'icon'        => '<i class="icon-plus"></i>',
                    'text'        => Yii::t('coreMessages', 'sign_up'),
                    'itemOptions' => $this->defaultItemOptions,
                    /*'linkOptions' => array(
                        'data-target' => '#registration-form',
                        'data-toggle' => 'modal',
                    ),*/
                ),
            );
        }else
        {// заказчику предлагаем прочитать коммерческое предложение 
            $items = array(
                array(
                    'url'         => Yii::app()->createUrl('//sale'),
                    'icon'        => '<i class="icon-thumbs-o-up"></i>',
                    'text'        => 'Посмотреть коммерческое предложение',
                    'itemOptions' => $this->defaultItemOptions,
                    'linkOptions' => array('target' => '_blank'),
                ),
                array(
                    'url'         => $loginUrl,
                    'icon'        => '<i class="icon-lock"></i>',
                    'text'        => Yii::t('coreMessages', 'sign_in'),
                    'itemOptions' => $this->defaultItemOptions,
                ),
            );
        }
        // всем гостям на всякий случай предлагаем вернуться к выбору режима
        $items[] = array(
            'url'         => $selectStateUrl,
            'icon'        => '<i class="icon-repeat"></i>',
            'text'        => 'Вернуться на страницу выбора',
            'itemOptions' => $this->defaultItemOptions,
        );
        
        return $items;
    }
}