<?php

/**
 * Контроллер для работы со страницами новой админки
 * Вся новая админка работает через AJAX, поэтому ознакомьтесь с документацией 
 * админской темы прежде чем создавать новые страницы
 */
class AjaxController extends Admin2BaseController
{
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     *
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('index'),
                'roles'   => array('Admin'),
            ),
            array('allow',
                'actions' => array('selection'),
                'roles'   => array('Admin', 'Customer'),
            ),
            array('allow',
                'actions' => array('upload'),
                'roles'   => array('Admin'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * @see CController::actions()
     */
    public function actions()
    {
        return array(
            'upload' => array(
                'class' => 'xupload.actions.S3XUploadAction',
            ),
        );
    }
    
    /**
     * Отображает dashboard - главную страницу админки
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