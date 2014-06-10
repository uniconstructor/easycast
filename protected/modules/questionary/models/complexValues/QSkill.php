<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Класс для работы с полем анкеты "дополнительные умения и навыки"
 */
class QSkill extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'skill';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        $parentScope  = parent::defaultScope();
        $currentScope = array(
            'alias'     => "skills",
            'condition' => "`skills`.`type`='skill'",
        );
        return CMap::mergeArray($parentScope, $currentScope);
    }
}