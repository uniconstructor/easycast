<?php

class EcCreateAction extends EcUpdateAction
{
    /**
     * @var bool whether to throw an exception if we cannot find a model requested by the id
     */
    public $exceptionOnNullModel = false;
    
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
        if ( $modelData = Yii::app()->request->getPost($this->modelClass) )
        {
            $model = new $this->modelName;
            $model->attributes = $modelData;
            
            $this->performAjaxValidation($model);
            
            $success = $model->save();
        
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
        $finder = CActiveRecord::model($this->modelName);
        
        if ( $this->additionalCriteriaOnLoadModel )
        {
            $c = new CDbCriteria($this->additionalCriteriaOnLoadModel);
            $c->mergeWith(
                array(
                    'condition' => $finder->tableSchema->primaryKey . '=:id',
                    'params'    => array(':id' => $id),
                )
            );
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
}