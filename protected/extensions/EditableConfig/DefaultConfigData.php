<?php

/**
 * Виджет отображающий одну настройку и форму редактирования для нее 
 */
class DefaultConfigData extends CWidget
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
     * @see CWidget::run()
     */
    public function run()
    {
        $content = $this->getEditableContent();
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
        }elseif ( $this->config->objectid )
        {
            return 'Настройка модели';
        }else
        {
            return 'Настройка класса моделей';
        }
    }
    
    /**
     * Получить редактируемое содержимое этой настройки
     * 
     * @return string
     */
    protected function getEditableContent()
    {
        
    }
}