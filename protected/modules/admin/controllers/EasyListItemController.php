<?php

Yii::import('ext.EditableGrid.EditableGridController');

/**
 * Контроллер для редактирования элементов списка
 */
class EasyListItemController extends EditableGridController
{
    /**
     * @var string
     */
    protected $modelClass = 'EasyListItem';
    
    /**
     * Preview для письма с оповещением
     * 
     * @return void
     */
    public function actionView()
    {
        $id = Yii::app()->request->getParam('id');
        $item = $this->loadModel($id);
        
        $message = Yii::app()->curl->get($item->value);
        
        // @todo временно размещаем тут тест отправки оповещения
        /* @var $s3 Aws\S3\S3Client */
        $s3   = Yii::app()->getComponent('ecawsapi')->getS3();
        $user = Yii::app()->getModule('user')->user();
        
        Yii::app()->getComponent('ecawsapi')->sendMail($user->email, $item->description, $message);
        
        echo $message;
    }
    
    /**
     * Создать запись
     * 
     * @return void
     */
    public function actionCreate()
    {
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
                echo CJSON::encode($instance->getAttributes());
            }
        }
    }
    
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
        //$item->updateProxy($field, $value);
        $item->setAttribute($field, $value);
    
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