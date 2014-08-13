<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// select
//$models = QActivityType::model()->forActivity($attribute)->findAll();
//$options = CHtml::listData($models, 'id', 'translation');
$default = array('' => Yii::t('coreMessages', 'not_set') );
$options = QActivityType::model()->activityVariants($attribute);
$options = CMap::mergeArray($default, $options);
echo $form->dropDownListRow($model, $attribute, $options);