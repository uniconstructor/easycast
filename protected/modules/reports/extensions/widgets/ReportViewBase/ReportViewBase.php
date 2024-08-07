<?php

/**
 * Виджет для отображения одного отчета и действий с ним (базовый класс)
 */
class ReportViewBase extends CWidget
{
    /**
     * @var Report
     */
    public $report;
    /**
     * @var string - отображать ли гоголовок отчета?
     */
    public $showHeader = false;
    /**
     * @var string - класс формы, которая отвечает за действия с отчетом
     *               по умолчанию доступны только стандартные действия - сохранить и отправить по email
     */
    public $actionsClass = 'ReportActionsBase';
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! ($this->report instanceof Report) )
        {
            throw new CException('В виджет ReportView не передан отчет для отображения');
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->displayHeader();
        $this->displayReportData();
        $this->displayActions();
    }
    
    /**
     * Получить заголовок с общей информацией по отчету
     * @return null
     */
    protected function displayHeader()
    {
        if ( ! $this->showHeader )
        {
            return;
        }
        $this->render('reports.extensions.widgets.ReportViewBase.views._header');
    }
    
    /**
     * Отобразить основные данные отчета
     * Логика зависит от типа отчета, и поэтому задается только в дочерних классах
     * @return null
     */
    protected function displayReportData()
    {
        throw new CException('displayReportData() must be redeclarated');
    }
    
    /**
     * Отобразить все доступные для отчета действия
     * @return null
     */
    protected function displayActions()
    {
        echo $this->createActions();
    }
    
    /**
     * Получить html-код доступных для отчета действий
     * @return string
     */
    protected function createActions()
    {
        return $this->widget("reports.extensions.widgets.{$this->actionsClass}.{$this->actionsClass}",
            $this->createActionsParams(), true);
    }
    
    /**
     * Получить параметры для создания формы со списком действий для отчета,
     * эти параметры будут использоваться в виджете ReportActionsBase
     * @return array
     */
    protected function createActionsParams()
    {
        $allowSave     = false;
        $allowSendMail = false;
        
        if ( $this->report->isNewRecord )
        {
            $allowSave = true;
        }
        if ( $this->report->status === Report::STATUS_FINISHED )
        {
            $allowSendMail = true;
        }
        return array(
            'report'        => $this->report,
            'allowSave'     => $allowSave,
            'allowSendMail' => $allowSendMail,
        );
    }
}