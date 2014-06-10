<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Класс для работы с дополнительными характеристиками внешности участника
 */
class QAddChar extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'addchar';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        $parentScope  = parent::defaultScope();
        $currentScope = array(
            'alias'     => "addchars",
            'condition' => "`addchars`.`type`='addchar'",
        );
        return CMap::mergeArray($parentScope, $currentScope);
    }
}