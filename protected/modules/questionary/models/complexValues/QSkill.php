<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Класс для работы с полем анкеты "дополнительные умения и навыки"
 */
class QSkill extends QActivity
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "skills",
            'condition' => "`skills`.`type`='skill'",
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

    public function setName($name)
    {
        if ( $this->value == 'custom' )
        {
            $this->uservalue = strip_tags($name);
        }else
       {
           $this->value = strip_tags($name);
        }
    }
}