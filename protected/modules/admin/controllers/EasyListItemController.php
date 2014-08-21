<?php

Yii::import('ext.EditableGrid.EditableGridController');

/**
 * Контроллер для редактирования элементов списка
 */
class EasyListItemController extends EditableGridController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'EasyListItem';
    
    /**
     * Обновить запись
     * @return void
     *
     * @todo возвращать ошибки, связанные с другими полями
     */
    public function actionUpdate()
    {
        $id    = Yii::app()->request->getParam('pk');
        $field = Yii::app()->request->getParam('name');
        $value = Yii::app()->request->getParam('value');
        
        // загружаем элемент списка
        $item = $this->loadModel($id);
        $this->checkAccess($item);
        // обновляем привязанный элемент
        $item->updateProxy($field, $value);
    
        if ( ! $item->save() )
        {// не удалось обновить запись в поле
            throw new CHttpException(500, $item->getError($field));
        }
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