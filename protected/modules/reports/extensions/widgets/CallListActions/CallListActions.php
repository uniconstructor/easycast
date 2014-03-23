<?php
// подключение базовых классов
Yii::import('reports.extensions.widgets.ReportActionsBase.ReportActionsBase');

/**
 * Форма действий для фотовызывного
 * @todo указывать количество подтвержденных, предварительно одобренных и отклоненных заявок
 */
class CallListActions extends ReportActionsBase
{
    /**
     * @see ReportActionsBase::displaySaveAction()
     */
    public function displaySaveAction()
    {
        if ( ! $this->allowSave )
        {
            return;
        }
        $this->render('_saveForm');
    }
    
    /**
     * @see ReportActionsBase::displaySendMailAction()
     */
    public function displaySendMailAction()
    {
        parent::displaySendMailAction();
        // указываем заявки с какими статусами включены в фотовызывной
        $data = $this->report->getData();
        if ( isset($data['statuses']) AND ! empty($data['statuses']) )
        {
            foreach ( $data['statuses'] as $status )
            {
                switch ( $status )
                {
                    case ProjectMember::STATUS_ACTIVE: 
                        $this->widget('bootstrap.widgets.TbLabel', array(
                            'type'  => 'success',
                            'label' => 'Одобренные',
                        ));
                    break;
                    case ProjectMember::STATUS_PENDING:
                        $this->widget('bootstrap.widgets.TbLabel', array(
                            'type'  => 'warning',
                            'label' => 'Предварительно отобранные',
                        ));
                    break;
                    case ProjectMember::STATUS_DRAFT:
                        $this->widget('bootstrap.widgets.TbLabel', array(
                            'type'  => 'info',
                            'label' => 'Неподтвержденные',
                        ));
                    break;
                }
            }
        }
    }
}