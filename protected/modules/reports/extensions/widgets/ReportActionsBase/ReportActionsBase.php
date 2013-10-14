<?php

/**
 * Виджет отображающий все возможные действия с отчетом
 */
class ReportActionsBase extends CWidget
{
    /**
     * @var Report
     */
    public $report;
    
    public $allowSave = true;
    
    public $allowSendMail = true;
    
    public $savePath = '';
    
    public $mailPath = '';
    
    /**
     * @var array дополнительные параметры для отчета (свои для каждого типа)
     */
    public $saveParams = array();
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     * 
     * @todo проверить наличие объекта в $this->report
     */
    public function init()
    {
        Yii::import('reports.models.Report');
        Yii::import('reports.models.ReportLink');
        
        parent::init();
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        echo '<div class="row">';
        echo '<div class="span4">';
        $this->displaySaveAction();
        echo '</div>';
        echo '<div class="span4">';
        $this->displaySaveSendMailAction();
        echo '</div>';
        echo '</div>';
    }
    
    protected function displaySaveAction()
    {
        if ( ! $this->allowSave )
        {
            return;
        }
        $this->render('_saveForm');
    }
    
    protected function displaySaveSendMailAction()
    {
        if ( ! $this->allowSendMail )
        {
            return;
        }
        $this->render('_mailForm');
    }
}