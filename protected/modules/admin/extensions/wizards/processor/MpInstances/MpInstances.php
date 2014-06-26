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
     * @var Vacancy
     */
    public $vacancy;
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
        if ( $this->sectionInstanceId > 0 )
        {// id раздела указан - выводим только заявки раздела
            $criteria->scopes = array(
                'forSectionInstance' => array($this->sectionInstanceId),
                'withLinkTypes'      => array($this->markers),
                'withMemberStatus'   => array($this->statuses),
                'lastModified',
            );
            $model = 'MemberInstance';
            $view  = '_instance';
        }else
        {// если id раздела не указан - выводим заявки всех разделов
            $criteria->scopes = array(
                'forVacancy'    => array($this->vacancy->id),
                'withStatus'    => array($this->statuses),
                'lastCreated',
            );
            $model = 'ProjectMember';
            $view  = '_member';
        }
        
        $dataProvider = new CActiveDataProvider($model, array(
            'criteria'   => $criteria,
            'pagination' => array(
                'pageSize' => $this->pageSize,
            ),
        ));
        $this->widget('bootstrap.widgets.TbListView', array(
            'dataProvider' => $dataProvider,
            'ajaxUpdate'   => false,
            'itemView'     => $view,
            'template'     => $this->render('_template', null, true),
            'viewData'     => array(
                'customerInvite'     => $this->customerInvite,
                'sectionGridOptions' => $this->sectionGridOptions,
                'owner'              => $this,
            ),
            'pager' => array(
                'class'          => 'bootstrap.widgets.TbPager',
                'maxButtonCount' => 25,
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