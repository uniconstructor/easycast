<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Образы пародиста
 */
class QParodist extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'parodist';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        $parentScope  = parent::defaultScope();
        $currentScope = array(
            'alias'     => "parodistlist",
            'condition' => "`parodistlist`.`type`='parodist'",
        );
        return CMap::mergeArray($parentScope, $currentScope);
    }
}