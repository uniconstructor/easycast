<?php
/**
 * Список заявок внутри раздела
 */

/**
 * Список заявок в отдельной вкладке
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
     * @see CWidget::run()
     */
    public function run()
    {
        $members = ProjectMember::model()->forSectionInstance($this->sectionInstanceId, $this->markers)->
            withStatus($this->statuses)->findAll();
        if ( $members )
        {
            foreach ( $members as $member )
            {
                $this->widget('admin.extensions.wizards.processor.MpMemberData.MpMemberData', array(
                    'member'             => $member,
                    'customerInvite'     => $this->customerInvite,
                    'sectionGridOptions' => $this->sectionGridOptions,
                ));
            }
        }else
        {
            $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                'message' => 'В этом разделе нет заявок',
                'type'    => 'info',
            ));
        }
    }
}