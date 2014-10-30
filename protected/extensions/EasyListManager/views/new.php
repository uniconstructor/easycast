<?php
/**
 * Форма создания нового списка
 */
/* @var $this EasyListManager */

// форма создания списка работает полностью через AJAX
echo Sweeml::beginAjaxForm($this->createUrl);

// название
echo Sweeml::activeLabelEx($this->easyList, 'name');
echo Sweeml::activeTextField($this->easyList, 'name');
// описание
echo Sweeml::activeLabelEx($this->easyList, 'description');
echo Sweeml::activeTextField($this->easyList, 'description');
// требовать уникальность элементов
echo Sweeml::activeLabelEx($this->easyList, 'unique');
echo Sweeml::activeRadioButtonList($this->easyList, 'unique', array('1' => 'Да', '0' => 'Нет'));
// дополнение списка 
echo Sweeml::activeLabelEx($this->easyList, 'triggerupdate');
echo Sweeml::activeRadioButtonList($this->easyList, 'triggerupdate', $this->easyList->getTriggerOptionsList());
// очистка списка
echo Sweeml::activeLabelEx($this->easyList, 'triggercleanup');
echo Sweeml::activeRadioButtonList($this->easyList, 'triggercleanup', $this->easyList->getTriggerOptionsList());
// @todo интервал дополнения
// @todo интервал очистки
// кнопка отправки
$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'type'       => 'success',
    'size'       => 'large',
    'label'      => Yii::t('coreMessages', 'save'),
));

echo Sweeml::endForm();