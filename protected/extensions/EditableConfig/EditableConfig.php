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
     * @var string - 
     */
    public $objectType = 'system';
    /**
     * @var int - 
     */
    public $objectId = 0;
    /**
     * @var array - массив определяющий какие настройки какими виджетами редактировать
     *              Используется когда нужны особые виджеты для особых настроек
     *              Ключом всегда является название (name) настройки а значением - 
     *              массив, в котором ключом является класс виджета а содержимым 
     *              массив параметров инициализации 
     */
    public $widgetMap = array();
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl;
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl;
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl;
    /**
     * @var CDbCriteria - дополнительные условия выборки настроек внутри набора ограниченного
     *                    парой objecttype/objectid
     * 
     * @todo пока не используется
     */
    public $extraCriteria;
    
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
        // определим есть ли в наборе хотя бы одна настройка
        $this->hasConfig   = Config::model()->forObject($this->objectType, $this->objectId)->exists();
        
        if ( $this->configItems )
        {// в наборе нет ни одной настройки
            return;
        }
        // получаем все настройки
        $this->configItems = Config::model()->forObject($this->objectType, $this->objectId)->findAll();
        // @todo загружаем классы виджетов отдельных настроек
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
        }
        // отображаем все настройки из набора
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
    protected function getConfigWidget($config, $widgetOptions=array(), $return=false)
    {
        if ( isset($this->widgetMap[$config->name]) )
        {// 
            $class   = $this->widgetMap[$config->name]['class'];
            $options = $this->widgetMap[$config->name]['options'];
            $options = CMap::mergeArray($options, $widgetOptions);
            // отображаем или выводим виджет
            return $this->widget($class, $options, $return);
        }
        return $this->getDefaultWidget($config, $widgetOptions, $return);
    }
    
    /**
     * Получить и вывести стандартный виджет для редактирования одной настройки
     * 
     * @param  Config $config - редактируемая настройка
     * @param  array  $widgetOptions - список параметров для виджета настройки 
     *                                 (или для настройки виджета: русский язык, ну что ты творишь!)
     * @param  bool    $return - вывести виджет лил вернуть его html
     * @return string
     * 
     * @todo предусмотреть все типы настроек - пока что только текст
     */
    protected function getDefaultWidget($config, $widgetOptions=array(), $return=false)
    {
        $prefix      = 'ext.EditableConfig.';
        $widgetClass = 'ext.EditableConfig.DefaultConfigData';
        
        switch ( $config->type )
        {// определяем тип виджета
            case 'textarea':
            case 'redactor':
                $widgetClass = $prefix.'TextAreaConfigData';
            break;
            default: $widgetClass = $prefix.'DefaultConfigData'; break;
        }
        return $this->widget($widgetClass, $widgetOptions, $return);
    }
    
    /**
     * Получить модель к которой привязаны все настройки
     * 
     * @return CActiveRecord|null
     */
    protected function getModel()
    {
        
    }
}