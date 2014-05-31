<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Класс для хранения данных из поля анкеты "выполнение трюков"
 */
class QTrick extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'trick';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "tricks",
            'condition' => "`tricks`.`type`='trick'",
        );
    }
}