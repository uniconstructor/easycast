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
        return array(
            'alias'     => "parodistlist",
            'condition' => "`parodistlist`.`type`='parodist'",
        );
    }
}