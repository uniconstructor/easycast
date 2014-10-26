<?php

/**
 * Действие контроллера для редактирования значения настройки
 * 
 * @todo добавить возможность обновлять любое поле связанного значения
 */
class EcUpdateConfigValue extends EcUpdateAction
{
    /**
     * @see EcUpdateAction::run()
     */
    public function run()
    {
        if ( ! Yii::app()->getRequest()->isPostRequest )
        {
            throw new CHttpException(Yii::t('zii', 'Invalid request'));
        }
        // id настройки
        $id         = Yii::app()->request->getParam('id');
        // загружаем модель настройки
        $config     = $this->loadConfigModel($id);
        // id значения внутри настройки (для редактирования элементов в настройках со списком значений)
        // настройки, которые хранят одно значение не используют этот параметр
        $optonId    = Yii::app()->request->getParam('optonId', 0);
        // обновляемое значение настройки
        $value      = Yii::app()->request->getParam('value');
        
        // модель значения зависит от настройки: 
        // (настройка может ссылаться на на модели разных классов для хранения значений)
        $this->modelName = $config->valuetype;
        // загружаем модель значения настройки
        $valueModel = $this->loadModel($config->valueid);
        // определяем обновляемое поле в объекте значения настройки
        // (используется редко - как правило оно задано в модели)
        $valueField = Yii::app()->request->getParam('valueField', $config->valuefield);
        
        if ( $config->isSingle() )
        {// настройка с одним значением - проcто обновляем поле связанной записи
            $valueModel->$valueField = $value;
            if ( $success = $valueModel->save(true, array($valueField)) )
            {// помечаем настройку отредактированной
                $config->markModified();
            }
        }else
        {// настройка содержит список значений - значит в качестве 
            // объекта значения она использует список (EasyList)
            // @todo проверить возможность редактировать разнородные значения списков 
            $this->modelName = $valueModel->itemtype;
            // загружаем модель элемента списка
            $optionModel = $this->loadModel($optonId);
            // сохраняем новое значение элемента
            $optionModel->$valueField = $value;
            if ( $success = $optionModel->save(true, array($valueField)) )
            {// помечаем настройку отредактированной
                $config->markModified();
            }
        }
        // выполняем оставшиеся операции
        if ( Yii::app()->getRequest()->isAjaxRequest )
        {
            echo $success ? $this->ajaxResponseOnSuccess : $this->ajaxResponseOnFailed;
            exit(0);
        }
        if ( $this->redirectRoute !== null )
        {
            $this->getController()->redirect($this->redirectRoute);
        }
    }
    
    /**
     * Загрузить модель редактируемой настройки
     * 
     * @param integer $id - id настройки (модель Config)
     *
     * @return Config the model instance.
     * @throws CHttpException if the model cannot be found
     */
    protected function loadConfigModel($id)
    {
        $finder = CActiveRecord::model('Config');
        if ( ! $model = $finder->findByPk($id) )
        {
            throw new CHttpException(404, 'Unable to find the requested object.');
        }
        return $model;
    }
}