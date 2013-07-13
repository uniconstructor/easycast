<?php

Yii::import('application.modules.questionary.extensions.behaviors.QSaveYearBehavior');

/**
 * Класс для работы с одним оконченным музыкальным ВУЗом
 */
class QMusicUniversity extends QUniversityInstance
{
    /**
     * @var string - тип ВУЗа по умолчанию
     */
    protected $defaultType = 'music';

    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias' => "musicuniversities",
            'with'  => array(
                'university' => array(
                    'joinType'  => 'INNER JOIN',
                    'condition' => "`university`.`type`='{$this->defaultType}'",
                ),
            ),
            'order' => '`musicuniversities`.`timeend` DESC');
    }
    
}
