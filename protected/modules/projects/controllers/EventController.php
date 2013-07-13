<?php

/**
 * Класс для отображения информации о событии пользователю
 */
class EventController extends Controller
{
    /**
     * Отобразить событие
     * @param int $id
     */
    public function actionView($id)
    {
        $model = $this->loadModel($id);
        $this->render('view', array(
                'model' => $model,
            )
        );
    }
    
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
            throw new CHttpException(500, 'Only AJAX request allowed');
        }
        if ( Yii::app()->user->isGuest )
        {// проверяем права
            throw new CHttpException(500, 'Operation not permitted');
        }
        $vacancyId = Yii::app()->request->getPost('vacancyid', 0);
        if ( ! $vacancy = EventVacancy::model()->findByPk($vacancyId) )
        {
            throw new CHttpException(500,'Необходимо выбрать вакансию');
        }
        
        // Создаем и сохраняем новый запрос на участие
        $request = new MemberRequest();
        $request->vacancyid = $vacancy->id;
        $request->memberid  = Yii::app()->getModule('user')->user()->questionary->id;
        if ( $request->save() )
        {
            echo 'OR';
        }else
       {
            echo 'ERROR';
        }
        Yii::app()->end();
    }
    
    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=ProjectEvent::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'Мероприятие не найдено id='.$id);
        return $model;
    }
}