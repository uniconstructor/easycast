<?php

Yii::import('application.modules.questionary.extensions.QEditSimpleActivity.QEditSimpleActivity');
/**
 * Виджет для редактирования поля "Типы вокала"
 * @author frost
 *
 */
class QEditVocalTypes extends QEditSimpleActivity
{
    public $fieldName = 'vocaltype';
    public $modelName = 'Questionary';
}