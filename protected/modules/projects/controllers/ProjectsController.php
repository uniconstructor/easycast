<?php

/**
 * Контроллер отображения информации о проектах
 */
class ProjectsController extends Controller
{
    /**
     * @var максимальное количество проектов на странице
     */
    const MAX_SECTION_ITEMS = 36;
    
    /**
     * Отобразить главную страницу со списком проектов
     */
	public function actionIndex()
	{
	    // Получаем раздел проектов, который надо просмотреть (если указано)
	    $type = Yii::app()->request->getPost('type');
	    // Получаем дополнительные данные для поиска (если пользователь захотел свой поиск)
	    $search = Yii::app()->request->getPost('search');
	     
	    $criteria = Yii::app()->getModule('projects')->getProjectsCriteria();
	    $dataProvider = new CActiveDataProvider('Project', 
            array(
                'criteria' => $criteria,
                'pagination' => array('pageSize' => self::MAX_SECTION_ITEMS),
            )
        );
	     
	    $this->render('index', array('dataProvider' => $dataProvider));
	}
	
	/**
	 * отобразить информацию о проекте, мероприятии или вакансии
	 * 
	 * @return null
	 */
	public function actionView()
	{
	    $vacancyId = Yii::app()->request->getParam('vacancyid', 0);
	    $eventId   = Yii::app()->request->getParam('eventid', 0);
	    $projectId = Yii::app()->request->getParam('id', 0);
	    
	    $vacancy   = null;
        $event     = null;
        $project   = null;
        
	    if ( $vacancyId )
	    {// отображаем вакансию
	        $vacancy   = $this->loadVacancyModel($vacancyId);
	        $event     = $vacancy->event;
	        $project   = $vacancy->event->project;
	        $eventId   = $vacancy->event->id;
	        $projectId = $vacancy->event->project->id;
	    }elseif ( $eventId )
	    {// отображаем мероприятие
	        $event     = $this->loadEventModel($eventId);
	        $project   = $event->project;
	        $eventId   = $event->id;
	        $projectId = $event->project->id;
	    }else
	   {// отображаем проект
	        $project = $this->loadProjectModel($projectId);
	    }
	    
	    $this->render('view', array(
	        'vacancy'   => $vacancy,
	        'event'     => $event,
	        'project'   => $project,
	        'vacancyId' => $vacancyId,
	        'eventId'   => $eventId,
	        'projectId' => $projectId, 
	    ));
	}
	
	protected function loadProjectModel($id)
	{
	    $model=Project::model()->findByPk($id);
	    if($model===null)
	        throw new CHttpException(404,'Проект не найден');
	    return $model;
	}
	
	protected function loadEventModel($id)
	{
	    $model=ProjectEvent::model()->findByPk($id);
	    if($model===null)
	        throw new CHttpException(404,'Мероприятие не найдено');
	    return $model;
	}
	
	protected function loadVacancyModel($id)
	{
	    $model=EventVacancy::model()->findByPk($id);
	    if($model===null)
	        throw new CHttpException(404,'Вакансия не найдена');
	    return $model;
	}
}