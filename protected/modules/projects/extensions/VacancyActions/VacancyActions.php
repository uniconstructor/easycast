<?php

/**
 * Отображение кнопок-действий для вакансии
 * @todo добавить кнопку отмены подачи заявки (кроме заявок, поданных по токену)
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
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $result = '';
        
        if ( ! $this->vacancy->hasApplication($this->invite->questionaryid) )
        {
            $result .= $this->createAddApplicationButton();
        }else
        {
            $result .= '(Заявка подана)';
        }
        
        echo $result;
        echo '<div class="" style="display:none;" id="vacancy_actions_message_'.$this->vacancy->id.'"></div>';
    }
    
    /**
     * Создать кнопку "подать заявку"
     * 
     * @return string
     */
    protected function createAddApplicationButton()
    {
        // Создаем параметры для кнопки
        $url = Yii::app()->createUrl('/projects/vacancy/addApplicationByToken');
        $ajaxOptions = $this->createButtonAjaxOptions('add');
        $htmlOptions = array(
            'class' => 'btn btn-success',
            'id'    => 'add_application_'.$this->vacancy->id);
        
        return CHtml::ajaxButton('Отправить заявку', $url, $ajaxOptions, $htmlOptions);
    }
    
    /**
     * Создать настройки для кнопки с AJAX-запросом добавления или отзыва заявки на вакансию
     * @param string $type - для какой кнопки получить настройки (add, remove)
     * @param ind $vacancyId - id вакансии на которую подает заявку участник
     * @return array
     *
     * @todo настроить beforeSend
     */
    protected function createButtonAjaxOptions($type)
    {
        $ajaxOptions = array(
            'url'  => $this->createButtonAjaxUrl($type),
            'data' => array(
                'vacancyId' => $this->vacancy->id,
                'key'       => $this->key,
                'inviteId'  => $this->invite->id,
                Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken
            ),
            'type'       => 'post',
            'success'    => $this->createAddApplicationSuccessJs(),
            //'error' =>
            //'beforeSend' => $beforeSendJS,
        );
    
        return $ajaxOptions;
    }
    
    /**
     * Получить адрес для ajax-запроса, отправляемого при клике на кнопку
     * @param unknown $type
     * @return string
     */
    protected function createButtonAjaxUrl($type)
    {
        $ection = '';
        switch ( $type )
        {
            case 'add':    $action = 'addApplicationByToken'; break;
            case 'remove': break;
            default: return '#';
        }
    
        return Yii::app()->createUrl('/projects/vacancy/'.$action);
    }
    
    /**
     * Получить JS, выполняющийся после успешной подачи заявки
     * @return string
     * @return null
     */
    protected function createAddApplicationSuccessJs()
    {
        $buttonId    = 'add_application_'.$this->vacancy->id;
        $messageId   = 'vacancy_actions_message_'.$this->vacancy->id;
        $messageText = ProjectsModule::t('application_added');
        
        return "js:function (data, status){
        $('#{$buttonId}').attr('class', 'btn disabled');
        $('#{$buttonId}').attr('disabled', 'disabled');
    
        $('#{$messageId}').attr('class', 'alert alert-success');
        $('#{$messageId}').text('{$messageText}');
        $('#{$messageId}').fadeIn(200);
        }";
    }
}