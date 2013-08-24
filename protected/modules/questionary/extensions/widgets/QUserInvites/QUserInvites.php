<?php

/**
 * Виджет, отображающий все непросмотренные приглашения участника на съемки
 * 
 * @todo языковые строки
 * @todo отображать принятые и отклоненные приглашения, отображать в приглашениях их статус
 * @todo вывести все возможные вакансии с возможностью подать и отозвать заявку
 */
class QUserInvites extends CWidget
{
    /**
     * @var Questionary - анкета для которой отображаются приглашения
     */
    public $questionary;
    
    /**
     * @var string - какие приглашения выводить
     *               new - только новые
     *               old - только удаленные
     */
    public $mode = 'new';
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $this->registerClientScript();
    }
    
    /**
     * Registers the javascript code.
     * 
     * @todo сделать min-версию скриптов
     */
    public function registerClientScript()
    {
        $baseUrl = CHtml::asset(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
        
        $clientScript = Yii::app()->clientScript;
        //register core javascript
        $clientScript->registerCoreScript('jquery');
        
        if ( YII_DEBUG )
        {
            $script = '/quserinvites.js';
        }else
        {
            $script = '/quserinvites.js';
        }
        // register main js lib
        $clientScript->registerScriptFile($baseUrl . $script);
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->mode == 'new' )
        {
            if ( ! $invites = $this->questionary->invites )
            {
                echo '<div class="alert alert-block">Нет новых приглашений</div>';
                return;
            }
        }else
        {
            if ( ! $invites = $this->questionary->oldinvites )
            {
                echo '<div class="alert alert-block">Нет удаленных приглашений</div>';
                return;
            }
        }
        
        foreach ( $invites as $invite )
        {// выводим приглашения по очереди
            $this->render('invite', array(
                'invite' => $invite,
                'mode'   => $this->mode,
            ));
        }
    }
    
    /**
     * Создать ajax-кнопку, принимающую приглашение на съемки
     * 
     * @param EventInvite $invite - отображаемое приглашение на съемки
     * @return string
     */
    protected function createAcceptButton($invite)
    {
        $ajaxOptions = $this->createAjaxOptions('accept', $invite);
        $url = Yii::app()->createUrl('/projects/invite/accept');
        
        return CHtml::ajaxButton('Принять', $url, $ajaxOptions, 
            array(
            'class' => 'btn btn-success pull-left',
            'id'    => 'accept_button'.$invite->id));
    }
    
    /**
     * Создать ajax-кнопку, отклоняющую приглашение
     * 
     * @param EventInvite $invite - отображаемое приглашение на съемки
     * @return string
     */
    protected function createRejectButton($invite)
    {
        $ajaxOptions = $this->createAjaxOptions('reject', $invite);
        $url = Yii::app()->createUrl('/projects/invite/reject');
        
        return CHtml::ajaxButton('Отказаться', $url, $ajaxOptions, 
            array(
                'class' => 'btn btn-primary pull-right',
                'id'    => 'reject_button'.$invite->id));
    }
    
    /**
     * Получить параметры для AJAX-запроса кнопки принятия или отклонения приглашения 
     * @param string $action - производимое действие (accept/reject)
     * @param EventInvite $invite - приглашение на съемки
     * @return array
     * 
     * @todo обработать возможные ошибки
     * @todo сделать красивую темнеющую кнопочку
     */
    protected function createAjaxOptions($action, $invite)
    {
        if ( $action == 'accept' )
        {
            $message = 'Приглашение принято';
        }else
        {
            $message = 'Приглашение отклонено';
        }
        $url = Yii::app()->createUrl('/projects/invite/'.$action);
        
        return array(
            'url'  => $url,
            'data' => array(
                'id' => $invite->id,
                Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken),
            'dataType' => 'json',
            'type'     => 'post',
            'success'  => "js:function() {ec_quinvites_success('{$action}', {$invite->id}, '{$message}');}",
            //'message' => "js:function() {return $('#someid').val();}",
            //'error' => '',
            //'beforeSend' => $beforeSendJS,
        );
    }
}