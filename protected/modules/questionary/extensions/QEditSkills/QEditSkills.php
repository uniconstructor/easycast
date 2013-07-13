<?php

Yii::import('application.modules.questionary.extensions.QEditSimpleActivity.QEditSimpleActivity');
/**
 * Виджет для редактирования поля "Умения и навыки"
 * @author frost
 *
 */
class QEditSkills extends QEditSimpleActivity
{
    public $fieldName = 'skill';
    public $modelName = 'Questionary';
}