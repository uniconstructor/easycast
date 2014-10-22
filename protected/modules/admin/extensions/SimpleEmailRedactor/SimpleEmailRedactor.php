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
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        // получаем настройку с текстом оповещения
        $this->config = $this->model->getConfigObject($this->configName);
    }
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('redactor');
    }
    
    /**
     *
     *
     * @param  EasyListItem $item
     * @return array
     */
    protected function getFormOptions($item)
    {
        $url = Yii::app()->createUrl($this->updateUrl, array(
            'id' => $item->id,
        ));
        return array(
            'id'     => $idPrefix.'notify-config-form-'.$item->id.'-'.$this->id,
            'method' => 'post',
            'action' => $url,
            'enableAjaxValidation' => true,
        );
    }
}