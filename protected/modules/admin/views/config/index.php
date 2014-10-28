<?php
/**
 * Главная страница настроек: показывает все системные настройки
 */
/* @var $this ConfigController */

// верхняя навигация
$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
    'Настройки'         => array('/admin/config'),
);

// общий список настроек
$this->widget('bootstrap.widgets.TbNavbar', array(
    'brand'    => 'Настройки',
    'fixed'    => false,
    'brandUrl' => array('/admin/config'),
    'items' => array(
        array(
            'class' => 'bootstrap.widgets.TbMenu',
            'items' => array(
                array(
                    'label' => 'Система',
                    'url'   => array('/admin/config'),
                ),
                array(
                    'label' => 'Анкета',
                    'url'   => array('/admin/config/view', 'type' => 'Questionary'),
                ),
                array(
                    'label' => 'Проект',
                    'url'   => array('/admin/config/view', 'type' => 'Project'),
                ),
                array(
                    'label' => 'Мероприятие',
                    'url'   => array('/admin/config/view', 'type' => 'ProjectEvent'),
                ),
                array(
                    'label' => 'Роль',
                    'url'   => array('/admin/config/view', 'type' => 'EventVacancy'),
                ),
            )
        )
    )
));

// все настройки
$this->widget('ext.EditableConfig.EditableConfig', array(
    'objectType'      => 'system',
    'objectId'        => 0,
    'createUrl'       => Yii::app()->createUrl('admin/config/createValue'),
    'updateUrl'       => Yii::app()->createUrl('admin/config/updateValue'),
    'deleteUrl'       => Yii::app()->createUrl('admin/config/deleteValue'),
    'updateObjectUrl' => Yii::app()->createUrl('admin/config/updateConfig'),
));

