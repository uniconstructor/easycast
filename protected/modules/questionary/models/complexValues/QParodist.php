<?php

Yii::import('application.modules.questionary.models.QActivity');

class QParodist extends QActivity
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "parodistlist",
            'condition' => "`parodistlist`.`type`='parodist'",
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
                        array('name', 'length', 'max'=>255 ),
        );
        return CMap::mergeArray($rules, $customRules);
    }
    
    public function getName()
    {
        return $this->Uservalue;
    }
    
    public function setName($name)
    {
        if ( $this->value == 'custom' )
        {
            $this->uservalue = strip_tags($name);
        }
    }
}