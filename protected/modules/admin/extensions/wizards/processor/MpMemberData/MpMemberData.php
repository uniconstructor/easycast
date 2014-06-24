<?php

/**
 * Виджет выводящий всю информацию по одной заявке
 */
class MpMemberData extends CWidget
{
    /**
     * @var string режим отображения
     * @todo
     */
    public $mode;
    /**
     * @var ProjectMember
     */
    public $member;
    /**
     * @var CustomerInvite - приглашение заказчика для отбора актеров
     *                       (для случаев, когда происходит отбор актеров по одноразовой ссылке)
     */
    public $customerInvite;
    /**
     * @var string 
     */
    public $wrapperId;
    /**
     * @var array
     */
    public $customerFields = array('height', 'weight', 'chestsize', 'waistsize', 'hipsize', 'shoessize', 'countryName');
    /** 
     * @var array
     */
    public $adminFields    = array('mobilephone', 'email', 'status');
    /**
     * @var array - настройки виджета MpMemberSections
     */
    public $sectionGridOptions = array();
    /**
     * @var bool - свернуть ли изначально блок дополнительных полей?
     */
    public $collapseExtra    = true;
    /**
     * @var bool - свернуть ли изначально блок c разделами заявки?
     */
    public $collapseSections = true;
    
    /**
     * @var Questionary
     */
    protected $questionary;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ( $this->member instanceof ProjectMember ) AND isset($this->member->questionary) )
        {
            $this->questionary = $this->member->questionary;
            $this->sectionGridOptions['member'] = $this->member;
        }
        $this->sectionGridOptions['customerInvite'] = $this->customerInvite;
        if ( ! $this->wrapperId )
        {
            $this->wrapperId = 'wrapper_'.$this->id;
        }
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->questionary )
        {
            $memberId = 0;
            if ( isset($this->member->id) )
            {
                $memberId = $this->member->id;
            }
            Yii::log('Не удалось отобразать заявку, memberid:'.$memberId, CLogger::LEVEL_ERROR);
            return;
        }
        $this->render('main');
    }
    
    /**
     * 
     * @return array
     */
    protected function getSummaryData()
    {
        $data = array('id' => 1);
        $fields = $this->customerFields;
        if ( Yii::app()->user->checkAccess('Admin') )
        {
            $fields = CMap::mergeArray($fields, $this->adminFields);
        }
        foreach ( $fields as $field )
        {
            $data[$field] = $this->questionary->$field;
        }
        return $data;
    }
    
    /**
     * 
     * @return array
     */
    protected function getSummaryAttributes()
    {
        $attributes = array();
        $fields = $this->customerFields;
        if ( Yii::app()->user->checkAccess('Admin') )
        {
            $fields = CMap::mergeArray($fields, $this->adminFields);
        }
        foreach ( $fields as $field )
        {
            $attributes[] = array('name' => $field, 'label' => $this->questionary->getAttributeLabel($field));
        }
        return $attributes;
    }
}