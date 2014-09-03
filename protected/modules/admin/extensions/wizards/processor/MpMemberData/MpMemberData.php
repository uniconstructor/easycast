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
    public $customerFields     = array('height', 'weight', 'chestsize', 'waistsize', 'hipsize', 'shoessize', 'countryName');
    /** 
     * @var array
     */
    public $adminFields        = array('mobilephone', 'email', 'status');
    /**
     * @var array - настройки виджета MpMemberSections
     */
    public $sectionGridOptions = array();
    /**
     * @var bool - свернуть ли изначально блок дополнительных полей?
     */
    public $collapseExtra      = true;
    /**
     * @var bool - свернуть ли изначально блок c разделами заявки?
     */
    public $collapseSections   = true;
    
    /**
     * @var Questionary
     */
    protected $questionary;
    /**
     * @var bool
     */
    protected $displayContacts = false;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        Yii::import("xupload.models.XUploadForm");
        
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
        $inviteData = $this->customerInvite->loadData();
        if ( isset($inviteData['displayContacts']) AND $inviteData['displayContacts'] )
        {
            $this->displayContacts = true;
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
            Yii::log('Не удалось отобразать заявку, memberid: '.$memberId, CLogger::LEVEL_ERROR);
            return;
        }
        $this->render('main');
    }
    
    /**
     * Получить список значений для краткой информации об участнике (для виджета TbDetailView)
     * @return array
     */
    protected function getSummaryData()
    {
        $data   = array('id' => 1);
        $fields = $this->customerFields;
        
        if ( Yii::app()->user->checkAccess('Admin') )
        {// админам видно больше полей чам заказчикам
            $fields = CMap::mergeArray($fields, $this->adminFields);
        }
        foreach ( $fields as $field )
        {
            $data[$field] = $this->questionary->$field;
        }
        return $data;
    }
    
    /**
     * Получить список отображаемых полей для краткой информации об участнике (для виджета TbDetailView)
     * @return array
     */
    protected function getSummaryAttributes()
    {
        $attributes = array();
        $fields     = $this->customerFields;
        
        if ( Yii::app()->user->checkAccess('Admin') )
        {// админам видно больше полей чам заказчикам
            $fields = CMap::mergeArray($fields, $this->adminFields);
        }
        foreach ( $fields as $field )
        {
            $attributes[] = array('name' => $field, 'label' => $this->questionary->getAttributeLabel($field));
        }
        return $attributes;
    }
    
    /**
     * Определить нужно ли отображать разворачивающийся блок с дополнительной информацией об участнике
     * (это дополнительная информация, указываемая участником)
     * @return bool
     * 
     * @todo сделать настройку "скрыть дополнительные поля" и проверять ее здесь
     */
    protected function displayExtraFields()
    {
        $extraFieldCount = ExtraField::model()->
            forVacancy($this->member->vacancy)->count();
        if ( ! $extraFieldCount )
        {// к роли не прикреплено ни одного дополнительного поля - не отображаем блок с доп. полями
            return false;
        }
        if ( $extraFieldCount < 5 )
        {// не сворачиваем изначально блок с доп. информацией, если полей мало
            $this->collapseExtra = false;
        }
        return true;
    }
    
    /**
     * Определить, нужно ли отображать раскрывающийся 
     * блок со списком разделов, в которые можно распределить заявки
     * @return bool
     */
    protected function displayVacancySections()
    {
        $sectionCount = CatalogSectionInstance::model()->
            forObject('vacancy', $this->member->vacancy->id)->count();
        if ( ! $sectionCount )
        {// заявки роли не разбиты на разделы - не выводим ненужный блок
            return false;
        }
        if ( $sectionCount < 5 )
        {// не сворачиваем изначально блок с разделами, если разделов мало
            $this->collapseSections = false;
        }
        return true;
    }
}