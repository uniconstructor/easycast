<?php

/**
 * AJAX-кнопка отключающая новые приглашения на определенный проект или тип проектов
 */
class QNoNewInvitesButton extends CWidget
{
    /**
     * @var Questionary - анкета для которой изменяется настройка оповещений
     */
    public $questionary;
    /**
     * @var string
     */
    public $configName = 'projectTypesBlackList';
    /**
     * @var string
     */
    public $url;
    
    /**
     * @var Config - модель настройки
     */
    protected $config;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! $this->questionary )
        {
            $this->questionary = Yii::app()->getModule('questionary')->getCurrentQuestionary();
        }
        if ( ! $this->config = $this->questionary->getConfigObject($this->configName) )
        {
            throw new CException("Настройка с именем '{$this->configName}' не связана с моделью анкеты");
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        // обработка событий
        $beforeSendJs = "
            $('#{$this->getButtonId()}').prop('disabled', true);
            $('#{$this->getButtonId()}').addClass('disabled');
        ";
        $completeJs = "
            $('#{$this->getButtonId()}').prop('disabled', false);
            $('#{$this->getButtonId()}').removeClass('disabled');
        ";
        $successJs = "$('#{$this->getContainerId()}').html(data);";
        $errorJs   = "jGrowl('".Yii::t('coreMessages', 'ajax_error_try_again')."');";
        // параметры AJAX
        $ajaxOptions = array(
            'method'     => 'post',
            // выключить кнопку когда запрос начался (чтобы пользователи видели что процесс пошел)
            'beforeSend' => "function (jqXHR, settings) { {$beforeSendJs} }",
            // обработать ответ сервера
            'success'    => "function (data, status) { {$successJs} }",
            // включить кнопку обратно после выполнения запроса
            'complete'   => "function (jqXHR, settings) { {$completeJs} }",
            // обработка ошибки, возникшей при запросе
            'error'      => "function (jqXHR, textStatus, errorThrown) { {$errorJs} }",
            // собираем введенные данные формы перед отправкой
            'data'       => new CJavaScriptExpression("$('#{$this->getFormId()}').serialize()"),
        );
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'  => TbButton::BUTTON_AJAXBUTTON,
            'type'        => TbButton::TYPE_DANGER,
            'size'        => TbButton::SIZE_LARGE,
            'label'       => Yii::t('coreMessages', 'save'),
            'icon'        => 'remove white',
            'ajaxOptions' => $ajaxOptions,
            // адрес для AJAX-запроса
            'url' => Yii::app()->createUrl('/questionary/questionary/action'),
            'htmlOptions' => array(
                'id'      => $this->getButtonId(),
                'confirm' => Yii::t('zii', 'Are you sure you want to delete this item?'),
            ),
        ));
    }
    
    /**
     * Получить уникальный id для кнопки
     * 
     * @return string
     */
    protected function getButtonId()
    {
        return $this->id.'_noNewInvitesButton';
    }
    
    /**
     * Получить уникальный id для контейнера кнопки
     *
     * @return string
     */
    protected function getContainerId()
    {
        return $this->id.'_noNewInvitesContainer';
    }
}