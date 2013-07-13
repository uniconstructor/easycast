<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Модель для хранения данных в поле "типы вокала"
 */
class QVocalType extends QActivity
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "vocaltypes",
            'condition' => "`vocaltypes`.`type`='vocaltype'",
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
            return QuestionaryModule::t('vocaltype_'.$this->value);
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