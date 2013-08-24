<?php

/**
 * Класс для мероприятия. Отвечает только за обработку AJAX-запросов 
 * Отображением занимается projectController
 * 
 * @todo настроить права доступа
 */
class EventController extends Controller
{
    /**
     * @deprecated
     * @todo перемещено в VacancyController, удалить при рефакторинге
     */
    public function actionAddApplication()
    {
        if ( ! Yii::app()->request->isAjaxRequest OR ! Yii::app()->request->isPostRequest )
        {// разрешаем только POST AJAX-запросы
            throw new CHttpException(500, 'Only AJAX request allowed');
        }
        if ( Yii::app()->user->isGuest )
        {// проверяем права
            throw new CHttpException(500, 'Operation not permitted');
        }
        $vacancyId = Yii::app()->request->getPost('vacancyid', 0);
        if ( ! $vacancy = EventVacancy::model()->findByPk($vacancyId) )
        {
            throw new CHttpException(500, 'Необходимо выбрать вакансию');
        }
        
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
     * @deprecated
     * @todo перемещено в VacancyController, удалить при рефакторинге
     */
    protected function createApplication($vacancy, $questionaryId=null)
    {
        if ( ! $questionaryId )
        {
            $questionaryId = Yii::app()->getModule('user')->user()->questionary->id;
        }
        $request = new MemberRequest();
        $request->vacancyid = $vacancy->id;
        $request->memberid  = $questionaryId;
        return $request->save();
    }
    
    /**
     * Отобразить событие (редирект для совместимости)
     * @param int $id
     */
    public function actionView($id)
    {
        $this->redirect(Yii::app()->createUrl('/projects/projects/view', array('eventid' => $id)));
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = ProjectEvent::model()->findByPk($id);
        if ( $model === null )
        {
            throw new CHttpException(404, 'Мероприятие не найдено id='.$id);
        }
        return $model;
    }
}