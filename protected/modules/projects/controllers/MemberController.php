<?php

/**
 * Контроллер для работы с заявками и подтвержденными участниками
 * @todo настроить права доступа
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
     * 
     * @todo при исаользовании токенов проверять статус приглашения
     */
    public function actionSetStatus()
    {
        if ( ! Yii::app()->request->isAjaxRequest OR ! Yii::app()->request->isPostRequest )
        {// разрешаем только POST AJAX-запросы
            throw new CHttpException(400, 'Only AJAX request allowed');
            Yii::app()->end();
        }
        $id     = Yii::app()->request->getPost('id');
        $member = $this->loadModel($id);
        $customerInviteId = Yii::app()->request->getPost('ciid', 0);
        $customerInvite   = null;
        $key  = Yii::app()->request->getPost('k1', '');
        $key2 = Yii::app()->request->getPost('k2', '');
        $refresh = Yii::app()->request->getPost('refresh', 0);
        
        if ( ! $newStatus = Yii::app()->request->getPost('status') )
        {
            throw new CHttpException(400, 'Необходимо указать статус');
            Yii::app()->end();
        }
        if ( $customerInviteId )
        {// происходит работа с заявками по токену
            if ( ! $customerInvite = CustomerInvite::model()->findByPk($customerInviteId) )
            {
                throw new CHttpException(500, 'Приглашение не найдено');
                Yii::app()->end();
            }
            if ( $customerInvite->key != $key OR $customerInvite->key2 != $key2 )
            {// ключи доступа не совпадают
                throw new CHttpException(500, 'Неправильная ссылка для смены статуса');
                Yii::app()->end();
            }
        }
        if ( ! $this->canSetStatus($member, $newStatus, $customerInvite) )
        {
            throw new CHttpException(500, 'Недостаточно полномочий для изменения статуса');
            Yii::app()->end();
        }
        
        if ( ! $member->setStatus($newStatus) )
        {// изменяем статус, если пройдены все проверки
            echo 'ERROR';
            Yii::app()->end();
        }
        if ( ! $refresh )
        {// обновлять содержимое кнопок не нужно
            echo 'OK';
            Yii::app()->end();
        }
        
        // отправляем html-код кнопок (для обновления)
        $widgetOptions = array(
            'member'         => $member,
            'refreshButtons' => true,
            'forceDisplayStatus' => true,
        );
        if ( $customerInvite )
        {
            $widgetOptions['customerInvite'] = $customerInvite;
        }
        $this->widget('application.modules.projects.extensions.MemberActions.MemberActions', $widgetOptions);
        Yii::app()->end();
    }
    
    /**
     * Проверить, разрешена ли смена статуса для заявки
     * @param ProjectMember $member - заявка или участник
     * @param string $newStatus - новый статус
     * @param CustomerInvite $customerInvite - приглашение заказчика на отбор актеров
     *                                         (если отбор работает по одноразовым ссылкам)
     * @return boolean
     * 
     * @todo проверить статус роли, мероприятия и проекта
     */
    protected function canSetStatus($member, $newStatus, $customerInvite=null)
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
        if ( $customerInvite instanceof CustomerInvite )
        {// проверяем, позволяет ли это приглашение менять статус этого участника
            switch ( $customerInvite->objecttype )
            {
                case 'project':
                    if ( $member->vacancy->event->projectid == $customerInvite->objectid )
                    {
                        return true;
                    }
                break;
                case 'event':
                    if ( $member->vacancy->eventid == $customerInvite->objectid )
                    {
                        return true;
                    }
                break;
                case 'vacancy':
                    if ( $member->vacancyid == $customerInvite->objectid )
                    {
                        return true;
                    }
                break;
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