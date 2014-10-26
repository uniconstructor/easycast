<?php

/**
 * Действие контроллера для удаления значения настройки
 * 
 * @todo запретить удалять чужие значения
 */
class EcDeleteConfigValue extends EcUpdateConfigValue
{
    /**
     * @var bool whether to throw an exception if we cannot find a model requested by the id
     */
    public $exceptionOnNullModel = false;
    
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
        $id      = Yii::app()->request->getParam('id');
        // загружаем модель настройки
        $config  = $this->loadConfigModel($id);
        // id значения внутри настройки (для редактирования элементов в настройках со списком значений)
        // настройки, которые хранят одно значение используют поле valueid вместо этого параметра
        $optonId = Yii::app()->request->getParam('optonId', $config->valueid);
        
        // модель значения зависит от настройки: 
        // (настройка может ссылаться на на модели разных классов для хранения значений)
        $this->modelName = $config->valuetype;
        // загружаем модель значения настройки
        $valueModel = $this->loadModel($config->valueid);
        
        if ( $config->isSingle() )
        {// настройка с одним значением - проcто обновляем поле связанной записи
            if ( $success = $valueModel->delete() )
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
            if ( $success = $optionModel->delete() )
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
}