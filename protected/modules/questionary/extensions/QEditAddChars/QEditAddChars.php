<?php

Yii::import('application.modules.questionary.extensions.QEditSimpleActivity.QEditSimpleActivity');
/**
 * Виджет для редактирования поля "Дополнительные характеристики"
 * @author frost
 *
 */
class QEditAddChars extends QEditSimpleActivity
{
    public $fieldName = 'addchar';
    public $modelName = 'Questionary';
}