<?php

/**
 * Виджет AJAX-редактирование настройки анкеты
 * 
 * @todo проверки при инициализации
 * @todo языковые строки
 */
class QUserConfig extends CWidget
{
    /**
     * @var Questionary - анкета для которой редактируется значения настройки
     */
    public $questionary;
    /**
     * @var string - системное название настройки
     */
    public $configName;
    /**
     * @var string - url по которому происходит изменение значения настройки
     */
    public $ajaxUrl   = '/questionary/questionary/toggleProjectTypeNotification';
    /**
     * @var string - название события по которому происходит обновление значения настройки
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
            throw new CException('Не передано название настройки для отображения виджета');
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
            if ( ! Project::model()->withTypeId($item->id)->visible()->exists() )
            {// пока не было ни одного проекта с таким типом - не предлагаем участнику
                // отказаться от типа проекта про который он еще не знает
                continue;
            }
            if ( $item->value === 'onlinecasting' )
            {// @todo придумать оставить ли этот тип проекта или убрать
                continue;
            }
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
        $selectedListId = $this->config->selectedList->id;
        $selected       = ! EasyListItem::model()->withListId($selectedListId)->
            withParentId($item->id)->exists();
        // AJAX-кнопка смены состояния одного оповещения
        $toggleButton = $this->widget('bootstrap.widgets.TbToggleButton', array(
            'name'          => $this->getItemInputId($item),
            'onChange'      => 'js:function($el, status, e){'.$this->createToggleActionJs($item).'}',
            'enabledLabel'  => 'I',//Yii::t('yii', 'Yes'),
            'disabledLabel' => 'O',//Yii::t('yii', 'No'),
            'enabledStyle'  => 'primary',
            'disabledStyle' => 'danger',
            'width'         => 90,
            'value'         => (int)$selected,
        ), true);
        $toggleButton = CHtml::tag('span', array(/*'class' => 'pull-right'*/), $toggleButton);
        // форма одного элемента, две колонки: слева переключатель, справа иконки проектов
        return array(
            'type'        => 'form',
            'title'       => $toggleButton.' '.$item->title,
            'elements' => array(
                '<div class="row-fluid">',
                '<div class="span6">',
                $item->description,
                '</div>',
                '<div class="span6 text-right">',
                $this->getExampleProjects($item, $selected),
                '</div>',
                '</div>',
            ),
        );
    }
    
    /**
     * Получить примеры проектов указанного типа
     * 
     * @param  EasyListItem $item
     * @param  bool         $selected - включено ли оповещение для этого типа проектов
     * @return string
     */
    protected function getExampleProjects($item, $selected)
    {
        $result   = '';
        $class    = $item->value;
        $projects = Project::model()->withTypeId($item->id)->visible()->
            bestRated()->findAll(array('limit' => 3));
        if ( ! $projects )
        {
            return $result;
        }
        if ( ! $selected )
        {
            $class .= ' grayscale';
        }
        foreach ( $projects as $project )
        {
            $imageUrl   = $project->getAvatarUrl('small');
            $projectUrl = Yii::app()->createUrl('/projects/projects/view', array(
                'id' => $project->id,
            ));
            $title = '<b>'.$project->name.'</b>: <br>'.strip_tags($project->shortdescription, '<br>');
            $image = CHtml::image($imageUrl, $project->name, array(
                'style'       => "height:100px;width:100px;",
                'class'       => $class,
                'title'       => $title,
                'data-toggle' => 'tooltip',
                'data-html'   => true,
            ));
            $logo  = CHtml::link($image, $projectUrl, array(
                'target' => '_blank',
            ));
            $result .= $logo.'&nbsp;';
        }
        return '<div><b>Пример:</b></div>'.$result;
    }
    
    /**
     * Создать скрипт AJAX-запроса для изменения настройки
     * 
     * @param  EasyListItem $item
     * @return string
     */
    protected function createToggleActionJs($item)
    {
        $inputId   = $this->getItemInputId($item);
        $wrapperId = $this->getItemWrapperId($item);
        // settings - настройки AJAX-запроса перед отправкой
        $beforeSendJs = "function(jqXHR, settings) {
            $('#{$inputId}').prop('disabled', true);
            $('#{$wrapperId}').addClass('disabled').addClass('muted');
        }";
        // data - пришедший в запрос html
        $successJs    = "function(data, status){
            $.jGrowl(data);
        }";
        $completeJs   = "function(data, status){
            $('.{$item->value}').toggleClass('grayscale');
            $('#{$inputId}').prop('disabled', false);
            $('#{$wrapperId}').removeClass('disabled').removeClass('muted');
        }";
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
    
    /**
     * 
     * @param  EasyListItem $item
     * @return string
     */
    protected function getItemInputId(EasyListItem $item)
    {
        return 'pnType_'.$item->value;
    }
    
    /**
     * 
     * @param  EasyListItem $item
     * @return string
     */
    protected function getItemWrapperId(EasyListItem $item)
    {
        return 'wrapper-pnType_'.$item->value;
    }
}