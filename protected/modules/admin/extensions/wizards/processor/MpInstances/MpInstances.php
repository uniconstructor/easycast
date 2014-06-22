<?php
/**
 * Список заявок внутри раздела в отдельной вкладке
 */
class MpInstances extends CWidget
{
    /**
     * @var int - id просматриваемой вкладки с заявками
     */
    public $sectionInstanceId;
    /**
     * @var CustomerInvite - приглашение заказчика для отбора актеров
     *                       (для случаев, когда происходит отбор актеров по одноразовой ссылке)
     */
    public $customerInvite;
    /**
     * @var array
     */
    public $statuses = array();
    /**
     * @var array
     */
    public $markers = array();
    /**
     * @var array
     */
    public $sectionGridOptions = array();
    /**
     * @var int
     */
    public $pageSize = 15;
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $criteria = ProjectMember::model()->forSectionInstance($this->sectionInstanceId, $this->markers)->
            withStatus($this->statuses)->getDbCriteria();
        $dataProvider = new CActiveDataProvider('ProjectMember', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $this->pageSize,
            ),
        ));
        
        $this->widget('bootstrap.widgets.TbListView', array(
            'dataProvider' => $dataProvider,
            'ajaxUpdate'   => false,
            'itemView'     => '_member',
            'template'     => $this->render('_template', null, true),
            'viewData'     => array(
                'customerInvite'     => $this->customerInvite,
                'sectionGridOptions' => $this->sectionGridOptions,
                'owner'              => $this,
            ),
            'pager' => array(
                'class'          => 'bootstrap.widgets.TbPager',
                'maxButtonCount' => 30,
            ),
            'emptyText' => $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'message' => 'В этом разделе нет заявок',
                'type'    => 'info',
            ), true),
        ));
    }
}