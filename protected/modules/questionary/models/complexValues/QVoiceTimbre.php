<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Класс для хранения тембров голоса, которыми владеет участник
 */
class QVoiceTimbre extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'voicetimbre';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        $parentScope  = parent::defaultScope();
        $currentScope = array(
            'alias'     => "voicetimbres",
            'condition' => "`voicetimbres`.`type`='voicetimbre'",
        );
        return CMap::mergeArray($parentScope, $currentScope);
    }
}