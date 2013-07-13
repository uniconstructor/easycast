<?php

Yii::import('application.modules.questionary.extensions.QEditSimpleActivity.QEditSimpleActivity');
/**
 * Виджет для редактирования поля "пародист"
 * @author frost
 *
 */
class QEditParodistList extends QEditSimpleActivity
{
    public $fieldName = 'parodist';
    public $modelName = 'Questionary';
}