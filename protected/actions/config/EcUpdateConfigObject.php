<?php

/**
 * Действие контроллера для редактирования объекта настройки
 * 
 * @todo права доступа при редактировании системных настроек
 */
class EcUpdateConfigObject extends EcUpdateConfigValue
{
    /**
     * @see EcUpdateConfigValue::run()
     */
    public function run()
    {
        // id настройки
        $id      = Yii::app()->request->getParam('id');
        // поле модели
        $attribute = Yii::app()->request->getParam('attribute');
        // обновляемое значение
        $value     = Yii::app()->request->getParam('value');
        // массив с обновляемыми значениями (если есть)
        $modelData = Yii::app()->request->getPost('Config');
        
        if ( Yii::app()->getRequest()->isPostRequest )
        {
            // загружаем модель настройки
            $config  = $this->loadConfigModel($id);
            
            // готовим настройку к редактированию, защищая системные настройки от случайных правок
            $config->prepareUpdateValue();
            
            if ( is_array($modelData) AND ! empty($modelData) )
            {
                $model->attributes = $modelData;
                $success = $model->save();
            }else
            {
                $model->$attribute = $value;
                $success = $model->save(false, array($attribute));
            }
        
            if ( Yii::app()->getRequest()->isAjaxRequest )
            {
                echo $success ? $this->ajaxResponseOnSuccess : $this->ajaxResponseOnFailed;
                exit(0);
            }
            if ( $this->redirectRoute !== null )
            {
                $this->getController()->redirect($this->redirectRoute);
            }
        }else
        {
            throw new CHttpException(Yii::t('zii', 'Invalid request'));
        }
    }
}