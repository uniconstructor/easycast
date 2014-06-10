<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Модель для хранения данных в поле "типы вокала"
 */
class QVocalType extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'vocaltype';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        $parentScope  = parent::defaultScope();
        $currentScope = array(
            'alias'     => "vocaltypes",
            'condition' => "`vocaltypes`.`type`='vocaltype'",
        );
        return CMap::mergeArray($parentScope, $currentScope);
    }
}