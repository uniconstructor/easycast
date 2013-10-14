<?php

Yii::import('reports.extensions.widgets.ReportViewBase.ReportViewBase');

/**
 * Отобразить фотовызывной
 * @todo добавить проверку eventid
 * @todo для созданных отчетов брать eventid из данных отчета
 */
class CallListReport extends ReportViewBase
{
    /**
     * @var ProjectEvent
     */
    public $event;
    
    /**
     * @var bool - отображать ли контакты в вызывном листе
     */
    public $displayContacts = false;
    
    /**
     * (non-PHPdoc)
     * @see ReportViewBase::displayReportData()
     */
    protected function displayReportData()
    {
        $this->widget('admin.extensions.CallList.CallList', array(
            'objectId' => $this->event->id,
        ));
    }
    
    /**
     * (non-PHPdoc)
     * @see ReportViewBase::createActionsParams()
     */
    protected function createActionsParams()
    {
        $params    = parent::createActionsParams();
        $newParams = array(
            'savePath' => 'admin.projectEvent.createCallListReport',
            'mailPath' => 'admin.projectEvent.sendCallListReport',
        );
        return CMap::mergeArray($params, $newParams);
    }
}