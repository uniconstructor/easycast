<?php

/**
 * Виджет отображающий все возможные действия с отчетом
 * 
 * @todo убрать параметр $allowCastingList, вместо него создать класс-наследник от ReportActionsBase
 */
class ReportActionsBase extends CWidget
{
    /**
     * @var Report - отчет с которым производятся действия
     */
    public $report;
    /**
     * @var bool - разрешить сохранение фотовызывного (добавляет галочку в форму)
     */
    public $allowSave        = true;
    /**
     * @var bool - разрешить отправку фотовызывного по email (добавляет галочку в форму)
     */
    public $allowSendMail    = true;
    /**
     * @var bool - разрешить преобразование фотовызывного в кастинг-лист
     * @todo перенести это поле в класс CallListActions
     */
    public $allowCastingList = true;
    /**
     * @var string - путь к контроллеру выполняющему сохранение отчета
     */
    public $savePath = '';
    /**
     * @var string - путь к контроллеру выполняющему отправку отчета
     */
    public $mailPath = '';
    /**
     * @var array дополнительные параметры для отчета (свои для каждого типа)
     */
    public $saveParams = array();
    
    /**
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
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('reports.extensions.widgets.ReportActionsBase.views.actions');
    }
    
    /**
     * Отобразить форму сохранения отчета
     * @return null
     */
    protected function displaySaveAction()
    {
        if ( ! $this->allowSave )
        {
            return;
        }
        $this->render('reports.extensions.widgets.ReportActionsBase.views._saveForm');
    }
    
    /**
     * Отобразить форму отправки отчета по email
     * @return null
     */
    protected function displaySendMailAction()
    {
        if ( ! $this->allowSendMail )
        {
            return;
        }
        $this->render('reports.extensions.widgets.ReportActionsBase.views._mailForm');
    }
}