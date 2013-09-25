<?php

/**
 * Форма регистрации во всплывающем окне
 */
class ECRegister extends CWidget
{
    protected $redirectUrl;
    
    protected $actionUrl;
    
    /**
     * (non-PHPdoc)
     * @see CWidget::init()
     */
    public function init()
    {
        $this->redirectUrl = Yii::app()->createAbsoluteUrl('//questionary/questionary/view');
        $this->actionUrl   = Yii::app()->createAbsoluteUrl('//user/registration/ajaxRegistration');
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        $model = new RegistrationForm;
        $this->render('_form', array('model' => $model));
    }
}