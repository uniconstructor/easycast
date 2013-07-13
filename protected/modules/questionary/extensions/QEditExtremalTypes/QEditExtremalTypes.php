<?php

Yii::import('application.modules.questionary.extensions.QEditSimpleActivity.QEditSimpleActivity');
/**
 * Виджет для редактирования поля "Экстремальные виды спорта"
 * @author frost
 *
 */
class QEditExtremalTypes extends QEditSimpleActivity
{
    public $fieldName = 'extremaltype';
    public $modelName = 'Questionary';
}