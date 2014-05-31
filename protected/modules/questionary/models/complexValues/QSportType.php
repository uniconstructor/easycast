<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * 
 */
class QSportType extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'sporttype';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "sporttypes",
            'condition' => "`sporttypes`.`type`='sporttype'",
        );
    }
}