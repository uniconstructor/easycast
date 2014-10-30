<?php
/**
 * Элементы списка
 */
/* @var $this EasyListManager */

$this->widget('admin.extensions.EasyListManager.ListItemsGrid', array(
    'easyList'    => $this->easyList,
    //'model'       => $config->getTargetObject(),
    'modalHeader' => 'Добавить шаблон',
));