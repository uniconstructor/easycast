<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Класс для работы с дополнительными характеристиками внешности участника
 */
class QAddChar extends QActivity
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "addchars",
            'condition' => "`addchars`.`type`='addchar'",
        );
    }
    /**
     * @see parent::rules()
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        
        $customRules = array(
            // название характеристики
            array('name', 'length', 'max'=>255),
            array('name', 'filter', 'filter'=>'trim'),
        );
        return CMap::mergeArray($rules, $customRules);
    }
    
    /**
     * Получить название дополнительной характеристики участника
     * @see QActivity::getName()
     */
    public function getName()
    {
        if ( $this->value == 'custom' )
        {
            return $this->Uservalue;
        }
       
        if ( $this->scenario == 'view' )
        {
            return $this->getDefaultValueForDisplay();
        }
        return $this->value;
    }
    
    /**
     * Сохранить значение стандартной характеристики участника
     * @see QActivity::setName()
     */
    public function setName($name)
    {
        if ( $this->value == 'custom' )
        {
            $this->uservalue = strip_tags($name);
        }
    }
}