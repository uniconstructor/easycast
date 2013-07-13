<?php

/**
 * отображение списка стандартных значений
 */
class QDefaults extends CWidget
{
    /**
     * @var string - класс отображаемых значения - ВУЗ, театр или просто свойство
     */
    public $valueClass;

    /**
     * @var string - тип отображаемых значений - музыкальные ВУЗы или виды спорта
     */
    public $valueType;
    
    protected $_assetUrl;
    
    /**
     *
     */
    public function init()
    {
        
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->valueClass OR ! $this->valueType )
        {
            return '';
        }
        // Выводим таблицу со списком стандаотных значений
        $this->widget('bootstrap.widgets.TbGridView', array(
            'dataProvider' => $this->getElements(),
            'template'     => "{items}{pager}",
            'columns'      => $this->getColumns(),
        ));
        // Выводим кнопку для добавления нового значения
        $addUrl = $this->getActionUrl('create');
        echo CHtml::link('Добавить значение', $addUrl, array('class' => 'btn btn-success'));
    }
    
    /**
     * Получить заголовки таблицы в зависимости от класса стандартных значений
     * @return array
     */
    protected function getColumns()
    {
        // Добавлием столбцы в зависимости от класса занчения
        $columns = $this->getColumnsByClass();
        
        $viewUrl   = $this->getActionUrlExpression('view');
        $updateUrl = $this->getActionUrlExpression('update');
        $replaceUrl = $this->getActionUrlExpression('replace');
        
        // добавляем общую для всех последнюю колонку с действиями
        $columns[] = array(
            'class' => 'CButtonColumn',
            'template' => '{update} {replace}',
            'buttons' => array(
                //'view'   => array('url' => $viewUrl),
                'update' => array('url' => $updateUrl),
                'replace' => array(
                    'url'      => $replaceUrl,
                    'label'    => 'Заменить',
                    'imageUrl' => '/images/merge.png'
               ),
            )
        );
        
        return $columns;
    }
    
    /**
     * Получить набор колонок в таблице в зависимости от того что мы мы редактируем
     * (ВУЗ или другое стандартное значение)
     * @return array  
     */
    protected function getColumnsByClass()
    {
        $columns = array();
        switch ( $this->valueClass )
        {
            case 'university':
                $columns[] = array('name'=>'name', 'header'=>Yii::t('coreMessages', 'name'), 'type' => 'html');
                /*$columns[] = array(
                    'class'       => 'JToggleColumn',
                    'name'        => 'system', // boolean model attribute (tinyint(1) with values 0 or 1)
                    'filter'      => array('0' => 'No', '1' => 'Yes'), // filter
                    'htmlOptions' => array('style'=>'text-align:center;min-width:60px;')
                );*/
                $columns[] = array('name'=>'system', 'header'=>'Отображать в меню?', 'type' => 'html');
            break;
            case 'activity':
                $columns[] = array('name'=>'translation', 'header'=>'Отображаемый текст', 'type' => 'html');
                $columns[] = array('name'=>'value', 'header'=>'Короткое название');
            break;
        }
        return $columns;
    }
    
    /**
     * Получить список стандартных значений, в зависимости от их класса и типа
     * @return CArrayProvider
     */
    protected function getElements()
    {
        switch ( $this->valueClass )
        {
            case 'university': $elements = $this->getUniversities(); break;
            case 'activity':   $elements = $this->getActivityTypes(); break;
        }
        
        $arrayProvider = new CArrayDataProvider($elements, array(
            'pagination'=>false,
        ));
        
        return $arrayProvider;
    }
    
    /**
     * Получить список ВУЗов
     * @return array
     */
    protected function getUniversities()
    {
        $criteria = new CDbCriteria;
        $criteria->condition = ' `type` = :type ';
        $criteria->params = array(':type' => $this->valueType);
        $criteria->order = ' `name` ASC';
        
        $records = QUniversity::model()->findAll($criteria);
        
        $elements = array();
        
        foreach ( $records as $record )
        {
            //$nameUrl = $this->getActionUrl('view', $record->id);
            
            $element = array();
            $element['id'] = $record->id;
            $element['name'] = $record->name;//CHtml::link($record->name, $nameUrl);
            $element['system'] = Yii::t('coreMessages', $record->system);
            
            $elements[] = $element;
        }
        
        return $elements;
    }
    
    /**
     * Получить html-код ajax-переключателя для смены параметра system в модели QUniversity
     * (переключатель поределяе, можно ли показывать этот ВУЗ в списке стандартных значений или нет) 
     * 
     * @param int $id - id учебного заведения в таблице q_universities
     * 
     * @return string - html-код виджета
     */
    protected function getSystemChangeWidget($id)
    {
        return $this->widget('ext.EToggleBox.EToggleBox', array(
            'model'     => $questionary,
            'attribute' => 'isactor',
            'options'   => CMap::mergeArray($toggleBoxJsOptions, array(
                // при включении: добавляем ВУЗ в список стандартных значений
                'after_on'  => 'js:function () {
                            
                        }',
                // при выключении: удаляем ВУЗ из списка стандартных значений
                'after_off' => 'js:function () {
                            
                        }'))
                         ), true);
    }
    
    /**
     * Получить список характеристик указанного типа
     * @return array
     */
    protected function getActivityTypes()
    {
        $criteria = new CDbCriteria;
        $criteria->condition = ' name = :type ';
        $criteria->params = array(':type' => $this->valueType);
        $criteria->order = ' `translation` ASC';
        
        $records = QActivityType::model()->findAll($criteria);
        
        foreach ( $records as $record )
        {
            $nameUrl = $this->getActionUrl('view', $record->id);
            
            $element = array();
            $element['id'] = $record->id;
            $element['translation'] = $record->translation;//CHtml::link($record->translation, $nameUrl);
            $element['value'] = $record->value;
            
            $elements[] = $element;
        }
        
        return $elements;
    }
    
    /**
     * Получить ссылку для совершения действия с объектом
     * @param string $action - совершаемое действие (create, view, update. replace)
     * @param int $id - id записи
     * @return string
     */
    protected function getActionUrl($action, $id=null)
    {
        $path = $this->getPathByAction($action);
        
        $options = array(
            'class' => $this->valueClass,
            'type'  => $this->valueType,
        );
        
        if ( $id )
        {
            $options['id'] = $id;
        }
        
        return Yii::app()->createUrl($path, $options);
    }
    
    /**
     * Получить url в зависимости от того какой элемент мы собрались редактировать
     * @param string $action - что будем редактировать ВУЗ или другое стандартное значение
     * @return string
     */
    protected function getPathByAction($action)
    {
        switch ( $this->valueClass )
        {
            case 'university': return '/admin/standartValue/'.$action.'University'; break;
            case 'activity':   return '/admin/standartValue/'.$action.'ActivityType'; break;
        }
    }
    
    protected function getActionUrlExpression($action)
    {
        $path = $this->getPathByAction($action);
        
        return 'Yii::app()->createUrl("'.$path.'",
                array(
                    "id"    => $data["id"],
                    "class" => "'.$this->valueClass.'",
                    "type"  => "'.$this->valueType.'",
                )
            );';
    }
    
    /**
     * Registers the javascript code.
     */
    public function registerClientScript()
    {
        $this->_assetUrl = CHtml::asset(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets');
        $id = __CLASS__ . '#' . $this->getId();
    
        $clientScript = Yii::app()->clientScript;
        //register javascript
        $clientScript->registerCoreScript('jquery');
    
        if ( YII_DEBUG )
        {// @todo сделать краткую и полную версию скриптов
            $script = '/qdefaults.js';
        }else
       {
            $script = '/qdefaults.js';
        }
        // register main js lib
        $clientScript->registerScriptFile($baseUrl . $script);
    }
}