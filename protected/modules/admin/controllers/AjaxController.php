<?php

/**
 * Контроллер для работы с AJAX-страницами новой админки
 */
class AjaxController extends Controller
{
    /**
     * @var string
     */
    public $layout = '//layouts/index';
    /**
     * @var array - массив с дополнительными графиками вверху страницы 
     */
    public $sparks = array();
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        // для просмотра заявок в расширенном виде: убираем все скрипты и стили
        // (они все равно не совместимы с админской темой)
        // и используем только то что подключается из нее
        Yii::app()->clientScript->enableJavaScript = false;
        // в админке переключаемся на специальную тему оформления
        Yii::app()->setTheme('admin');
         
        parent::init();
    }
    
    /**
     * 
     * @return void
     */
    public function actionIndex()
    {
        
    }
    
    /**
     * @return void
     */
    public function actionAgregateMembers()
    {
        $vacancyid = Yii::app()->request->getParam('vid');
        if ( $vacancy = EventVacancy::model()->findByPk($vacancyid) )
        {
            // задаем меню в навигации в зависисости от того сколько у нас разделов
            $categories = array();
            foreach ( $vacancy->catalogSectionInstances as $instance )
            {
                $categories[] = array(
                    'label' => $instance->section->name,
                    'url'   => Yii::app()->createUrl('/admin/ajax/fullMemberList', array(
                        'vid'  => $vacancyid,
                        'siid' => $instance->section->id,
                    )),
                );
            }
            $allItemsUrl = Yii::app()->createUrl('/admin/ajax/agregateMembers', array(
                'vid'  => $vacancyid,
                'siid' => -1,
            ));
            $unsortedUrl = Yii::app()->createUrl('/admin/ajax/agregateMembers', array(
                'vid'  => $vacancyid,
                'siid' => 0,
            ));
            $items = array(
                array(
                    'label'    => '<span class="menu-item-parent">Все заявки</span>',
                    'url'      => Yii::app()->createUrl('/admin/ajax/fullMemberList', array(
                        'vid'  => $vacancyid,
                    )),
                ),
                array(
                    'label'    => '<span class="menu-item-parent open">По категориям</span>',
                    'url'      => '#',//array('admin/projectMember/index', array('vid' => $vacancyid)),
                    'items'    => $categories,
                ),
	            array(
                    'label'    => '<span class="menu-item-parent">Не распределены</span>',
                    'url'      => $unsortedUrl,
	            ),
            );
            $this->sideBar = $items;
        }
        $this->render('members');
    }
    
    /**
     * Список всех заявок на роль
     * @return void
     */
    public function actionFullMemberList()
    {
        // верстка списка участников во всю ширину
        $this->layout = '//layouts/ajax/row';
        // Получаем роль для которой будет происходить отбор
        $vacancyid = Yii::app()->request->getParam('vid');
        $vacancy   = EventVacancy::model()->findByPk($vacancyid);
        if ( ! $vacancy )
        {
            throw new CHttpException(404, 'Не найдена роль для отбора заявок');
        }
        $this->render('fullList', array(
            'vacancy' => $vacancy,
        ));
    }
    
    /**
     * Изменить список разделов в которые входит заявка участника
     * @return void
     */
    public function actionChangeMemberCategory()
    {
        $memberId          = Yii::app()->request->getParam('memberId');
        $sectionInstanceId = Yii::app()->request->getParam('sectionInstanceId');
        $newValue          = Yii::app()->request->getParam('newValue');
        
        $projectMember = ProjectMember::model()->findByPk($memberId);
        if ( $newValue == 'true' )
        {
            $projectMember->addToInstance($sectionInstanceId);
        }elseif ( $newValue == 'false' )
        {
            $projectMember->removeFromInstance($sectionInstanceId);
        }
    }
}