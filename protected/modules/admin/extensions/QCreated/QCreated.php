<?php

/**
 * Список созданных администратором анкет
 */
class QCreated extends CWidget
{
    /**
     * @var int
     */
    public $startDate;
    /**
     * @var int
     */
    public $userId;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        $date = DateTime::createFromFormat('Y-m-d' , '2013-09-01');
        $this->startDate = $date->format('U');
        if ( ! $this->userId )
        {
            $this->userId = Yii::app()->user->id;
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('objecttype', 'user');
        $criteria->compare('objectid', $this->userId);
        $criteria->compare('timecreated', '>'.$this->startDate);
        $criteria->order = 'timecreated DESC';
        // @todo дубли из таблицы q_creation_history убраны, необходимости в distinct больше нет
        //       Удалить при рефакторинге
        $criteria->distinct = true;
        $criteria->select = array('questionaryid');
        
        
        $dataProvider = new CActiveDataProvider('QCreationHistory', array(
                'criteria'   => $criteria,
                'pagination' => false,
            )
        );
        
        $this->widget('bootstrap.widgets.TbGridView', array(
            'dataProvider' => $dataProvider,
            'template'     => '{summary}{items}{pager}',
            'columns' => array(
                array( // ФИО
                    'value'  => '($data->questionary ? CHtml::link($data->questionary->fullname, Yii::app()->createAbsoluteUrl("/questionary/questionary/view", array("id" => $data->questionaryid))) : "(Анкета удалена)" )',
                    'header' => '<b>ФИО</b>',
                    'type'   => 'raw',
                ),
                array(
                    'class'           => 'bootstrap.widgets.TbButtonColumn',
                    'template'        => '{view} {update}',
                    'viewButtonUrl'   => 'Yii::app()->controller->createUrl("/questionary/questionary/view", array("id" => $data->questionaryid))',
                    'updateButtonUrl' => 'Yii::app()->controller->createUrl("/questionary/questionary/update", array("id" => $data->questionaryid))',
                ),
            ),
        ));
    }
}