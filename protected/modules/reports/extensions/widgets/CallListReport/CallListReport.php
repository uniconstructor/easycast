<?php

// Подключаем родительский класс виджета
Yii::import('reports.extensions.widgets.ReportViewBase.ReportViewBase');

/**
 * Отобразить фотовызывной
 * 
 * @todo добавить проверку eventid
 * @todo написать функцию составления фотовызывного по данным из отчета, без обращения к текущей базе.
 *       Для этого написать отдельный виджет. Использовать виджет CallList в случае, когда отображается 
 *       еще не созданный отчет (текущие данные) и новый виджет, если отображаются десериализованные данные отчета.
 *       В новом виджете предусмотреть режимы отображения: для письма и для вывода на экран.
 * @todo Сохранять в данных отчета только массив с необходимыми данными (не сериализовывать AR)
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
     * 
     * (non-PHPdoc)
     * @see ReportViewBase::init()
     */
    public function init()
    {
        if ( ! $this->report->isNewRecord )
        {// если мы отображаем существующий отчет - то берем данные из него
            $this->event = $this->report->reportData['event'];
        }
        parent::init();
    }
    
    /**
     * (non-PHPdoc)
     * @see ReportViewBase::displayReportData()
     */
    protected function displayReportData()
    {
        $this->widget('admin.extensions.CallList.CallList', array(
            'objectId' => $this->event->id,
            'comment'  => $this->report->comment,
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
            //'savePath' => 'admin.projectEvent.createCallListReport',
            //'mailPath' => 'admin.projectEvent.sendCallListReport',
        );
        return CMap::mergeArray($params, $newParams);
    }
}