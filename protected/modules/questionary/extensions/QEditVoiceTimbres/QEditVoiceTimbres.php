<?php

Yii::import('application.modules.questionary.extensions.QEditSimpleActivity.QEditSimpleActivity');
/**
 * Виджет для редактирования поля "тембр голоса"
 * @author frost
 *
 */
class QEditVoiceTimbres extends QEditSimpleActivity
{
    public $fieldName = 'voicetimbre';
    public $modelName = 'Questionary';
}