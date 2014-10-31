<?php

/**
 * 
 */
class TextAreaConfigData extends DefaultConfigData
{
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $content = $this->getEditableContent();
        $this->render('configData', array('content' => $content));
    }
    
    /**
     *
     * @return string
     */
    protected function getEditableContent()
    {
        return $this->widget('admin.extensions.SimpleEmailRedactor.SimpleEmailRedactor', array(
            'model'     => $this->config->getTargetObject(),
            'createUrl' => Yii::app()->createUrl('admin/admin/createListItem'),
            'updateUrl' => Yii::app()->createUrl('admin/admin/updateListItem'),
            'deleteUrl' => Yii::app()->createUrl('admin/admin/deleteListItem'),
        ), true);
    }
}