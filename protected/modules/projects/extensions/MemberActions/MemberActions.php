<?php

/**
 * Этот виджет выводит AJAX-кнопки, изменяющие статус заявки или участника мероприятия
 * Используется самими участниками, чтобы отменять свои заявки или админами, чтобы подтверждать,
 * отклонять или предварительно одобрять заявки на роли.
 * Этот виджет никогда не виден гостю.
 * 
 * @todo языковые строки
 * @todo сообщения
 */
class MemberActions extends CWidget
{
    /**
     * @var ProjectMember - заявки или участник проекта, дял которого отображаются кнопки
     */
    public $member;
    
    /**
     * @var bool - выдавать ли подтверждение перед изменением статуса?
     */
    public $confirmActions = true;
    
    /**
     * @var string - id тега, содержащего все AJAX-кнопки
     */
    public $containerId;
    
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
     * @var array - список кнопок, которые нужно отобразить.
     *              (названия кнопок совпадают с названиями статусов, в которые переходит заявка, так удобнее)
     *              'canceled', 'draft', 'pending', 'active', 'rejected', 'succeed', 'failed'
     */
    protected $buttons = array();
    
    /**
     * @var string - id тега, содержащего текст сообщения
     */
    protected $messageId;
    
    /**
     * @var string 
     */
    protected $messageStyle = 'display:none;';
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! is_object($this->member) )
        {
            throw new CException(500, 'Member not set');
        }
        
        $this->messageId   = 'member_actions_message_'.$this->member->id;
        $this->containerId = 'member_actions_container_'.$this->member->id;
        
        $this->setAllowedButtons();
        $this->setDefaultMessage();
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( Yii::app()->user->isGuest )
        {// виджет никогда не виден гостю
            return;
        }
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
        // получаем все доступные для перехода статусы
        $this->buttons = $this->member->getAllowedStatuses();
        
        foreach ( $this->buttons as $id => $buttonType )
        {// оставляем только те, на которые есть права
            if ( ! $this->isAllowed($buttonType) )
            {
                unset($this->buttons[$id]);
            }
        }
    }
    
    /**
     * Установить сообщение при отображении кнопок
     * @return null
     */
    protected function setDefaultMessage()
    {
        if ( $this->message )
        {// сообщение уже задано извне
            return;
        }
        if ( Yii::app()->user->checkAccess('User') AND Yii::app()->user->id == $this->member->member->user->id )
        {// информационные сообщения для участников
            switch ( $this->member->status )
            {
                case ProjectMember::STATUS_DRAFT:    $this->message = 'Заявка ожидает рассмотрения'; break;
                case ProjectMember::STATUS_PENDING:  $this->message = 'Заявка предварительно одобрена'; break;
                case ProjectMember::STATUS_ACTIVE:   $this->message = 'Заявка одобрена'; break;
                case ProjectMember::STATUS_REJECTED: $this->message = 'Заявка отклонена'; break;
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
        if ( Yii::app()->user->checkAccess('Admin') AND $type != 'canceled' )
        {// админам можно все кроме отмены заявок (эта функция только для участников)
            return true;
        }
        if ( Yii::app()->user->checkAccess('User') )
        {// обычным участникам позволяем только отменять свои заявки
            if ( $type == 'canceled' AND Yii::app()->user->id == $this->member->member->user->id )
            {
                return true;
            }
        }
        
        return false;
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
        return Yii::app()->createUrl('/projects/member/setStatus');
    }
    
    /**
     * Получить уникальный HTML-id для кнопки
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButtonId($type)
    {
        return 'set_'.$type.'_'.$this->member->id;
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
            case 'canceled': return 'btn btn-primary';
            case 'draft':    return 'btn btn-success';
            case 'pending':  return 'btn btn-warning';
            case 'active':   return 'btn btn-success';
            case 'rejected': return 'btn btn-danger';
            case 'succeed':  return 'btn btn-success';
            case 'failed':   return 'btn btn-danger';
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
            case 'canceled': return 'Отозвать заявку';
            case 'draft':    return 'Подать заявку';
            case 'pending':  return 'Предв. одобрить';
            case 'active':   return 'Подтвердить';
            case 'rejected': return 'Отклонить';
            case 'succeed':  return 'Успешно завершена';
            case 'failed':   return 'Неуспешно завершена';
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
            case 'canceled': return 'Отозвать заявку?';
            case 'draft':    return false;
            case 'pending':  return 'Предварительно одобрить эту заявку?';
            case 'active':   return 'Окончательно подтвердить заявку?';
            case 'rejected': return 'Отклонить заявку?';
            case 'succeed':  return 'Пометить заявку успешно завершенной?';
            case 'failed':   return 'Пометить заявку неуспешно завершенной?';
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
        $data['id']     = $this->member->id;
        $data['status'] = $type;
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
            case 'canceled': return 'Заявка отозвана';
            case 'draft':    return 'Заявка отправлена';
            case 'pending':  return 'Заявка предварительно одобрена';
            case 'active':   return 'Участие подтверждено';
            case 'rejected': return 'Заявка отклонена';
            case 'succeed':  return 'Отмечена как успешно завершенная';
            case 'failed':   return 'Отмечена как неуспешно завершенная';
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