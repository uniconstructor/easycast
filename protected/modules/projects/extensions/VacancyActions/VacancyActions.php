<?php

/**
 * Отображение кнопок-действий для вакансии
 * Отображает кнопки для участника (подать/отозвать заявку) и для админа (закрыть, открыть вакансию и т. п.)
 * 
 * @todo добавить кнопку отмены подачи заявки (кроме заявок, поданных по токену)
 * @todo добавить кнопки для админа
 * @todo включить мозг и придумать общий родительский класс для VacancyActions и MemberActions
 *       (идея в том, чтобы выводить набор кнопок с доступными действиями в зависимости от контекста)
 * @todo добавить проверку всех обязательных параметров в init()
 * @todo добавить возможность подавать заявку тем у кого отключен JS
 * @todo вынести название и контекст события для подачи заявки в параметры виджета
 * @todo не создавать по одному sweekit-событию для каждой кнопки, а сделать его универсальным:
 *       передавать в JS-функцию все необходимые id как параметры
 * @todo вынести вывод обычной кнопки и вывод кнопки для загрузки по AJAX в разные функции
 */
class VacancyActions extends CWidget
{
    /**
     * @var bool - загружаются ли данные виджета через AJAX?
     *             (если да - то выводим скрипты после разметки, иначе они не подключатся)
     */
    public $isAjaxRequest = false;
    /**
     * @var EventVacancy - вакансия, для которой отображаются кнопки
     */
    public $vacancy;
    /**
     * @var string - режим отображения 
     *               normal - для авторизованнх пользователей
     *               token - для подачи заявки по токену
     */
    public $mode = 'normal';
    /**
     * @var EventInvite - приглашение участника, дающее ему право подавать заявку
     */
    public $invite;
    /**
     * @var string - ключ по которому происходит подача заявки (если заявка подается из почты, по ключу)
     */
    public $key;
    /**
     * @var string - id тега, содержащего все AJAX-кнопки
     */
    public $containerId;
    /**
     * @var bool - выдавать ли подтверждение перед подачей заявки?
     */
    public $confirmActions = false;
    /**
     * @var string - Изначально пустая строка.
     *               Задается только если нужно вывести какое-то сообщение сразу же рядом с кнопкой или вместо кнопки
     */
    public $message        = '';
    /**
     * @var string - css-класс сообщения
     */
    public $messageClass   = 'alert alert-info';
    /**
     * @var int - id участника, для которого отбражаются кнопки
     */
    public $questionaryId;
    /**
     * @var string - размер кнопок для действия с ролью
     * @see TbButton
     */
    public $buttonSize;
    /**
     * @var string - дополнительный css класс для всех кнопок
     */
    public $buttonClass = '';
    
    /**
     * @var string - id тега, содержащего текст сообщения
     */
    protected $messageId;  
    /**
     * @var string
     */
    protected $messageStyle = 'display:none;';
    /**
     * @var array - список кнопок, которые нужно отобразить.
     *              'addApplication', 'removeApplication', //'close', 'publish', 'changePrice'
     */
    protected $buttons = array('addApplication'); // 'removeApplication'   
    /**
     * @var Questionary
     */
    protected $questionary;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        switch ( $this->mode )
        {
            case 'normal':
                if ( ! $this->questionaryId )
                {// берем id текущего пользователя, если он не задан вручную
                    $this->questionaryId = Yii::app()->getModule('questionary')->getCurrentQuestionaryId();
                }
            break;
            case 'token':
                $this->questionaryId = $this->invite->questionaryid;
            break;
            default: throw new CException(500, 'Display mode for VacancyActions not set');
        }
        $this->questionary = Questionary::model()->findByPk($this->questionaryId);
        $this->messageId   = 'vacancy_actions_message_'.$this->vacancy->id;
        $this->containerId = 'vacancy_actions_container_'.$this->vacancy->id;
        
        $this->setAllowedButtons();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->message )
        {
            $this->messageStyle = 'display:block;';
        }
        $this->render('actions');
    }
    
    /**
     * Определить, какие кнопки можно показывать, а какие нет
     * (Вызывается из init())
     *
     * @return null
     */
    protected function setAllowedButtons()
    {
        // @todo получаем все доступные для вакансии действия
        //$this->buttons = $this->member->getAllowedStatuses();
        foreach ( $this->buttons as $id => $buttonType )
        {// оставляем только те кнопки, на которые есть права, и для которых не нужно указывать доп. данные
            if ( ( $this->vacancy->needMoreDataFromUser($this->questionary) OR Yii::app()->user->isGuest ) AND
                   $buttonType === 'addApplication' )
            {
                $this->buttons[$id] = 'addApplicationData';
                if ( ! $this->vacancy->event->isExpired() )
                {
                    continue;
                }
            }
            if ( ! $this->isAllowed($buttonType) )
            {
                unset($this->buttons[$id]);
            }
        }
    }
    
    /**
     * Определить, доступна ли кнопка пользователю
     * 
     * @param string $type - тип кнопки
     * @return bool
     */
    protected function isAllowed($type)
    {
        if ( ! $this->isUserAction($type) )
        {// админам можно все кроме подачи и отзыва заявок на вакансию (эта функция только для участников)
            return Yii::app()->user->checkAccess('Admin');
        }else
        {
            if ( Yii::app()->user->checkAccess('Admin') )
            {// не показываем админам кнопки подачи заявок,
                if ( $this->questionaryId != Yii::app()->getModule('user')->user()->questionary->id AND
                     $this->vacancy->isAvailableForUser($this->questionaryId) )
                {// если только это не кнопка подачи заявки от имени другого участника
                    // и этому участнику эта роль доступна
                    return true;
                }else
                {// сами от себя админы заявок не подают, и подать заявку от имени другого участника
                    // если он не подходит по критериям поиска тоже нельзя
                    return false;
                }
            }
        }
        // дальше идет только проверка действий "для участника" (подать/отозвать заявку)
        if ( Yii::app()->user->checkAccess('User') OR $this->mode == 'token' )
        {// участник вошел на сайт или происходит подача заявки по токену 
            if ( $this->vacancy->hasApplication($this->questionaryId) )
            {// участник уже подал заявку - сообщим ему об этом
                $this->message     .= 'Вы уже подали заявку на эту роль';
                $this->messageClass = 'alert alert-block';
            }
            if ( $this->vacancy->isAvailableForUser($this->questionaryId) )
            {// участник еще не подал заявку и проходит по критериям вакансии - покажем кнопку
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Определить, является ли переданный тип кнопки кнопкой только для участника
     * @param string $type - тип кнопки
     * @return null
     */
    protected function isUserAction($type)
    {
        return in_array($type, array('addApplication', 'addApplicationData', 'removeApplication'));
    }
    
    /**
     * Получить HTML-код кнопки
     * 
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButton($type)
    {
        $title       = $this->getButtonTitle($type);
        $url         = $this->getButtonUrl($type);
        $ajaxOptions = $this->getButtonAjaxOptions($type);
        $htmlOptions = $this->getButtonHtmlOptions($type);
        
        if ( $type === 'addApplicationData' )
        {// @todo разделить все кнопки на AJAX и не AJAX
            return CHtml::link($title, $url, $htmlOptions);
        }
        if ( $this->isAjaxRequest )
        {// виджет передается через AJAX: подключить скрипты заранее нет возможности: выводим их за кнопкой
            $buttonLink   = Sweeml::raiseEventUrl('vacancy_action_'.$type.'_'.$this->vacancy->id);
            $button       = Sweeml::link($title, $buttonLink, $htmlOptions);
            $buttonScript = $this->getButtonEventJs($type);
            return $button.$buttonScript;
        }else
        {// виджет отображается обычным способом
            return CHtml::ajaxButton($title, $url, $ajaxOptions, $htmlOptions);
        }
    }
    
    /**
     * Получить скрипты, выполняющиеся при нажатии на кнопку (нужно если виджет передается через AJAX)
     * 
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButtonForAjaxLoad($type)
    {
         // @todo 
    }
    
    /**
     * Получить URL по которому будет происходить AJAX-запрос от кнопки
     * @param string $type - тип кнопки (чаще всего совпадает с выполняемым action-действием)
     *                       
     * @return string
     */
    protected function getButtonUrl($type)
    {
        if ( $type === 'addApplicationData' )
        {
            return Yii::app()->createUrl('/projects/vacancy/registration', array(
                'vid' => $this->vacancy->id,
            ));
        }
        if ( $this->mode === 'normal' )
        {// подача заявки от авторизованного участника
            switch ( $type )
            {
                case 'addApplication':
                    return Yii::app()->createUrl('/projects/vacancy/addApplication', array(
                        'vacancyId' => $this->vacancy->id,
                    ));
                break;
            }
        }elseif ( $this->mode === 'token' )
        {// подача заявки по ключу (по ссылке из почты, без захода на сайт)
            switch ( $type )
            {
                case 'addApplication':
                    return Yii::app()->createUrl('/projects/vacancy/addApplicationByToken', array(
                        'vacancyId' => $this->vacancy->id,
                    ));
                break;
            }
        }
    }
    
    /**
     * Получить уникальный HTML-id для кнопки
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButtonId($type)
    {
        return 'set_'.$type.'_'.$this->vacancy->id;
    }
    
    /**
     * Получить css-класс кнопки в зависимости от ее типа
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButtonClass($type)
    {
        $class = $this->buttonClass.' btn'; 
        if ( $this->buttonSize )
        {
            $class .= ' btn-'.$this->buttonSize;
        }
        switch ( $type )
        {
            case 'addApplication':     $class .= ' btn-success';
            case 'addApplicationData': $class .= ' btn-warning';
            case 'removeApplication':  $class .= ' btn-primary';
        }
        return $class;
    }
    
    /**
     * Получить надпись на кнопке
     * @param string $type - тип кнопки
     * @return string
     * 
     * @todo если кнопка отображается админу, и переданный id анкеты не совпадает с id анкеты админа 
     *       то писать на кнопке подачи заявки "подать заявку от имени ..."
     */
    protected function getButtonTitle($type)
    {
        switch ( $type )
        {
            case 'addApplication':     return 'Подать заявку';
            case 'addApplicationData': return 'Подать заявку';
            case 'removeApplication':  return 'Отозвать заявку';
        }
    }
    
    /**
     * Получить текст предупреждения (во всплывающем окне) перед действием кнопки
     * @param string $type - тип кнопки
     * @return string|bool - возвращает false если подтверждение не требуется
     */
    protected function getButtonConfirm($type)
    {
        if ( ! $this->confirmActions )
        {// подтверждения отключены
            return false;
        }
        switch ($type)
        {
            case 'addApplication':    return false;
            case 'removeApplication': return 'Отозвать заявку?';
        }
        return false;
    }
    
    /**
     * Получить настройки AJAX запроса
     * @param string $type - тип кнопки
     * @return array
     */
    protected function getButtonAjaxOptions($type)
    {
        return array(
            'url'     => $this->getButtonUrl($type),
            'data'    => $this->getButtonPostData($type),
            'type'    => 'post',
            'success' => $this->getButtonSuccessJs($type),
            //'error' =>
            //'beforeSend' => $this->getButtonBeforeSendJs($type),
        );
    }
    
    /**
     * Получить массив параметров POST для AJAX запроса
     * @param string $type - тип кнопки
     * @return array
     */
    protected function getButtonPostData($type)
    {
        $data = array();
        switch ( $type )
        {
            // добавить заявку
            case 'addApplication':
                $data['vacancyId'] = $this->vacancy->id;
                if ( $this->mode === 'token' )
                {// по токену
                    $data['key']      = $this->key;
                    $data['inviteId'] = $this->invite->id;
                }
                if ( $this->questionaryId AND Yii::app()->user->checkAccess('Admin') )
                {// если админ подает заявку от чужого имени - добавим id участника
                    // от имени которого подается заявка
                    $data['questionaryId'] = $this->questionaryId;
                }
            break;
            // отклонить заявку
            case 'removeApplication':
                $data['vacancyId'] = $this->vacancy->id;
            break;
        }
        // CSRF-подпись для выполнения POST-запроса
        $data[Yii::app()->request->csrfTokenName] = Yii::app()->request->csrfToken;
    
        return $data;
    }
    
    /**
     * Получить JS-код, выполняющийся в случае успешного AJAX запроса
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButtonSuccessJs($type)
    {
        $buttonId = $this->getButtonId($type);
        $message  = $this->getSuccessMessage($type);
        
        $js = 'function (data, status) {';
        // скрываем все кнопки
        $js .= "\$('#{$this->containerId}').hide();";
        // показываем сообщение
        $js .= "\$('#{$this->messageId}').text('{$message}');";
        $js .= "\$('#{$this->messageId}').show();}";
    
        return $js;
    }
    
    /**
     * Получить сообщение, появляющееся после успешного AJAX запроса
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getSuccessMessage($type)
    {
        switch ( $type )
        {
            case 'addApplication':    return 'Заявка отправлена';
            case 'removeApplication': return 'Заявка отозвана';
        }
    }
    
    /**
     * Получить HTML-параметры для AJAX-кнопки (если нужно)
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButtonHtmlOptions($type)
    {
        $options = array(
            'id'    => $this->getButtonId($type),
            'class' => $this->getButtonClass($type),
        );
        if ( $confirm = $this->getButtonConfirm($type) )
        {// если требуется подтверждение перед отправкой запроса - зададим его
            $options['confirm'] = $confirm;
        }
        return $options;
    }
    
    /**
     * Получить JS-код, выполняющийся до отправки AJAX-запроса
     * @param string $type - тип кнопки
     * @return string
     *
     * @todo пока не реализовано
     */
    protected function getButtonBeforeSendJs($type)
    {
        return '';
    }
    
    /**
     * Получить скрипт выполняющийся при нажатии кнопки (если виджет загружается по AJAX)
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButtonEventJs($type)
    {
        $ajax = CHtml::ajax($this->getButtonAjaxOptions($type));
        $js   = Sweeml::registerEventScript(
            'vacancy_action_'.$type.'_'.$this->vacancy->id,
            "js:function(x){".$ajax."}"
        );
        return CHtml::script($js);
    }
}