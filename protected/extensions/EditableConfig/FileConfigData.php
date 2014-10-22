<?php

/**
 * 
 * @todo переписать для нормального использования $this->updateUrl
 */
class FileConfigData extends DefaultConfigData
{
    /**
     * @see DefaultConfigData::run()
     */
    public function run()
    {
        $content = $this->getEditableContent();
        $this->render('configData', array('content' => $content));
    }
    
    /**
     *
     *
     * @return void
     */
    protected function getEditableContent()
    {
        return $this->widget('ext.ECMarkup.ECConfigImageField.ECConfigImageField', array(
            'config'      => $this->config,
            'formOptions' => array(
                'id'     => 'config-data-file-editable-'.$this->config->id.'-'.$this->id,
                'action' => $this->updateUrl,
            ),
            'hiddenFields' => array(
                'projectId' => $model->id,
                'pk'        => $bannerConfig->id,
            ),
        ), true);
    }
}