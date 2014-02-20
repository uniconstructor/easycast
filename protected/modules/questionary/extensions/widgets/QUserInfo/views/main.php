<?php
/**
 * Основная информация: внешность и другие параметры
 * @todo изменить верстку таблицы: пока выводим стандартный TbDetailView
 */
/* @var $this QUserInfo */
/* @var $data array */

$this->widget('bootstrap.widgets.TbDetailView', array(
    'data'       => $data,
    'attributes' => $attributes,
));