<?php

Yii::import('ext.CdGridPreview.behaviors.CdGridItemBehavior');

/**
 * Класс для отображения анкет в раскрывающемся блоке
 */
class CdGridViewQuestionaryBehavior extends CdGridItemBehavior
{
    /**
     * @var bool - load item description via AJAX if true
     */
    public $useAjax = true;
    /**
     * @see parent::previewHtmlOptions
     */
    public $previewHtmlOptions = array(
        'style' => 'min-height:150px;max-width:150px;min-width:150px;',
        'class' => 'ec-shadow-3px',
    );
    
    /**
     * @see parent::getGridItemPreviewSrc()
     * @return string
     */
    public function getGridItemPreviewSrc()
    {
        return $this->owner->getAvatarUrl('small', true);
    }
    
    /**
     * @see parent::getGridItemPreviewAlt()
     * @return string
     */
    public function getGridItemPreviewAlt()
    {
        return $this->owner->fullname.' '.$this->owner->age;
    }
    
    /**
     * @see parent::getGridItemLargeSrc()
     * @return string
     */
    public function getGridItemLargeSrc()
    {
        return '';
    }
    
    /**
     * @see parent::getGridItemTitle()
     * @return string
     */
    public function getGridItemTitle()
    {
        return CHtml::link($this->getGridItemPreviewAlt(), $this->getGridItemNoJsUrl(), array('target' => '_blank'));
    }
    
    /**
     * @see CdGridItemBehavior::getGridListItemContent()
     */
    protected function getGridItemDescriptionContent()
    {
        return '<script></script>';
    }
    
    /**
     * @see CdGridItemBehavior::getGridListItemOptions()
     */
    public function getGridListItemOptions()
    {
        $defaults = parent::getGridListItemOptions();
        $options  = array(
            'data-toggle' => 'tooltip',
            'data-title'  => $this->getGridItemPreviewAlt(),
        );
        return CMap::mergeArray($defaults, $options);
    }
    
    /**
     * @see parent::getGridItemNoJsUrl()
     * @return string
     */
    public function getGridItemNoJsUrl()
    {
        return Yii::app()->createAbsoluteUrl('//questionary/questionary/view', array('id' => $this->owner->primaryKey));
    }
}