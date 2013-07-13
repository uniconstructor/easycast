<?php

Yii::import('application.modules.questionary.extensions.QEditSimpleActivity.QEditSimpleActivity');
/**
 * Виджет для редактирования поля "Двойник"
 * @author frost
 *
 */
class QEditTwinList extends QEditSimpleActivity
{
    public $fieldName = 'twin';
    public $modelName = 'Questionary';
}