<?php

Yii::import('ext.CdGridPreview.behaviors.CdGridItemBehavior');

/**
 * Позволяет отображать список проектов с раскрывающейся информацией (Экспериментальный CSS3 виджет)
 * Используется виджетом CdGridPreview
 * 
 * @property Project $owner
 */
class CdGridViewProjectBehavior extends CdGridItemBehavior
{
    /**
     * @var bool - load item description via AJAX if true
     */
    public $useAjax = true;
    
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
        return $this->owner->name;
    }
    
    /**
     * @see parent::getGridItemLargeSrc()
     * @return string
     */
    public function getGridItemLargeSrc()
    {
        return $this->owner->getAvatarUrl('full');
    }
    
    /**
     * @see parent::getGridItemTitle()
     * @return string
     */
    public function getGridItemTitle()
    {
        return $this->owner->name;
    }
    
    /**
     * @see CdGridItemBehavior::getGridItemDescription()
     */
    public function getGridItemDescription()
    {
        $result  = parent::getGridItemDescription();
        $result .= Yii::app()->controller->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'link',
            'type'       => 'primary',
            'size'       => 'large',
            'label'      => 'На страницу проекта',
            'url'        => $this->getGridItemNoJsUrl(),
        ), true);
        return $result;
    }
    
    /**
     * @see CdGridItemBehavior::getGridListItemOptions()
     */
    public function getGridListItemOptions()
    {
        $defaults = parent::getGridListItemOptions();
        // @todo подсказка не отображается нормально в коммерческом со старыми стилями
        $options  = array(/*
            'data-toggle' => 'tooltip',
            'data-title'  => $this->getGridItemPreviewAlt(),
        */);
        return CMap::mergeArray($defaults, $options);
    }
    
    /**
     * @see parent::getGridItemNoJsUrl()
     * @return string
     */
    public function getGridItemNoJsUrl()
    {
        return Yii::app()->createAbsoluteUrl('//projects/projects/view', array('id' => $this->owner->primaryKey));
    }
}