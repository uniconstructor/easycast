<?php

/**
 * Этот виджет выводит AJAX-кнопки, изменяющие статус заявки или участника мероприятия
 * Используется самими участниками, чтобы отменять свои заявки или админами, чтобы подтверждать,
 * отклонять или предварительно одобрять заявки на роли.
 * Этот виджет никогда не виден гостю.
 * 
 * @todo языковые строки
 * @todo сообщения
 * @todo включить мозг и придумать общий родительский класс для VacancyActions и MemberActions
 */
class MemberActions extends CWidget
{
    /**
     * @var ProjectMember - заявка или участник проекта, для которого отображаются кнопки
     */
    public $member;
    /**
     * @var CustomerInvite - приглашение заказчика для отбора актеров
     *                       (для случаев, когда происходит отбор актеров по одноразовой ссылке)
     */
    public $customerInvite;
    /**
     * @var bool - выдавать ли подтверждение перед изменением статуса?
     */
    public $confirmActions     = true;
    /**
     * @var bool - показывать ли доступные кнопки действий заново после изменения статуса заявки
     */
    public $refreshButtons     = false;
    /**
     * @var bool - всегда ли отображать текущий статус заявки над действиями?
     */
    public $forceDisplayStatus = false;
    /**
     * @var string - id тега, содержащего все AJAX-кнопки
     */
    public $containerId;
    /**
     * @var string - Сообщение над кнопками (изначально пустая строка)
     *               Задается только если нужно вывести какое-то сообщение сразу же рядом с кнопкой или вместо кнопки
     */
    public $message = '';
    /**
     * @var string - css-класс сообщения
     */
    public $messageClass = 'alert ';
    /**
     * @var string
     */
    public $displayMode = 'column';
    
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
     *              (названия кнопок совпадают с названиями статусов, в которые переходит заявка, так удобнее)
     *              'canceled', 'draft', 'pending', 'active', 'rejected', 'succeed', 'failed'
     */
    protected $buttons = array();
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! is_object($this->member) )
        {
            throw new CException(500, get_class($this).': Member not set');
        }
        
        $this->messageId   = 'member_actions_message_'.$this->member->id;
        $this->containerId = 'member_actions_container_'.$this->member->id;
        
        $this->setAllowedButtons();
        $this->setDefaultMessage();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( Yii::app()->user->isGuest AND ! ( $this->customerInvite instanceof CustomerInvite ) )
        {// виджет никогда не виден гостю, только если он не зашел по одноразовой ссылке
            // return '<!-- MemberActions is empty for guest -->';
            return '';
        }
        $this->render($this->displayMode);
        if ( Yii::app()->user->checkAccess('Admin') AND ! $this->customerInvite )
        {
            $this->widget('admin.extensions.ChangeMemberVacancy.ChangeMemberVacancy', array(
                'member' => $this->member,
            ));
        }
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
        {// оставляем только те кнопки, на которые есть права
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
        if ( ! $this->needDisplayStatusMessage() )
        {// сообщение отображать не нужно
            return;
        }
        switch ( $this->member->status )
        {// отображаем текущий статус заявки
            case ProjectMember::STATUS_DRAFT:    
                $this->message       = 'Заявка ожидает рассмотрения';
                $this->messageClass .= 'alert-info';
            break;
            case ProjectMember::STATUS_PENDING:  
                $this->message       = 'Заявка предварительно одобрена';
                $this->messageClass .= 'alert-warning';
            break;
            case ProjectMember::STATUS_ACTIVE:   
                $this->message       = 'Заявка одобрена';
                $this->messageClass .= 'alert-success';
            break;
            case ProjectMember::STATUS_REJECTED: 
                $this->message       = 'Заявка отклонена';
                $this->messageClass .= 'alert-danger';
            break;
        }
        
        $item = StatusHistory::model()->forObject('project_member', $this->member->id)->getLastItem();
        if ( ($this->customerInvite OR Yii::app()->user->checkAccess('Admin')) AND $item AND $item->getSourceName() )
        {// если есть информация о смене статуса - то отображаем кто ее изменил
            $this->message .= '['.$item->getSourceName().'] '.date('Y-m-d H:i', $item->timecreated);
        }
        if ( $this->message )
        {
            $this->messageStyle = 'display:block;';
        }
    }
    
    /**
     * Определить, нужно ли текущему отображать информацию о том в каком статусе сейчас заявка
     * @return bool
     */
    protected function needDisplayStatusMessage()
    {
        if ( $this->forceDisplayStatus )
        {// отображаем статус, потому что так указано в настройках виджета
            return true;
        }
        if ( Yii::app()->user->checkAccess('User') AND Yii::app()->user->id == $this->member->member->user->id )
        {// участнику в своей анкете всегда показывается статус заявки
            return true;
        }
        return false;
    }
    
    /**
     * Определить, доступна ли кнопка пользователю
     * @param string $type - тип кнопки (совпадает с будущим статусом заявки)
     * @return bool
     */
    protected function isAllowed($type)
    {
        if ( $this->isAllowedByToken($type) )
        {// используется одноразовая ссылка
            return true;
        }
        if ( $type == 'finished' OR $type == 'succeed' OR $type == 'failed' )
        {// @todo доработать ручную установку этих статусов
            return false;
        }
        if ( Yii::app()->user->checkAccess('Admin') AND $type != 'canceled' )
        {// админам можно все кроме отмены заявок (эта функция только для участников)
            return true;
        }
        if ( Yii::app()->user->checkAccess('User') )
        {// обычным участникам позволяем только отменять свои заявки
            if ( $type == 'canceled' AND Yii::app()->user->id == $this->member->member->user->id )
            {// @todo временно запрещаем отзывать заявки
                return false;//return true;
            }
        }
        
        return false;
    }
    
    /**
     * Определить, доступна ли кнопка по одноразовой ссылке
     * @param string $type - тип кнопки (совпадает с будущим статусом заявки)
     * @return bool
     * 
     * @todo проверить статус приглашения заказчика
     */
    protected function isAllowedByToken($type)
    {
        if ( ! ( $this->customerInvite instanceof CustomerInvite ) )
        {// одноразовая ссылка не используется
            return false;
        }
        // по одноразовой ссылке заказчику разрешено подтверждение, отклонение и предварительное одобрение заявок
        $types = array(ProjectMember::STATUS_PENDING, ProjectMember::STATUS_ACTIVE, ProjectMember::STATUS_REJECTED);
        if ( in_array($type, $types) )
        {
            return true;
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
        $class = 'btn ';
        if ( $this->displayMode == 'column' )
        {
            $class .= 'btn-block ';
        }
        switch ( $type )
        {
            case 'canceled': $class .= ' btn-primary'; break;
            case 'draft':    $class .= ' btn-success'; break;
            case 'pending':  $class .= ' btn-warning'; break;
            case 'active':   $class .= ' btn-success'; break;
            case 'rejected': $class .= ' btn-danger'; break;
            case 'succeed':  $class .= ' btn-success'; break;
            case 'failed':   $class .= ' btn-danger'; break;
        }
        return $class;
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
        $options = array(
            'url'     => $this->getButtonUrl($type),
            'data'    => $this->getButtonPostData($type),
            'type'    => 'post',
            //'error' => '',
            //'beforeSend' => $this->getButtonBeforeSendJs($type),
        );
        if ( $this->refreshButtons )
        {// нужно заново показать доступные кнопки после совершения действия
            $options['update'] = '#'.$this->containerId;
        }else
        {// не показываем кнопки после совершения действия (только после перезагрузки страницы)
            $options['success'] = $this->getButtonSuccessJs($type);
        }
        return $options;
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
        if ( $this->customerInvite )
        {// для работы с заявками по токену нужно передавать дополнительные параметры
            $data['ciid'] = $this->customerInvite->id;
            $data['k1']   = $this->customerInvite->key;
            $data['k2']   = $this->customerInvite->key2;
        }
        if ( $this->refreshButtons )
        {
            $data['refresh'] = 1;
        }
        
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