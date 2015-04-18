<?php

/**
 * Действие контроллера для редактирования модели
 * Редактирует саму модель, но не связанные с ней значения
 * 
 * @todo документировать
 */
class EcUpdateAction extends CAction
{
    /**
     * @var string the name of the model we are going to toggle values to
     */
    public $modelName;
    /**
     * @var bool whether to throw an exception if we cannot find a model requested by the id
     */
    public $exceptionOnNullModel = true;
    /**
     * @var array additional criteria to use to get the model
     */
    public $additionalCriteriaOnLoadModel = array();
    /**
     * @var mixed the route to redirect the call after updating attribute
     */
    public $redirectRoute;
    /**
     * @var mixed the response to return to an AJAX call when the attribute was successfully saved.
     */
    public $ajaxResponseOnSuccess = 1;
    /**
     * @var mixed the response to return to an AJAX call when failed to update the attribute.
     */
    public $ajaxResponseOnFailed  = 0;
    
    /**
     * @see CAction::run()
     *
     * @param integer $id
     * @param string $attribute
     *
     * @throws CHttpException
     */
    public function run()
    {
        // id модели
        $id         = Yii::app()->request->getParam('id');
        // поле модели
        $attribute  = Yii::app()->request->getParam('attribute');
        // обновляемое значение
        $value      = Yii::app()->request->getParam('value');
        // массив с обновляемыми значениями (если есть)
        $modelData  = Yii::app()->request->getPost($this->modelName);
        
        if ( Yii::app()->getRequest()->isPostRequest )
        {
            $model = $this->loadModel($id);
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
    
    /**
     * Loads the requested data model.
     *
     * @param integer $id the model ID
     *
     * @return CActiveRecord the model instance.
     * @throws CHttpException if the model cannot be found
     */
    protected function loadModel($id)
    {
        if ( ! $this->modelName AND Yii::app()->user->checkAccess('Admin') )
        {// задать вручную класс редактируемой модели разрешено только админу
            $modelName = trim(Yii::app()->request->getParam('modelName'));
        }
        if ( ! $this->modelName AND ($modelName instanceof CActiveRecord) )
        {// и только если все правильно
            $this->modelName = $modelName;
        }
        $finder = CActiveRecord::model($this->modelName);
        
        if ( $this->additionalCriteriaOnLoadModel )
        {
            $c = new CDbCriteria($this->additionalCriteriaOnLoadModel);
            $c->mergeWith(array(
                'condition' => $finder->tableSchema->primaryKey.'=:id',
                'params'    => array(':id' => $id),
            ));
            $model = $finder->find($c);
        }else
        {
            $model = $finder->findByPk($id);
        }
        if ( ! $model )
        {
            throw new CHttpException(404, 'Unable to find the requested object.');
        }
        return $model;
    }
    
    /**
     * Performs the AJAX validation.
     * @param CActiveRecord $model the model to be validated
     *
     * @todo придумать более цивилизованный способ сообщать об ошибках
     * @todo добавить проверку isAjaxRequest
     */
    protected function performAjaxValidation($model)
    {
        if ( ! Yii::app()->request->getPost($this->modelName) )
        {
            Yii::app()->end();
        }
        $result = CActiveForm::validate($model);
    
        if ( $result != '[]' )
        {// при сохранении обнаружены ошибки
            $errors = array();
            $result = CJSON::decode($result);
            foreach ( $result as $element )
            {
                $errors[] = current($element);
            }
            $message = "\n".implode("\n", $errors);
            throw new CHttpException(400, $message);
        }
    }
}