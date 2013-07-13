<?php

Yii::import('application.modules.questionary.models.QActivity');


class QExtremalType extends QActivity
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "extremaltypes",
            'condition' => "`extremaltypes`.`type`='extremaltype'",
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
     * Получить название экстремального вида спорта (если модель отображается)
     * или значение из БД (если модель редактируется или создается)
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
     * Сохранить вид экстремального спорта, которым занимается участник 
     * (выбранный из списка или свой)
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