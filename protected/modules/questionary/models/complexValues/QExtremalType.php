<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Вид экстремального спорта
 */
class QExtremalType extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'extremaltype';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "extremaltypes",
            'condition' => "`extremaltypes`.`type`='extremaltype'",
        );
    }
}