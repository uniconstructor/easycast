<?php

/**
 * Контроллер для LP перед созданием онлайн-кастинга
 * 
 * @TODO добавить оплату при создании роли в онлайн-кастинге
 * @todo переименовать вторую страницу в "требования к участникам"
 * @todo переименовать статистов в "типажи"
 */
class OnlineCastingController extends Controller
{
    /**
     * 
     * @return void
     */
    public function actionIndex()
    {
        $this->render('index');
    }
    
    /**
     * 
     * @return void
     */
    public function actionCreate()
    {
        $onlineCastingForm    = new OnlineCastingForm();
        $onlineCastingRoleForm = new OnlineCastingRoleForm();
        
        $step = Yii::app()->request->getParam('step', 'info');
        
        $this->render('create', array(
            'onlineCastingForm'     => $onlineCastingForm,
            'onlineCastingRoleForm' => $onlineCastingRoleForm,
            'step'                  => $step,
        ));
    }
    
    /**
     * Сохранить информацию об онлайн-кастинге в сессию
     * @return void
     */
    public function actionSaveCasting()
    {
        $onlineCastingForm = new OnlineCastingForm();
        
        //$this->performAjaxValidation($onlineCastingForm);
        
        if ( $formData = Yii::app()->request->getPost('OnlineCastingForm') )
        {// сохранена форма кастинга
            print_r($formData);
        }
    }
    
    /**
     * Сохранить в сессию информацию о роли
     * @return void
     */
    public function actionSaveRole()
    {
        
    }
    
    /**
     * Сохранить критерии поиска для роли
     * @return void
     */
    public function actionSaveRoleCriteria()
    {
        
    }
    
    /**
     * Сохранить кастинг со всеми ролями из сессии в базу и 
     * отправить команде оповещение о новом запросе на кастинг
     * @return void
     */
    public function actionFinishCastingSetup()
    {
        
    }
    
    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if ( Yii::app()->request->getParam('ajax') === 'online-casting-form' )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}