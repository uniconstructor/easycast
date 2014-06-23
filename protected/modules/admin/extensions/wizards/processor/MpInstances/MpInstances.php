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
    public $pageSize = 10;
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $criteria = new CDbCriteria();
        $criteria->scopes = array(
            'forSectionInstance' => array($this->sectionInstanceId),
            'withLinkTypes'      => array($this->markers),
            'withMemberStatus'   => array($this->statuses),
            'lastModified',
        );
        
        $dataProvider = new CActiveDataProvider('MemberInstance', array(
            'criteria'   => $criteria,
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
                /*'pageSize'       => $this->pageSize,
                'pages' => array(
                    'pageVar' => 'page',
                )*/
            ),
            'emptyText' => $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'message' => 'В этом разделе нет заявок',
                'type'    => 'info',
            ), true),
        ));
    }
}