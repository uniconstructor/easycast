<?php

/**
 * Виджет AJAX-редактирование настройки анкеты
 * 
 * @todo проверки при инициализации
 */
class QUserConfig extends CWidget
{
    /**
     * @var Questionary - 
     */
    public $questionary;
    /**
     * @var string - системное название настройки
     */
    public $configName;
    /**
     * @var string - 
     */
    public $ajaxUrl   = '/questionary/questionary/toggleProjectTypeNotification';
    /**
     * @var string -
     */
    public $eventName = 'toggleItem';
    
    /**
     * @var Config - модель настройки
     */
    protected $config;
    /**
     * @var EasyListItem[] - список всех доступных для выбора значений настройки
     */
    protected $availableItems = array();
    /**
     * @var EasyListItem[] - выбранные пользователем значения
     */
    protected $selectedItems  = array();
    /**
     * @var bool - в первый ли раз настройка редактируется пользователем
     */
    protected $firstEdit;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! $this->questionary )
        {
            $this->questionary = Yii::app()->getModule('questionary')->getCurrentQuestionary();
        }
        if ( ! $this->configName )
        {
            throw new CException('Не передано название настройки');
        }
        if ( ! $this->config = $this->questionary->getConfigObject($this->configName) )
        {
            throw new CException("Настройка с именем '{$this->configName}' не связана с моделью анкеты");
        }
        // определяем сколько всего возможных значений и какие из них выбраны пользователем
        $this->availableItems = $this->config->defaultListItems;
        $this->selectedItems  = $this->config->selectedListItems;
        // определяем в первый ли раз настройка редактируется пользователем
        $this->firstEdit = $this->config->isModifiedFor('Questionary', $this->questionary->id);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $form = new TbForm($this->getMainFormConfig());
        $this->render('config', array('form' => $form));
    }
    
    /**
     * Получить массив для создания формы редактирования настройки
     * 
     * @return array
     */
    protected function getMainFormConfig()
    {
        $elements = array();
        foreach ( $this->availableItems as $item )
        {// для каждого элемента списка создаем форму
            $elements[$item->name] = $this->getItemForm($item);
        }
        // настройки конструктора формы
        return array(
            'title'       => $this->config->title,
            'description' => $this->config->description,
            'elements'    => $elements,
        );
    }
    
    /**
     * Получить форму редактирования одного элемента
     * 
     * @param  EasyListItem $item
     * @return array
     */
    protected function getItemForm($item)
    {
        $buttonValue    = 1;
        $selectedListId = $this->config->selectedList->id;
        $selectedItem   = EasyListItem::model()->withListId($selectedListId)->
            withParentId($item->id)->find();
        if ( $selectedItem )
        {
            $buttonValue = 0;
        }
        $toggleButton = $this->widget('bootstrap.widgets.TbToggleButton', array(
            'name'          => 'pnType_'.$item->value,
            'onChange'      => 'js:function($el, status, e){'.$this->createToggleActionJs($item).'}',
            'enabledLabel'  => 'Да',
            'disabledLabel' => 'Нет',
            'enabledStyle'  => 'primary',
            'disabledStyle' => 'default',
            'width'         => 250,
            'value'         => $buttonValue,
        ), true);
        return array(
            'type'        => 'form',
            //'model'       => $item,
            'title'       => $item->title,
            'description' => $item->description,
            'elements' => array(
                '<div class="row-fluid">',
                //'<div class="span6">',
                $toggleButton,
                //'</div>',
                //'<div class="span6">',
                //'---------',
                //'</div>',
                '</div>',
            ),
        );
    }
    
    /**
     * Создать скрипт AJAX-запроса для изменения настройки
     * 
     * @param  EasyListItem $item
     * @return string
     */
    protected function createToggleActionJs($item)
    {
        // settings - настройки AJAX-запроса перед отправкой
        $beforeSendJs = "function(jqXHR, settings){ console.log('beforeSend'); }";
        // data - пришедший в запрос html
        $successJs    = "function(data, status){ console.log('success');console.log(data); }";
        $completeJs   = "function(data, status){ console.log('complete');console.log(data); }";
        // данные запроса
        $data = array(
            'configName' => $this->configName,
            'objectType' => 'Questionary',
            'objectId'   => $this->questionary->id,
            'itemValue'  => $item->value,
            'itemId'     => $item->id,
            Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken
        );
        $ajax = array(
            'url'        => $this->ajaxUrl,
            'data'       => $data,
            'cache'      => false,
            'type'       => 'post',
            'beforeSend' => $beforeSendJs,
            'success'    => $successJs,
            'complete'   => $completeJs,
        );
        return CHtml::ajax($ajax);
    }
}