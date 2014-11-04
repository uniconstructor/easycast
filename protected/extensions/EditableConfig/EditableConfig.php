<?php

/**
 * Виджет редактирования настроек привязанных к любому объекту.
 * Предназначен для использования с моделями хранящими настройки в моделях Config.
 * 
 * @todo документировать все поля и методы
 * @todo решить, нужно ли наследовать или совмещать с EditableGrid
 * @todo проверки перед запуском виджета
 * @todo родительский класс от которого будут наследоваться специальные виджеты настроек
 */
class EditableConfig extends CWidget
{
    /**
     * @var string - тип объекта для которого отображаются настройки
     */
    public $objectType = 'system';
    /**
     * @var int - id объекта для которого отображаются настройки
     */
    public $objectId   = 0;
    /**
     * @var array - массив определяющий какие настройки какими виджетами редактировать
     *              Используется когда нужны особые виджеты для особых настроек
     *              Ключом всегда является название (name) настройки а значением - 
     *              массив, в котором ключом является класс виджета а содержимым 
     *              массив параметров инициализации 
     */
    public $widgetMap = array();
    /**
     * @var string - url по которому происходит удаление значений настройки
     */
    public $deleteUrl;
    /**
     * @var string - url по которому происходит создание значений настройки
     */
    public $createUrl;
    /**
     * @var string - url по которому происходит обновление значений настройки
     */
    public $updateUrl;
    /**
     * @var string - url по которому происходит обновление модели самой настройки
     */
    public $updateObjectUrl;
    /**
     * @var CDbCriteria - дополнительные условия выборки настроек внутри набора ограниченного
     *                    парой objecttype/objectid
     * 
     * @todo пока не используется
     */
    public $extraCriteria;
    /**
     * @var string - заголовок списка настроек
     */
    public $title  = 'Настройки';
    /**
     * @var string - тип отображения списка настроек
     *               'full'
     *               'short'
     */
    public $display = 'full';
    /**
     * @var array - список настроек, которые не отобразятся в списке
     */
    public $hiddenItems = array();
    
    /**
     * @var array - полный список настроек
     * 
     * @todo я просто хочу видеть здесь именованную коллекцию вместо массива. Когда-нибудь.
     */
    protected $configItems;
    /**
     * @var CActiveRecord - модель к которой прикреплены настройки (если они прикреплены к модели)
     *                      Содержит null для корневых и системных настроек
     */
    protected $model;
    /**
     * @var bool - содержит ли указанный набор хотя бы одну настройку?
     */
    protected $hasConfig = false;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        // получаем все настройки объекта
        $this->configItems = Config::model()->forObject($this->objectType, $this->objectId)->findAll();
        // @todo загружаем классы виджетов отдельных настроек
        if ( ! $this->configItems )
        {// в наборе нет ни одной настройки
            return;
        }
        $this->hasConfig = true;
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->hasConfig )
        {// не нашлось ни одной настройки - сообщим об этом
            $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'message' => 'Пока что тут не предусмотрено ни одной настройки',
            ));
            return;
        }
        // отображаем все настройки
        $this->render('configList');
    }
    
    /**
     * Получить и вывести виджет для редактирования одной настройки
     * 
     * @param  Config $config - редактируемая настройка
     * @param  array  $widgetOptions - список параметров для виджета настройки 
     *                                 (или для настройки виджета: русский язык, ну что ты творишь!)
     * @param  bool    $return - вывести виджет лил вернуть его html
     * @return string
     */
    protected function getDataWidget($config, $widgetOptions=array())
    {
        /*$options = array(
            'config'          => $config,
            'deleteUrl'       => $this->deleteUrl,
            'updateUrl'       => $this->updateUrl,
            'createUrl'       => $this->createUrl,
            'updateObjectUrl' => $this->updateObjectUrl,
            'configContent'   => $this->getValueEditWidget($config),
            'optionsContent'  => $config->value,
        );
        $options = CMap::mergeArray($options, $widgetOptions);*/
        $options = array(
            'content' => $this->getValueEditWidget($config),
            'config'  => $config,
        );
      
        //return $this->widget('ext.EditableConfig.ConfigData', $options, true);
        return $this->render('configData', $options);
    }
    
    /**
     * Получить и вывести стандартный виджет для редактирования одной настройки
     *
     * @param  Config $config - редактируемая настройка
     * @param  array  $widgetOptions - список параметров для виджета настройки
     *                                 (или для настройки виджета: русский язык, ну что ты творишь!)
     * @param  bool    $return - вывести виджет или вернуть его html
     * @return string
     *
     * @todo предусмотреть все типы настроек - пока что только текст
     */
    protected function getValueEditWidget($config, $widgetOptions=array())
    {
        if ( isset($this->widgetMap[$config->name]) )
        {// задан собственный виджет для отображения настройки
            $class   = $this->widgetMap[$config->name]['class'];
            $options = $this->widgetMap[$config->name]['options'];
            // отображаем или выводим виджет
            return $this->widget($class, $options, true);
        }
        
        if ( $config->isMultiple() )
        {// для списков с множественным выбором выводим виджет редактирования списка
            // @todo предусмотреть случай при котором список не найден
            return $this->widget('ext.EasyListManager.ListItemsGrid', array(
                'easyList'    => $config->selectedList,
                'modalHeader' => 'Добавить шаблон',
            ), true);
        }
        switch ( $config->type )
        {// стандартный тип editable-виджета
            case 'textarea':
            case 'redactor':
            case 'wysihtml5':
                return $this->getLargeEditableText($config);
            break;
        }
        return $this->getEditableField($config);
    }
    
    /**
     * 
     * @param  Config $config
     * @return void
     */
    protected function getConfigEditWidget($config)
    {
        
    }
    
    /**
     * Получить стандартный editable-виджет для редактирования значения настройки
     *
     * @param Config $config
     * @param CActiveRecord|EasyListItem $value
     * @return string
     */
    protected function getEditableField($config)
    {
        $action = 'update';
        if ( ! $config->isFilled() )
        {// если настройка не заполнена
            $action = 'create';
        }
        if ( $config->isMultiple() )
        {
            $modelClass = $config->selectedList->itemtype;
            $model      = new $modelClass;
        }else
        {
            $model = $config->getValueObject();
        }
        return $this->widget('bootstrap.widgets.TbEditableField', array(
            'type'      => $config->type,
            'model'     => $model,
            'attribute' => $config->valuefield,
            'url'       => $this->createConfigUrl($action, $config),
        ), true);
    }
    
    /**
     *
     * @return string
     */
    protected function getLargeEditableText($config)
    {
        return $this->widget('admin.extensions.SimpleEmailRedactor.SimpleEmailRedactor', array(
            'model'     => $config->getTargetObject(),
            'createUrl' => $this->createConfigUrl('create', $config),
            'updateUrl' => $this->createConfigUrl('update', $config),
            'deleteUrl' => $this->createConfigUrl('delete', $config),
        ), true);
    }
    
    /**
     *
     *
     * @return array
     */
    protected function getConfigListAttributes()
    {
        $attributes = array();
        foreach ( $this->configItems as $config )
        {
            $attributes = $this->getAttributeForConfigItem($config);
        }
        return $attributes;
    }
    
    /**
     *
     * @param Config $config
     * @return array
     */
    protected function getAttributeForConfigItem($config)
    {
        return array(
            'type'  => 'raw',
            'name'  => $config->name,
            'label' => $config->title
        );
    }
    
    /**
     * 
     *
     * @return array
     */
    protected function getConfigListData()
    {
        if ( ! $id = $this->objectId )
        {
            $id = 1;
        }
        $data = array('id' => $id);
        foreach ( $this->configItems as $config )
        {
            $data[$config->name] = $this->getDataForConfigItem($config);
        }
        return $data;
    }
    
    /**
     * 
     * @param Config $config
     * @return array
     */
    protected function getDataForConfigItem($config)
    {
        $options = array();
        if ( isset($this->widgetMap[$config->name]['options']) )
        {
            $options = $this->widgetMap[$config->name]['options'];
        }
        return array(
            $config->name => $this->getValueEditWidget($config),
        );
    }
    
    /**
     * Получить URL для действия с настройкой
     *
     * @param  string $action - краткое название действия
     * @param  Config $config - изменяемая настройка
     * @return string
     */
    protected function createConfigUrl($action, $config)
    {
        switch ( $action )
        {
            case 'create':
                return Yii::app()->createUrl($this->createUrl, array('id' => $config->id));
            case 'update':
                return Yii::app()->createUrl($this->updateUrl, array('id' => $config->id));
            case 'delete':
                return Yii::app()->createUrl($this->deleteUrl, array('id' => $config->id));
            case 'updateObject':
                return Yii::app()->createUrl($this->updateObjectUrl, array('id' => $config->id));
        }
    }
}