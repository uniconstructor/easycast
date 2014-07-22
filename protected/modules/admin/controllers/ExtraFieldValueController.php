<?php

Yii::import('ext.EditableGrid.EditableGridController');

/**
 * Контроллер для редактирования списка категорий объектов
 * 
 * @todo после создания добавлять поле в несколько категорий
 */
class ExtraFieldValueController extends EditableGridController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'ExtraFieldValue';
    
    /**
     * @see EditableGridController::actionCreate()
     */
    public function actionCreate()
    {
        throw new CException('Этот контроллер предназначен только для редактирования 
            значений дополнительных полей');
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