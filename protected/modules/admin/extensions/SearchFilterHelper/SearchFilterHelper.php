<?php

/**
 * Виджет для админов: отображает точный результат применения критериев поиска к одной анкете
 * Показывает какие поля анкеты подходят по критериям поиска а какие нет
 * 
 * Может использоваться для проверки критериев роли из анкеты участника так и для 
 * проверки подходящих участников со страницы роли, а также с любыми объектами,
 * которые используют критерии поиска
 * 
 * @todo расширить функционал этого виджета: применять его не только для анкет и ролей, но и для
 *       любых моделей системы, к которым могут быть применены критерии поиска.
 *       Для этого создать родительский класс, который решает эту задачу в общем виде,
 *       а этот виджет наследовать от него
 * @todo подгружать название и фото для роли и для участника
 */
class SearchFilterHelper extends CWidget
{
    /**
     * @var int
     */
    public $questionaryId = 0;
    /**
     * @var int
     */
    public $vacancyId     = 0;
    /**
     * @var string - адрес по которому происходит запрос проверки условий
     */
    public $checkUrl = '/admin/questionary/forceCheck';
    /**
     * @var string - адрес по которому происходит приглашение участника вручную 
     *               (без учета критериев поиска)
     */
    public $forceInviteUrl = '/admin/questionary/forceInvite';
    /**
     * @var string - адрес по которому происходит подача заявки от имени участника
     */
    public $forceSubscribeUrl = '/admin/questionary/forceSubscribe';
    
    /**
     * @var Questionary
     */
    protected $questionary;
    /**
     * @var EventVacancy
     */
    protected $vacancy;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( $this->vacancyId )
        {
            $this->vacancy = EventVacancy::model()->findByPk($this->vacancyId);
        }
        if ( $this->questionaryId )
        {
            $this->questionary = Questionary::model()->findByPk($this->questionaryId);
        }
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('helper');
    }
    
    /**
     * 
     * @param unknown $type
     * @return string
     */
    protected function getAjaxButton($type)
    {
        $beforeSendJs  = "$('#{$this->getButtonId($type)}').prop('disabled', true); ";
        //$beforeSendJs .= "$('#{$this->getButtonId($type)}').addClass('btn-danger');";
        
        $successJs     = "$('#{$this->getResultId($type)}').html(data);";
        
        $completeJs    = "$('#{$this->getButtonId($type)}').prop('disabled', false); ";
        //$completeJs   .= "$('#{$this->getButtonId($type)}').removeClass('btn-danger');";
        
        switch ( $type )
        {
            case 'check':
                $url   = Yii::app()->createUrl($this->checkUrl);
                $type  = 'primary';
                $label = 'Вычислить соответствие критериям';
                $buttonOptions = array(
                    'id'    => $this->getButtonId($type),
                    'class' => 'btn',
                );
                //$successJs .= "$('#search-helper-vacancy-name-{$this->id}').html(data);";
            break;
            case 'invite':
                $url   = Yii::app()->createUrl($this->forceInviteUrl);
                $type  = 'default';
                $label = 'Выслать приглашение этому участнику';
                $buttonOptions = array(
                    'id'      => $this->getButtonId($type),
                    'class'   => 'btn btn-warning',
                    'confirm' => 'Выслать приглашение этому участнику? 
                        Приглашенный вручную участник сможет подать заявку на роль 
                        независимо от критериев поиска.',
                );
            break;
            case 'subscribe': 
                $url   = Yii::app()->createUrl($this->forceSubscribeUrl);
                $type  = 'default';
                $label = 'Подать заявку от этого участника';
                $buttonOptions = array(
                    'id'      => $this->getButtonId($type),
                    'class'   => 'btn btn-success',
                    'confirm' => 'Подать заявку от имени этого участника? Заявка от этого участника
                    будет принята даже если он не подходит по критериям роли. Если в анкете участника
                    недостаточно данных для подачи заявки - заявка все равно будет создана, но участнику
                    придет письмо с просьбой дополнить данные.',
                );
            break;
            default: throw new CException('Не передан тип кнопки');
        }
        
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'ajaxSubmit',
            'size'       => 'large',
            'label'      => $label,
            'url'        => $url,
            'ajaxOptions' => array(
                'method'     => 'post',
                 // выключить кнопку когда запрос начался (чтобы пользователи видели что процесс пошел)
                'beforeSend' => "function (jqXHR, settings) { {$beforeSendJs} }",
                // обработать ответ сервера 
                'success'    => "function (data, status) { {$successJs} }",
                // включить кнопку обратно после выполнения запроса
                'complete'   => "function (jqXHR, settings) { {$completeJs} }",
                // собираем введенные данные формы перед отправкой
                'data'       => new CJavaScriptExpression("$('#{$this->getFormId()}').serialize()"),
            ),
            'htmlOptions' => $buttonOptions,
        ));
    }
    /**
     * 
     * 
     * @return array
     */
    protected function getDefaultResult()
    {
        if ( ! $this->questionary OR ! $this->vacancy )
        {
            return '';
        }
        
        $this->widget('admin.extensions.SearchFilterCompare.SearchFilterCompare', array(
            'questionary' => $this->questionary,
            'vacancy'     => $this->vacancy,
        ));
        if ( $this->vacancy->hasApplication($this->questionary->id) )
        {
            $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'type'    => 'info',
                'message' => 'Участник уже подал заявку на эту роль',
            ), true);
        }
        if ( $this->vacancy->isAvailableForUser($this->questionary->id, true) )
        {
            $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'type'    => 'success',
                'message' => 'Все поля анкеты участника соответствуют критериям роли',
            ), true);
        }else
        {
            $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'type'    => 'danger',
                'message' => 'Участник не подходит подходит по критериям роли',
            ), true);
        }
    }
    /**
     * 
     * @return string
     */
    protected function getFormId()
    {
        return 'search-helper-form-'.$this->id;
    }
    /**
     * 
     * @return string
     */
    protected function getResultId($type)
    {
        return $type.'-search-helper-result-'.$this->id;
    }
    /**
     * 
     * @return string
     */
    protected function getButtonId($type)
    {
        return $type.'Button-'.$this->id;
    }
}