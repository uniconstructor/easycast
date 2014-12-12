<?php

/**
 * Контроллер для календаря событий
 * 
 * @todo удалить модуль и перенести все действия в action
 */
class CalendarController extends Controller
{
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
     * Отображение главной страницы календаря событий
     * 
     * @return null
     */
    public function actionIndex()
    {
        $projectType = Yii::app()->request->getParam('type');
        $projectId   = Yii::app()->request->getParam('projectid');
        $userId      = Yii::app()->request->getParam('userid');
        $onlyActive  = Yii::app()->request->getParam('onlyactive', false);
        
        $this->render('calendar', array(
            'type'       => $projectType,
            'projectid'  => $projectId,
            'userid'     => $userId,
            'onlyactive' => $onlyActive,
        ));
    }
    
    /**
     * Получить список событий календаря через AJAX
     * @todo сделать раскрывающееся меню при клике на событие
     * 
     * @return string json-список событий
     */
    public function actionGetEvents()
    {
        Yii::import('projects.models.*');
        
        $timeStart = Yii::app()->request->getParam('start');
        $timeEnd   = Yii::app()->request->getParam('end');
        $projectId = Yii::app()->request->getParam('projectid');
        $userId    = null;
        if ( ! Yii::app()->user->isGuest )
        {
            if ( Yii::app()->user->checkAccess('Admin') OR
                 Yii::app()->getModule('user')->user()->id == Yii::app()->request->getParam('userid') )
            {
                $userId = Yii::app()->request->getParam('userid');
            }
        }
        $projectType = Yii::app()->request->getParam('type');
        $onlyActive  = Yii::app()->request->getParam('onlyactive', false);
        $events = ProjectEvent::model()->getCalendarEvents($timeStart,$timeEnd,$projectId,$userId,$projectType,$onlyActive);
        
        echo $events;
    }
} 