<?php

Yii::import('ext.EditableGrid.EditableGridController');
Yii::import('catalog.models.*');

/**
 * 
 */
class CatalogSectionGridController extends EditableGridController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'CatalogSection';

    /**
     * Проверить, есть ли у пользователя доступ к добавлению, редактированию или удалению объекта
     * @param CActiveRecord $item
     * @return void
     */
    protected function checkAccess($item)
    {
        if ( ! Yii::app()->user->checkAccess('Admin') )
        {
            throw new CHttpException(400, 'Ошибка при изменении записи');
        }
        return true;
    }
}