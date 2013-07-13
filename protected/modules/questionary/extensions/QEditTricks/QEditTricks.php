<?php

Yii::import('application.modules.questionary.extensions.QEditSimpleActivity.QEditSimpleActivity');
/**
 * Виджет для редактирования поля "Выполнение трюков"
 * @author frost
 *
 */
class QEditTricks extends QEditSimpleActivity
{
    public $fieldName = 'trick';
    public $modelName = 'Questionary';
}