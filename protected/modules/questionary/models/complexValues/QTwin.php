<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Модель для хранения данных в поле анкеты "список двойников"
 */
class QTwin extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'twin';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        $parentScope  = parent::defaultScope();
        $currentScope = array(
            'alias'     => "twinlist",
            'condition' => "`twinlist`.`type`='twin'",
        );
        return CMap::mergeArray($parentScope, $currentScope);
    }
}