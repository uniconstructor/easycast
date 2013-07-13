<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Класс для хранения тембров голоса, которыми владеет участник
 */
class QVoiceTimbre extends QActivity
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "voicetimbres",
            'condition' => "`voicetimbres`.`type`='voicetimbre'",
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

    /**
     * Получить значение тембра голоса участника
     * (полное название (перевод) если модель отображается или краткое значение из
     * БД если модель создается или редактируется)
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
     * Сохранить тембр голоса участника
     * @see QActivity::setName()
     */
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