<?php

/**
 * Список созданных администратором анкет
 */
class QCreated extends CWidget
{
    public $startDate;
    
    public $userId;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $date = DateTime::createFromFormat('Y-m-d' , '2013-10-01');
        //$this->startDate = 0;//$date->format('U');
        if ( ! $this->userId )
        {
            $this->userId = Yii::app()->user->id;
        }
        parent::init();
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('userid', $this->userId);
        $criteria->compare('timecreated', '>'.$this->startDate);
        $criteria->order = 'timecreated DESC';
        
        $dataProvider = new CActiveDataProvider('QCreationHistory',
            array(
                'criteria'   => $criteria,
                'pagination' => false,
            )
        );
        
        /*$actions = new TbButtonColumn;
        $actions->template='{view} {update}';
        $actions->viewButtonUrl   = 'Yii::app()->controller->createUrl("/questionary/questionary/view", array("id" => $data->questionaryid))';
        $actions->updateButtonUrl = 'Yii::app()->controller->createUrl("/questionary/questionary/update", array("id" => $data->questionaryid))';
        */
        $this->widget('bootstrap.widgets.TbGridView', array(
            'dataProvider' => $dataProvider,
            'template'     => '{summary}{items}',
            'columns' => array(
                array( // ФИО
                    'name'   => 'fullname',
                    'value'  => '($data->questionary ? CHtml::link($data->questionary->fullname, Yii::app()->createAbsoluteUrl("/questionary/questionary/view", array("id" => $data->questionaryid))) : "(Анкета удалена)" )',
                    'header' => '<b>ФИО</b>',
                    'type'   => 'html',
                ),
                array( // display a column with "view", "update" and "delete" buttons
                    'class'           => 'bootstrap.widgets.TbButtonColumn',
                    'template'        => '{view} {update}',
                    'viewButtonUrl'   => 'Yii::app()->controller->createUrl("/questionary/questionary/view", array("id" => $data->questionaryid))',
                    'updateButtonUrl' => 'Yii::app()->controller->createUrl("/questionary/questionary/update", array("id" => $data->questionaryid))',
                ),
            ),
        ));
    }
}