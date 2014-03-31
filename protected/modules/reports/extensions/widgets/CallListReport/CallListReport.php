<?php

// Подключаем родительский класс виджета
Yii::import('reports.extensions.widgets.ReportViewBase.ReportViewBase');

/**
 * Предварительно отобразить фотовызывной для админа
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
     * @var string - класс формы, которая отвечает за действия с отчетом
     *               по умолчанию доступны только стандартные действия - сохранить и отправить по email
     */
    public $actionsClass = 'CallListActions';
    
    /**
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
     * @see ReportViewBase::createActionsParams()
     */
    protected function createActionsParams()
    {
        $params    = parent::createActionsParams();
        $newParams = array(
            'allowCastingList' => true,
            'event'            => $this->event,
        );
        return CMap::mergeArray($params, $newParams);
    }
}