<?php

/**
 * Виджет отображающий одну настройку и форму редактирования для нее 
 */
class ConfigData extends CWidget
{
    /**
     * @var Config
     */
    public $config;
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
     * @var string - url по которому происходит обновление модели самой настройки
     */
    public $updateObjectUrl;
    /**
     * @var string - тип отображения списка настроек
     *               'tabs'   - вкладки настройка/значение/списки
     *               'blocks' - все данные одним блоком
     */
    public $display = 'tabs';
    /**
     * @var string
     */
    public $configContent  = '';
    /**
     * @var string
     */
    public $optionsContent = '';
    /**
     * @var bool - 
     */
    //public $displayConfig  = true;
    /**
     * @var bool -
     */
    //public $displayOptions = true;
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $content = $this->getContent();
        $this->render('configData', array('content' => $content));
    }
    
    /**
     * Определить тип настройки
     * 
     * @return string
     */
    protected function getConfigType()
    {
        if ( $this->config->objecttype === 'system' )
        {
            return 'Настройка системы';
        }
        if ( $this->config->objectid )
        {
            return 'Настройка модели '.$this->config->objecttype;
        }
        if ( $this->config->objecttype AND ! $this->config->objectid )
        {
            return 'Стандартная настройка моделей '.$this->config->objecttype;
        }
        return '';
    }
    
    /**
     * 
     * 
     * @return string
     */
    protected function getContent()
    {
        if ( $this->display === 'block' )
        {
            $this->render('configDataBlock', array(), true);
        }else
        {
            $this->render('configDataTabs', array(), true);
        }
    }
    
    
    protected function getEditableValue()
    {
        
    }
    
    /**
     *
     *
     * @param string $action
     * @param Config $config
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