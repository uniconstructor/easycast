<?php

Yii::import('ext.EditableGrid.EditableGridController');

/**
 * Контроллер для добавления списка разделов анкеты
 */
class CatalogSectionGridController extends EditableGridController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'CatalogSection';
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('catalog.models.*');
        parent::init();
    }

    /**
     * Проверить, есть ли у пользователя доступ к добавлению, редактированию или удалению объекта
     * @param CActiveRecord $item
     * @return bool
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