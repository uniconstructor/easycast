<?php

/**
 * Список отчетов
 */
class ReportList extends CWidget
{
    /**
     * @var CDbCriteria - условия выборки отчетов
     */
    public $criteria;
    
    /**
     * @var string
     */
    public $reportClass = 'Report';
    
    /**
     * @var bool|array
     */
    public $pagination = false;
    
    /**
     * @var string - заголовок списка отчетов
     */
    public $header = '';
    
    /**
     * @var string - относительный url на страницу просмотра отчета
     */
    public $viewUrl = '';
    
    /**
     * @var CActiveDataProvider
     */
    protected $dataProvider;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $this->dataProvider = new CActiveDataProvider($this->reportClass,
            array(
                'criteria'   => $this->criteria,
                'pagination' => $this->pagination,
            )
        );
        parent::init();
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        echo $this->createHeader();
        
        $this->widget('bootstrap.widgets.TbGridView', array(
            'dataProvider' => $this->dataProvider,
            'columns' => array(
                'id',
                array(
                    'name'  => 'name',
                    'value' => 'CHtml::link($data->name, Yii::app()->controller->createUrl("'.$this->viewUrl.'", array("id" => $data->id)))',
                    'type'  => 'html',
                ),
                array(
                    'name'   => 'timecreated',
                    'value'  => '( $data->timecreated ? date("Y.m.d H:i", $data->timecreated) : "" )',
                    //'header' => '<b>Super time</b>',
                ),
            ),
        ));
    }
    
    /**
     * Создать заголовок для списка отчетов
     * @return string
     */
    protected function createHeader()
    {
        if ( ! $this->header )
        {
            return '';
        }
        return '<h3>'.$this->header.'</h3>';
    }
}