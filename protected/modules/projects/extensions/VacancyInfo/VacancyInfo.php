<?php

/**
 * Этот виджет отображает всю доступную информацию по одной роли в зависимости от контекста
 * 
 * @todo добавить проверку входных параметров
 * @todo добавить возможность выводить скрипты после кода, чтобы этот виджет работал при получении через AJAX
 */
class VacancyInfo extends CWidget
{
    /**
     * @var bool - загружаются ли данные виджета через AJAX?
     *             (если да - то выводим скрипты после разметки, иначе они не подключатся)
     */
    public $isAjaxRequest = false;
    /**
     * @var EventVacancy - отображаемая роль
     */
    public $vacancy;
    /**
     * @var string - режим отображения роли
     *               user     - участник
     *               customer - зарегистированный (или зашедший по одноразовой ссылке) заказчик
     *               admin    - админ (как участник или как заказчик)
     *               guest    - гость (участник или заказчик)
     */
    public $displayMode;
    /**
     * @var string - режим подачи заявки
     *               normal - обычный (от авторизованного пользователя)
     *               token  - по ключу (по одноразовой ссылке в письме)
     */
    public $inviteMode = 'normal';
    /**
     * @var Questionary - анкета просматривающего роль участника (только для $this->displayMode = 'user')
     */
    public $questionary;
    /**
     * @var EventInvite - приглашение на съемку (событие), если происходит подписка/отписка по токену
     */
    public $invite;
    /**
     * @var string - ключ приглашения (если происходит подписка/отписка по токену)
     * @todo обязательное поле если указан $invite
     */
    public $key;
    /**
     * @var string - режим просмотра: заказчик (customer) или участник (user)
     */
    public $userMode;
    /**
     * @var string - размер кнопок для действия с ролью
     * @see TbButton
     */
    public $buttonSize;
    /**
     * @var bool - отображать ли список заявок на роль?
     */
    public $displayRequests       = false;
    /**
     * @var bool - отображать ли размер оплаты для этой роли?
     */
    public $displaySalary         = true;
    /**
     * @var bool - если оплата за съемки не предполагается (например это кастинг или некоммерческий проект), 
     *             то выводить ли об этом сообщение?
     */
    public $displayZeroSalary     = true;
    /**
     * @var bool - подходит ли просматривающий роль участник на эту вакансию?
     * @todo когда будет создана таблица, которая хранит информацию о том какой участник на какую роль подходит -
     *       то этот параметр станет не нужен. Сейчас он добавлен только для того чтобы сократить количество
     *       сложных проверок на соответствие / не соответствие критериям роли.
     */
    public $isAvailable           = false;
    /**
     * @var bool - отображать роль участнику если она ему не доступна?
     */
    public $displayNotAvailable   = true;
    /**
     * @var bool - отображать ли завершенные роли (те, на которые уже набрали людей)
     */
    public $displayFinished       = false;
    /**
     * @var bool - Показывать ли роль гостю?
     */
    public $displayToGuest        = true;
    /**
     * @var bool - скрыть ли блок с ролью сразу после подачи заявки?
     */
    public $hideAfterRequest      = false;
    /**
     * @var bool - отображать ли период времени, в который планируется на съемка?
     * @todo пока не введена таблица интервалов времени для событий этот параметр не используется
     */
    public $displayTime           = false;
    /**
     * @var bool - отобразить ли условия, по которым происходит отбор на роль?
     *            (составляется автоматически из условий поиска)
     *             Если указано true - то условия будут видны только админам и заказчикам
     * @todo возможно следует показать их также тем участникам, которые подпадают под эти критерии,
     *       но это сложный вопрос 
     */
    public $displayCriteria       = false;
    /**
     * @var bool - отображать ли в списке те роли, на которые участник подходит по всем параметрам, кроме
     *             оплаты за съемочный день. (То есть предлагать ли ему гарантированно доступные роли, но
     *             с более низкой оплатой чем та, на которую он расчитывал?)
     */
    public $displayLowSalaryRoles = false;
    /**
     * @var bool - отображать ли перед подачей заявки диалоговое окно с подтверждением?
     */
    public $confirmRequest        = false;
    /**
     * @var bool - отображать ли перед отменой (отзывом) заявки диалоговое окно с подтверждением?
     *            (к сведению: заявки нельзя отменить после того как они были хотя бы предварительно одобрены)
     */
    public $confirmCancel         = true;

    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! in_array($this->userMode, array('user', 'customer')) )
        {// определяем режим просмотра сайта (участник/заказчик) если он не задан
            $this->userMode = Yii::app()->getModule('user')->getViewMode();
        }
        if ( ! $this->invite AND $this->questionary AND $this->vacancy )
        {// получаем приглашение по id участника и роли если оно не передано
            $attributes = array(
                'questionaryid' => $this->questionary->id,
                'eventid' => $this->vacancy->event->id,
            );
            $this->invite = EventInvite::model()->findByAttributes($attributes);
        }
        // определяем роль пользователя
        $this->defineDisplayMode();
        
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->needsDisplay() )
        {// роль отображать не нужно - пропускаем ее
            return;
        }
        $this->render('vacancy', array('salary' => $this->getSalary()));
    }
    
    /**
     * отобразить размер оплаты
     * @return string
     */
    protected function getSalary()
    {
        if ( ! $this->displaySalary OR ! $this->vacancy->salary )
        {
            return '';
        }
        if ( $this->displayMode === 'user' OR $this->displayMode === 'admin' )
        {// отображаем размер оплаты только участникам или админам
            return $this->render('_salary', null, true);
        }
    }
    
    /**
     * С учетом всех переданных параметров определяет, отображать ли запись о роли или нет
     * @return bool
     */
    protected function needsDisplay()
    {
        if ( Yii::app()->user->isGuest AND ! $this->displayToGuest )
        {// не показываем роль гостю, если это явно запрещено
            return false;
        }
        if ( ! $this->displayFinished AND $this->vacancy->status === EventVacancy::STATUS_FINISHED )
        {// роль закрыта и закрытые роли запрещено показывать
            return false;
        }
        if ( $this->displayMode === 'user' AND ! $this->isAvailable AND ! $this->displayNotAvailable )
        {// участник не подходит на роль и неподходящие роли запрещено показывать
            return false;
        }
        if ( ! $this->vacancy )
        {// не переданы все данные для отображения
            return false;
        }
        return true;
    }
    
    /**
     * Определить режим просмотра виджета, если он не задан
     * @return void
     */
    protected function defineDisplayMode()
    {
        if ( $this->displayMode )
        {// режим задан
            return;
        }
        // по умолчанию считаем всех гостями что бы ни случилось :)
        $this->displayMode = 'guest';
        if ( Yii::app()->user->checkAccess('Admin') )
        {// это админ
            if ( $this->userMode == 'customer' )
            {// админ в рещиме заказчика видит почти все как у заказчика
                $this->displayMode = 'customer';
            }else
            {// админ в системе участника видит все, но не может подать ни одной заявки
                $this->displayMode = 'admin';
            }
        }elseif ( Yii::app()->user->checkAccess('Customer') )
        {// это заказчик
            $this->displayMode = 'customer';
        }elseif ( Yii::app()->user->checkAccess('User') )
        {// это участник
            $this->displayMode = 'user';
        }
    }
    
    /**
     * Получить HTML-код кнопок подписки и отписки
     * @param EventVacancy $vacancy - вакансия для которой создается кнопка
     * @return string - html-код кнопок
     */
    protected function createActionButtons()
    {
        if ( $this->displayMode === 'customer' )
        {// кнопку "подать заявку" видят только участники и админы (когда подают заявку от чужого имени)
            return '';
        }
        return $this->widget('projects.extensions.VacancyActions.VacancyActions', array(
            'isAjaxRequest' => $this->isAjaxRequest,
            'questionaryId' => $this->questionary->id,
            'buttonSize'    => $this->buttonSize,
            'vacancy'       => $this->vacancy,
            'mode'          => $this->inviteMode,
            'invite'        => $this->invite,
            'key'           => $this->key,
        ), true);
    }
    
    /**
     * Получить кнопку для просмотра отправленного участнику письма
     * (для админов и для тестирования)
     * 
     * @return string
     * 
     * @todo кнопка повторной отправки приглашения
     */
    protected function createPreviewButton()
    {
        if ( ! Yii::app()->user->checkAccess('Admin') )
        {
            return '';
        }
        $previewUrl = Yii::app()->createAbsoluteUrl('/mailComposer/mail/webVersion', array(
            'action' => 'newInvite',
            'id'     => $this->invite->id,
        ));
        return CHtml::link('Посмотреть приглашение', $previewUrl, array(
            'class'  => 'btn btn-default btn-small',
            'target' => '_blank',
        ));
    }
}