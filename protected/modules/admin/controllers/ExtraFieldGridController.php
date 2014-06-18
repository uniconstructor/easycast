<?php

Yii::import('ext.EditableGrid.EditableGridController');

/**
 * Контроллер для редактирования списка категорий объектов
 * 
 * @todo после создания добавлять поле в несколько категорий
 */
class ExtraFieldGridController extends EditableGridController
{
    /**
     * @var string - класс модели сложного значения
     */
    protected $modelClass = 'ExtraField';
    
    /**
     * @see EditableGridController::actionCreate()
     */
    public function actionCreate()
    {
        $categoryId = Yii::app()->request->getParam('categoryId', 0);
        // создаем модель для добавления
        $instance = $this->initModel();
        // ajax-проверка введенных данных
        $this->performAjaxValidation($instance);

        if ( $instanceData = Yii::app()->request->getPost($this->modelClass) )
        {// проверяем права на добавление данных
            $this->checkAccess($instance);
            $instance->attributes = $instanceData;

            if ( ! $instance->save() )
            {
                throw new CHttpException(500, 'Ошибка при сохранении данных');
            }else
            {
                if ( $category = Category::model()->findByPk($categoryId) )
                {// если поле нужно сразу же после создания добавить в раздел
                    $extraInstance = new ExtraFieldInstance();
                    $extraInstance->fieldid    = $instance->id;
                    $extraInstance->objecttype = 'category';
                    $extraInstance->objectid   = $category->id;
                    $extraInstance->filling    = 'required';
                    if ( ! $extraInstance->save() )
                    {
                        throw new CHttpException(500, 'Ошибка при сохранении данных');
                    }
                }
                echo CJSON::encode($instance->getAttributes());
            }
        }
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