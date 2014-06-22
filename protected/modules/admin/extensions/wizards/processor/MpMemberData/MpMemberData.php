<?php

/**
 * Виджет выводящий всю информацию по одной заявке
 */
class MpMemberData extends CWidget
{
    /**
     * @var string
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
     * @var Questionary
     */
    protected $questionary;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        $this->questionary = $this->member->questionary;
        if ( ! $this->wrapperId )
        {
            $this->wrapperId = 'wrapper_'.$this->id;
        }
        
        $this->sectionGridOptions['member']         = $this->member;
        $this->sectionGridOptions['customerInvite'] = $this->customerInvite;
        
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
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