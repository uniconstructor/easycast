<?php

/**
 * Виджет редактирования настройки с типом "файл изображения"
 * 
 * @todo временный виджет, позже будет создан универсальный
 */
class ECConfigImageField extends CWidget
{
    /**
     * @var Config
     */
    public $config;
    /**
     * @var unknown
     */
    public $formOptions = array();
    /**
     * @var unknown
     */
    public $hiddenFields = array();
    /**
     * @var unknown
     */
    public $fileField = 'file';
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        $formDefaults = array(
            'id'      => 'config-file-form'.$this->id,
            'method'  => 'post',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('enctype' => 'multipart/form-data'),
        );
        $this->formOptions = CMap::mergeArray($formDefaults, $this->formOptions);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $file = $this->config->getValueObject() )
        {
            $img     = CHtml::image($file->url, $file->oldname, array('style' => 'max-width:100%;'));
            $imgLink = CHtml::link($img, $file->url);
            echo '<div class="well">'.$imgLink.'</div>';
        }
        
        $this->beginWidget('bootstrap.widgets.TbActiveForm', $this->formOptions);
        foreach ( $this->hiddenFields as $name => $value )
        {
            echo CHtml::hiddenField($name, $value);
        }
        // форма загрузки файла
        echo CHtml::fileField($this->fileField);
        // кнопка загрузки файла
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'submit',
            'type'       => 'primary',
            'label'      => 'Сохранить',
        ));
        $this->endWidget();
    }
}