<?php

/**
 * Класс для совершения операций с вакансией
 * Этот контроллер обрабатывает только AJAX-запросы, информация по вакансиям обычно выводится виджетами
 */
class VacancyController extends Controller
{
    /**
     * Подать заявку на участие в мероприятии (указав определенную вакансию)
     * Подача заявки происхочить через AJAX-запрос, методом POST
     *
     * @todo языковые строки
     * @todo более подробная проверка прав
     */
    public function actionAddApplication()
    {
        if ( ! Yii::app()->request->isAjaxRequest OR ! Yii::app()->request->isPostRequest )
        {// разрешаем только POST AJAX-запросы
            throw new CHttpException(400, 'Only AJAX request allowed');
        }
        if ( Yii::app()->user->isGuest )
        {// проверяем права
            throw new CHttpException(400, 'Operation not permitted');
        }
        // получаем вакансию
        $vacancyId = Yii::app()->request->getPost('vacancyId', null);
        $vacancy   = $this->loadModel($vacancyId);
    
        // Создаем и сохраняем новый запрос на участие
        if ( $this->createApplication($vacancy) )
        {
            echo 'OR';
        }else
        {
            echo 'ERROR';
        }
    
        Yii::app()->end();
    }
    
    /**
     * Подать заявку на вакансию по токену
     *
     * @return null
     * @todo сделать проверку статуса вакансии и статуса заявки. Если они устарели - выводить сообщение
     * @todo выводить сообщение, если участник не подходит по критериям вакансии
     */
    public function actionAddApplicationByToken()
    {
        if ( ! Yii::app()->request->isAjaxRequest OR ! Yii::app()->request->isPostRequest )
        {// разрешаем только POST AJAX-запросы
            throw new CHttpException(400, 'Only AJAX request allowed');
        }
        
        if ( ! $key = Yii::app()->request->getPost('key', null) )
        {// не передан токен
            throw new CHttpException(500, 'Token not found');
        }
        $vacancyId = Yii::app()->request->getPost('vacancyId', null);
        $vacancy   = $this->loadModel($vacancyId);
        
        $inviteId = Yii::app()->request->getParam('inviteId', null);
        if ( ! $invite = EventInvite::model()->findByPk($inviteId) )
        {
            throw new CHttpException(404, 'Приглашение не найдено');
        }
        
        if ( $key != $invite->subscribekey )
        {// ключ одноразовой ссылки не подходит - что-то тут не так...
            throw new CHttpException(404, 'Страница не найдена');
        }
        
        // Создаем и сохраняем новый запрос на участие
        if ( $this->createApplication($vacancy, $invite->questionaryid ) )
        {
            echo 'OR';
        }else
        {
            echo 'ERROR';
        }
        
        Yii::app()->end();
    }
    
    /**
     * Создать заявку на участии в мероприятии
     * (выполняется после всех проверок)
     *
     * @param EventVacancy $vacancy
     * @return null
     *
     * @todo сделать проверку - не является ли текущий участник гостем, если questionaryid не указан
     */
    protected function createApplication($vacancy, $questionaryId=null)
    {
        if ( ! $questionaryId )
        {
            $questionaryId = Yii::app()->getModule('user')->user()->questionary->id;
        }
        if ( ! $vacancy->isAvailableForUser($questionaryId) )
        {// участник не подходит по критериям вакансии
            return false;
        }
        
        $request = new MemberRequest();
        $request->vacancyid = $vacancy->id;
        $request->memberid  = $questionaryId;
        return $request->save();
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = EventVacancy::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Роль не найдена id='.$id);
        }
        return $model;
    }
}