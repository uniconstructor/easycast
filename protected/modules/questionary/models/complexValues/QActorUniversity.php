<?php

Yii::import('application.modules.questionary.extensions.behaviors.QSaveYearBehavior');

/**
 * Класс для работы с одним актерским ВУЗом
 */
class QActorUniversity extends QUniversityInstance
{
    /**
     * @var string - тип ВУЗа по умолчанию
     */
    protected $defaultType = 'theatre';
    
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias' => "actoruniversities",
            'with' => array(
                'university' => array(
                    'joinType'  => 'INNER JOIN',
                    'condition' => "`university`.`type`='{$this->defaultType}'",
                ),
            ),
            'order' => '`actoruniversities`.`timeend` DESC');
    }
}