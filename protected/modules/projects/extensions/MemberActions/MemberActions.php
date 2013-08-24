<?php

/**
 * Этот виджет выводит AJAX-кнопки, изменяющие статус заявки или участника мероприятия
 * Используется самими участниками, чтобы отменять свои заявки или админами, чтобы подтверждать,
 * отклонять или предварительно одобрять заявки на роли.
 * Этот виджет никогда не виден гостю
 */
class MemberActions extends CWidget
{
    /**
     * @var ProjectMember - заявки или участник проекта, дял которого отображаются кнопки
     */
    public $member;
    
    /**
     * @var array - список кнопок, которые нужно отобразить.
     *              (названия кнопок совпадают с названиями статусов, в которые переходит заявка, так удобнее)
     *              'canceled', 'draft', 'pending', 'active', 'rejected', 'succeed', 'failed'
     */
    protected $buttons = array();    
    
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
        $this->setAllowedButtons();
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
        foreach ( $this->buttons as $button )
        {// отображаем все доступные кнопки
            echo $this->getButton($type).'&nbsp;';
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
        {// оставляем только те, на которые есть права
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
        switch ($type)
        {
            case 'canceled':
            case 'draft':
            case 'pending':
            case 'active':
            case 'rejected':
            case 'succeed':
            case 'failed':
        }
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
     * Получить надпись на кнопке
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButtonTitle($type)
    {
        switch ($type)
        {
            case 'canceled':
            case 'draft':
            case 'pending':
            case 'active':
            case 'rejected':
            case 'succeed':
            case 'failed':
        }
    }
    
    /**
     * Получить настройки AJAX запроса
     * @param string $type - тип кнопки
     * @return array
     */
    protected function getButtonAjaxOptions($type)
    {
        
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
            case 'canceled':
            case 'draft':
            case 'pending':
            case 'active':
            case 'rejected':
            case 'succeed':
            case 'failed':
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
        switch ($type)
        {
            case 'canceled':
            case 'draft':
            case 'pending':
            case 'active':
            case 'rejected':
            case 'succeed':
            case 'failed':
        }
    }
    
    /**
     * Получить HTML-параметры для AJAX-кнопки (если нужно)
     * @param string $type - тип кнопки
     * @return string
     */
    protected function getButtonHtmlOptions($type)
    {
        $options = array();
        
        switch ($type)
        {
            case 'canceled':
            case 'draft':
            case 'pending':
            case 'active':
            case 'rejected':
            case 'succeed':
            case 'failed':
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