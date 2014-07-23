<?php

/**
 * Виджет, отображающий все непросмотренные приглашения участника на съемки
 * 
 * @todo языковые строки
 * @todo отображать принятые и отклоненные приглашения, отображать в приглашениях их статус
 * @todo если приглашение принято, но заявка на роль не подана - то отображать приглашение
 *       до тех пор пока мероприятие активно, чтобы участник продолжал видеть список ролей
 * @todo в режиме "old" происходит ошибка sql-запроса. Переписать этот запрос через именованные группы условий
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
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->questionary )
        {
            return;
        }
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
            if ( ! $invite->event->hasVacanciesFor($this->questionary->id) )
            {// не показываем приглашение на событие если в событии нет ни одной подходящей участнику роли
                continue;
            }
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
        $message  = 'Выберите подходящую роль и нажмите кнопку &laquo;подать заявку&raquo;.<br>';
        $message .= 'Можно одновременно подать заявку на несколько ролей.';
        
        return CHtml::link('Посмотреть роли', '#', array(
            'class'   => 'btn btn-success btn-block',
            'id'      => 'accept_button'.$invite->id,
            'onClick'   => "ec_quinvites_success('accept', {$invite->id}, '{$message}');return false;",
        ));
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
        
        return CHtml::ajaxButton('Не участвовать', $url, $ajaxOptions, array(
            'class' => 'btn btn-default btn-block',
            'id'    => 'reject_button'.$invite->id,
        ));
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
        if ( $action === 'accept' )
        {
            $message  = 'Выберите подходящую роль и нажмите кнопку &laquo;подать заявку&raquo;.<br>';
            $message .= 'Если ролей несколько - подать заявку можно на каждую';
        }else
        {
            $message = 'Приглашение отклонено';
        }
        $url = Yii::app()->createUrl('/projects/invite/'.$action);
        
        return array(
            'url'  => $url,
            'data' => array(
                'id' => $invite->id,
                Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
            ),
            'dataType' => 'json',
            'type'     => 'post',
            'success'  => "js:function() {ec_quinvites_success('{$action}', {$invite->id}, '{$message}');}",
        );
    }
}