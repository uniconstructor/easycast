<?php

/**
 * Контроллер отображения информации о проектах, мероприятиях или вакансиях
 * 
 * @todo настроить права доступа
 * @todo перенести функцию просмотра в ProjectController
 */
class ProjectsController extends Controller
{
    /**
     * @var максимальное количество проектов на одной странице
     */
    const MAX_SECTION_ITEMS = 36;
    
    /**
     * @return array
     *
     * @todo настроить проверку прав на основе RBAC
     */
    public function filters()
    {
        $baseFilters = parent::filters();
        $newFilters  = array(
            // фильтр для подключения YiiBooster 3.x (bootstrap 2.x)
            array(
                'ext.bootstrap.filters.BootstrapFilter',
            ),
        );
        return CMap::mergeArray($baseFilters, $newFilters);
    }
    
    /**
     * Отобразить главную страницу со списком проектов
     * 
     * @return void
     */
	public function actionIndex()
	{
	    $statuses = array(swProject::ACTIVE, swProject::SUSPENDED, swProject::FINISHED);
	    // @todo получаем проекты по типу
	    $type   = Yii::app()->request->getPost('type');
	    // @todo получаем дополнительные данные для поиска (если нужен свой поиск по проектам)
	    $search = Yii::app()->request->getPost('search');
	    
	    // исключаем из списка проектов черновики
	    $criteria = new CDbCriteria();
	    $criteria->scopes = array(
	        'withStatus' => array($statuses),
	    );
	    if ( Yii::app()->getModule('user')->getViewMode() === 'customer' )
	    {// для заказчиков отображаем лучшие проекты по рейтингу
	        $criteria->scopes = array('bestRated');
	    }else
	    {// для участников отображаем последние проекты
	        $criteria->scopes = array('lastCreated');
	    }
	    $dataProvider = new CActiveDataProvider('Project', array(
            'criteria'   => $criteria,
            'pagination' => false,
        ));
	    
	    $this->render('index', array('dataProvider' => $dataProvider));
	}
	
	/**
	 * Отобразить информацию о проекте, мероприятии или вакансии
	 * 
	 * @return void
	 */
	public function actionView()
	{
	    $vacancyId = Yii::app()->request->getParam('vacancyid', 0);
	    $eventId   = Yii::app()->request->getParam('eventid', 0);
	    $projectId = Yii::app()->request->getParam('id', 0);
	    $activeTab = Yii::app()->request->getParam('activeTab', 'main');
	    
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
	        'activeTab' => $activeTab,
	    ));
	}
	
	/**
	 * Получить модель проекта или отобразить сообщение о том что проект не найден
	 * 
	 * @param int $id
	 * @throws CHttpException
	 * @return Project
	 * 
	 * @deprecated
	 */
	protected function loadProjectModel($id)
	{
	    $model = Project::model()->findByPk($id);
	    if ( $model === null )
	    {
	        throw new CHttpException(404, 'Проект не найден');
	    }
	    return $model;
	}
	
	/**
	 * Получить модель мероприятия или отобразить сообщение о том что мероприятие не найдено
	 * 
	 * @param int $id
	 * @throws CHttpException
	 * @return ProjectEvent
	 * 
	 * @deprecated
	 */
	protected function loadEventModel($id)
	{
	    $model = ProjectEvent::model()->findByPk($id);
	    if ( $model === null )
	    {
	        throw new CHttpException(404, 'Мероприятие не найдено');
	    }
	    return $model;
	}
	
	/**
	 * Получить модель вакансии или отобразить сообщение о том что вакансия не найдена
	 * 
	 * @param int $id
	 * @throws CHttpException
	 * @return EventVacancy
	 * 
	 * @deprecated
	 */
	protected function loadVacancyModel($id)
	{
	    $model = EventVacancy::model()->findByPk($id);
	    if ( $model === null )
	    {
	        throw new CHttpException(404, 'Роль не найдена');
	    }
	    return $model;
	}
}