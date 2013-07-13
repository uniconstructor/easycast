<?php

Yii::import('application.modules.questionary.extensions.QEditSimpleActivity.QEditSimpleActivity');
/**
 * Виджет для редактирования поля "Виды спорта"
 * @author frost
 *
 */
class QEditSportTypes extends QEditSimpleActivity
{
    public $fieldName = 'sporttype';
    public $modelName = 'Questionary';
}