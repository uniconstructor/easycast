<?php

/**
 * Виджет для настройки email-оповещений
 * 
 * @todo проверка параметров в init()
 * @todo проверка того нарушены ли фигурные скобки
 */
class EditMailNotifications extends CWidget
{
    /**
     * @var string - 
     */
    const CONFIG_NAME = 'inviteNotificationList';
    
    /**
     * @var CActiveRecord - модель по данным которой будут создаваться оповещения
     */
    public $model;
    /**
     * @var string
     */
    public $createUrl;
    /**
     * @var string
     */
    public $updateUrl;
    /**
     * @var string
     */
    public $deleteUrl;
    
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var array - все используемые в оповещении блоки 
     */
    protected $activeBlocks = array();
    /**
     * @var array - стандартные блоки письма
     */
    protected $defaultBlocks = array();
    /**
     * @var array - добавленные пользователем блоки
     */
    protected $userBlocks = array();
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        // получаем настройку с текстом оповещения
        $this->config        = $this->model->getConfigObject(self::CONFIG_NAME);
        // вытаскиваем из настройки все блоки письма чтобы было удобнее
        $this->defaultBlocks = $this->config->defaultListItems;
        $this->activeBlocks  = $this->config->selectedListItems;
        $this->userBlocks    = $this->config->userListItems;
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        echo '<div class="row-fluid">';
        echo '<div class="span12">';
        echo '<h2 class="text-center">Настройки оповещений</h2>';
        foreach ( $this->activeBlocks as $blockItem )
        {
            $this->render('block', array(
                'blockItem' => $blockItem,
            ));
        }
        echo '</div>';
        echo '</div>';
        
        // ссылка чтобы сделать все как было
        $restoreUrl = Yii::app()->createUrl('admin/eventVacancy/restoreDefault', array(
            'id' => $this->config->objectid,
            'restoreNotificationConfig' => 1,
        ));
        echo CHtml::link('Восстановить стандартное оповещение', $restoreUrl, array(
            'class'   => 'btn btn-warning btn-large',
            'confirm' => 'Восстановить стандартный вид оповещения?',
        ));
        // ссылка на предпросмотр
        $previewUrl = Yii::app()->createUrl('admin/eventVacancy/restoreDefault', array(
            'id' => $this->config->objectid,
            'restoreNotificationConfig' => 1,
        ));
        echo CHtml::link('Предварительный просмотр', $previewUrl, array(
            'class'  => 'btn btn-success btn-large pull-right',
            'target' => '_blank',
        ));
    }
    
    /**
     * 
     * 
     * @param  EasyListItem $item
     * @return bool
     */
    protected function isDefault($item)
    {
        return $this->config->isDefaultOption($item);
    }
    
    /**
     * 
     * 
     * @param  EasyListItem $item
     * @return array
     */
    protected function getFormOptions($item, $empty=false)
    {
        if ( $empty )
        {// пустые формы должны ссылаться на страницу добавления записи
            $idPrefix = 'after_';
            $url = Yii::app()->createUrl($this->createUrl, array('afterId' => $item->id));
        }else
        {
            $idPrefix = '';
            $url = Yii::app()->createUrl($this->updateUrl, array(
                'id' => $item->id,
            ));
        }
        return array(
            'id'     => $idPrefix.'notify-config-block-form-'.$item->id.'-'.$this->id,
            'method' => 'post',
            'action' => $url,
            'enableAjaxValidation' => true,
        );
    }
    
    /**
     * 
     * @param EasyListItem $blockItem
     * @param bool $empty - создать пустую форму, вставить ее после формы с $blockItem
     * @return void
     */
    protected function printBlockForm($blockItem, $empty=false)
    {
        if ( $empty )
        {
            $idPrefix = 'after_';
            $model    = new EasyListItem;
            $saveUrl  = $this->createUrl;
        }else
        {
            $idPrefix = '';
            $model    = $blockItem;
            $saveUrl  = $this->updateUrl;
        }
        $formOptions     = $this->getFormOptions($blockItem, $empty);
        $nameHtmlOptions = array('id' => $idPrefix.'EasyListItem_name_'.$blockItem->id);
        $dataHtmlOptions = array('id' => $idPrefix.'EasyListItem_data_'.$blockItem->id);
        
        if ( ! $empty AND $this->isDefault($model) )
        {// @todo возможно что запрещать редактировать эти блоки нет никакой необходимости
            $nameHtmlOptions['disabled'] = 'disabled';
            $dataHtmlOptions['disabled'] = 'disabled';
        }
        // форма редактирования блока письма
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', $formOptions);
        if ( $empty )
        {// для формы вставки записи: запоминаем между какими блоками вставить новый
            echo CHtml::hiddenField('afterId', $blockItem->id);
        }else
        {// для формы редактирования записи будем передавать id
            echo CHtml::hiddenField('id', $model->id);
        }
        // заголовок блока
        echo $form->textFieldRow($model, 'name', $nameHtmlOptions);
        // текст блока
        echo $form->redactorRow($model, 'value', array(
            'editorOptions' => array(
                'class'     => 'span6',
                'rows'      => 5,
                'options'   => array('plugins' => array('clips', 'fontfamily'), 'lang' => 'ru')
            ),
            'htmlOptions' => $dataHtmlOptions,
        ), array(
            'hint' => $model->description,
        ));
        
        $saveId = $idPrefix.'save_notify_block_'.$model->id;
        $this->widget('bootstrap.widgets.TbButton', array(
            'url'        => $saveUrl,
            'buttonType' => 'ajaxSubmit',
            'type'       => 'primary',
            'label'      => 'Сохранить',
            'ajaxOptions' => array(
                'method' => 'post',
                'data'       => new CJavaScriptExpression("$('#{$formOptions['id']}').serialize()"),
                'beforeSend' => "function (data, status) { $('#{$saveId}').addClass('btn-disabled'); }",
                'complete'   => "function (data, status) { $('#{$saveId}').removeClass('btn-disabled'); }",
            ),
            'htmlOptions' => array(
                'id' => $saveId,
            ),
        ));
        if ( ! $empty )
        {// кнопка удаления блока
            $this->widget('bootstrap.widgets.TbButton', array(
                'url'         => $this->deleteUrl,
                'buttonType'  => 'ajaxSubmit',
                'type'        => 'danger',
                'label'       => 'Удалить',
                'ajaxOptions' => array(
                    'method'     => 'post',
                    'data'       => array(
                        'id' => $model->id,
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                    ),
                    'beforeSend' => "function (data, status) { $('#config_notify_block_{$model->id}').remove(); }",
                ),
                'htmlOptions' => array(
                    'id'      => 'delete_notify_block_'.$model->id,
                    'confirm' => 'Удалить блок?',
                    'class'   => 'pull-right',
                ),
            ));
        }
        $this->endWidget();
    }
}
/**
$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'type'  => 'success',
    'size'  => 'large',
    'label' => Yii::t('coreMessages', 'save'),
    'icon'  => 'remove white',
    'url'   => 'http://ya.ru',
    // только для типов ajaxLink/ajaxButton
    'ajaxOptions' => array(
        'method' => 'post',
    ),
    'htmlOptions' => array(
        'id' => 'save_questionary',
        'confirm' => '????',
    )
));
 */