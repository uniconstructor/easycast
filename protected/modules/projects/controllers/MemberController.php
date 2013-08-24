<?php

/**
 * Контроллер для работы с заявками и подтвержденными участниками
 */
class MemberController extends Controller
{
    /**
     * (non-PHPdoc)
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('application.modules.projects.models.*');
    }
    
    /**
     * Изменить статус для заявки или подтвержденного участника (обработчик AJAX-запроса)
     * @throws CHttpException
     * @return null
     */
    public function actionSetStatus()
    {
        if ( ! Yii::app()->request->isAjaxRequest OR ! Yii::app()->request->isPostRequest )
        {// разрешаем только POST AJAX-запросы
            throw new CHttpException(500, 'Only AJAX request allowed');
        }
        $id     = Yii::app()->request->getPost('id');
        $member = $this->loadModel($id);
        
        if ( ! $newStatus = Yii::app()->request->getPost('status') )
        {
            throw new CHttpException(404, 'Необходимо указать статус');
        }
        if ( ! $this->canSetStatus($member, $newStatus) )
        {
            throw new CHttpException(500, 'Недопустимый статус');
        }
        
        if ( $member->setStatus($newStatus) )
        {// изменяем статус, если пройдены все проверки
            echo 'OK';
        }else
        {
            echo 'ERROR';
        }
        Yii::app()->end();
    }
    
    /**
     * Проверить, разрешена ли смена статуса для заявки
     * @param ProjectMember $member - заявка или участник
     * @param string $newStatus - новый статус
     * @return boolean
     */
    protected function canSetStatus($member, $newStatus)
    {
        if ( Yii::app()->user->checkAccess('Admin') AND $newStatus != 'canceled' )
        {// админам можно все кроме отмены заявок (эта функция только для участников)
            return true;
        }
        if ( Yii::app()->user->checkAccess('User') )
        {// обычным участникам позволяем только отменять свои заявки
            if ( $newStatus == 'canceled' AND Yii::app()->user->id == $member->member->user->id )
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = ProjectMember::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Заявка не найдена.');
        }
        return $model;
    }
}