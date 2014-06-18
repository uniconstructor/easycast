<?php

Yii::import('ext.EditableGrid.EditableGridController');

/**
 * Контроллер для прикрепления разделов анкет к другим объектам системы
 */
class SectionInstanceGridController extends EditableGridController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'CatalogSectionInstance';
    
    /**
     * @see EditableGridController::init()
     */
    public function init()
    {
        Yii::import('catalog.models.*');
        parent::init();
    }

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