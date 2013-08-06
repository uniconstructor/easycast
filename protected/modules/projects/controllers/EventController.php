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
     * Подать заявку на участие в мероприятии (указав определенную вакансию)
     * Подача заявки происхочить через AJAX-запрос, методом POST
     * 
     * @todo языковые строки
     * @todo более подробная проверка прав
     * @todo переместить в VacancyController, убрать отсюда и переписать весь старый код, посылающий запросы сюда
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
     * Создать заявку на участии в мероприятии
     * (выполняется после всех проверок)
     *  
     * @param EventVacancy $vacancy
     * @return null
     * 
     * @todo сделать проверку - не является ли текущий участник гостем, если questionaryid не указан
     * @todo переместить в VacancyController
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
     * Отобразить событие
     * @param int $id
     *
     * @deprecated
     * @todo удалить при рефакторинге, не используется
     */
    public function actionView($id)
    {
        throw new CHttpException('500', 'DEPRECATED');
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
            throw new CHttpException(404,'Мероприятие не найдено id='.$id);
        }
        return $model;
    }
}