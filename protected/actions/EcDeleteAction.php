<?php


class EcDeleteAction extends EcUpdateAction
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
        $id = Yii::app()->request->getParam('id');
        
        if ( Yii::app()->getRequest()->isPostRequest )
        {
            $model   = $this->loadModel($id);
            $success = $model->delete();
        
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