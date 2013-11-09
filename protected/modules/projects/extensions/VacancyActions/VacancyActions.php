<?php

/**
 * Отображение кнопок-действий для вакансии
 * Отображает кнопки для участника (подать/отозвать заявку) и для админа (закрыть, открыть вакансию и т. п.)
 * 
 * @todo добавить кнопку отмены подачи заявки (кроме заявок, поданных по токену)
 * @todo добавить кнопки для админа
 * @todo включить мозг и придумать общий родительский класс для VacancyActions и MemberActions
 * @todo добавить проверку всех обязательных параметров в init()
 */
class VacancyActions extends CWidget
{
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
    public $message = '';
    
    /**
     * @var string - css-класс сообщения
     */
    public $messageClass = 'alert alert-info';
    
    /**
     * @var int - id участника, для которого отбражаются кнопки
     */
    public $questionaryId;
    
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
    protected $buttons = array('addApplication'/*, 'removeApplication'*/);
    
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        switch ( $this->mode )
        {
            case 'normal':
                if ( ! $this->questionaryId )
                {// берем id текущего пользователя, если он не задан вручную
                    $this->questionaryId = Yii::app()->getModule('questionary')->getCurrentUserQuestionaryId();
                }
            break;
            case 'token':
                $this->questionaryId = $this->invite->questionaryid;
            break;
            default: throw new CException(500, 'Display mode for VacancyActions not set');
        }
        
        $this->messageId   = 'vacancy_actions_message_'.$this->vacancy->id;
        $this->containerId = 'vacancy_actions_container_'.$this->vacancy->id;
        
        $this->setAllowedButtons();
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->message )
        {
            $this->messageStyle = 'display:block;';
        }
        $this->render('actions');
        //CVarDumper::dump($this->isAllowed('addApplication'), 10, true);die;
        //CVarDumper::dump(Yii::app()->user->checkAccess('User'), 10, true);die;
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
        {// оставляем только те кнопки, на которые есть права
            if ( ! $this->isAllowed($buttonType) )
            {
                unset($this->buttons[$id]);
            }
        }
    }
    
    /**
     * Определить, доступна ли кнопка пользователю
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
            {// не показываем админам кнопки подачи заявок - они им не нужны
                return false;
            }
        }
        
        // дальше идет только проверка действий "для участника" (подать/отозвать заявку)
        if ( Yii::app()->user->checkAccess('User') OR $this->mode == 'token' )
        {// участник вошел на сайт или происходит подача заявки по токену 
            if ( $this->vacancy->isAvailableForUser($this->questionaryId) )
            {// участник еще не подал заявку и проходит по критериям вакансии - покажем кнопку
                return true;
            }
            if ( $this->vacancy->hasApplication($this->questionaryId) )
            {// участник уже подал заявку - сообщим ему об этом
                $this->message .= 'Вы уже подали заявку на эту роль';
                $this->messageClass = 'alert alert-block';
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
        return in_array($type, array('addApplication', 'removeApplication'));
    }
    
    /**
     * Получить HTML-код кнопки
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButton($type)
    {
        $title       = $this->getButtonTitle($type);
        $url         = $this->getButtonUrl($type);
        $ajaxOptions = $this->getButtonAjaxOptions($type);
        $htmlOptions = $this->getButtonHtmlOptions($type);
    
        return CHtml::ajaxButton($title, $url, $ajaxOptions, $htmlOptions);
    }
    
    /**
     * Получить URL по которому будет происходить AJAX-запрос от кнопки
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButtonUrl($type)
    {
        if ( $this->mode == 'normal' )
        {// подача заявки от авторизованного участника
            switch ($type)
            {
                case 'addApplication': return Yii::app()->createUrl('/projects/vacancy/addApplication');
            }
        }elseif ( $this->mode == 'token' )
        {// подача заявки по ключу
            switch ($type)
            {
                case 'addApplication': return Yii::app()->createUrl('/projects/vacancy/addApplicationByToken');
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
        switch ($type)
        {
            case 'addApplication':    return 'btn btn-success';
            case 'removeApplication': return 'btn btn-primary';
        }
    }
    
    /**
     * Получить надпись на кнопке
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButtonTitle($type)
    {
        switch ($type)
        {
            case 'addApplication':    return 'Подать заявку';
            case 'removeApplication': return 'Отозвать заявку';
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
        switch ($type)
        {
            // добавить заявку
            case 'addApplication':
                $data['vacancyId'] = $this->vacancy->id;
                if ( $this->mode == 'token' )
                {// по токену
                    $data['key']      = $this->key;
                    $data['inviteId'] = $this->invite->id;
                }
            break;
            // отклонить заявку
            case 'removeApplication':
                $data['vacancyId'] = $this->vacancy->id;
            break;
        }
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
        $js .= "$('#{$this->containerId}').hide();";
        // показываем сообщение
        $js .= "$('#{$this->messageId}').text('{$message}');";
        $js .= "$('#{$this->messageId}').show();}";
    
        return $js;
    }
    
    /**
     * Получить сообщение, появляющееся после успешного AJAX запроса
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getSuccessMessage($type)
    {
        switch ($type)
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
}