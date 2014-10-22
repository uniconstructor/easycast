<?php

/**
 * Упрощенный виджет для редактирования текста email-оповещений
 * 
 * @todo проверка параметров в init()
 */
class SimpleEmailRedactor extends CWidget
{
    /**
     * @var string - название настройки в которой лежит текст оповещения
     */
    public $configName = 'newInviteMailText';
    /**
     * @var CActiveRecord - модель к которой прикреплены стандартные оповещения
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
     * @var Config
     */
    protected $configValue;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        // получаем настройку с текстом оповещения
        $this->config = $this->model->getConfigObject($this->configName);
        if ( ! $this->config->valueid )
        {
            $item = new EasyListItem();
            $item->easylistid  = 0;
            $item->objecttype  = 'EasyListItem';
            $item->objectid    = 0;
            $item->objectfield = 'value';
            $item->save();
            
            $this->config->valueid = $item->id;
            $this->config->save();
        }
        $this->configValue = $this->config->getValueObject();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('redactor', array('model' => $this->model));
    }
    
    /**
     *
     *
     * @param  EasyListItem $item
     * @return array
     */
    protected function getFormOptions()
    {
        $url = Yii::app()->createUrl($this->updateUrl, array('id' => $this->model->id));
        return array(
            'id'     => 'notify-config-form-'.$this->id,
            'method' => 'post',
            'action' => $url,
            'enableAjaxValidation' => true,
        );
    }
}