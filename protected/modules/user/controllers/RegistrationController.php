<?php

/**
 * Registaion actions
 */
class RegistrationController extends Controller
{
	public $defaultAction = 'registration';
	
	/**
	 * Declares class-based actions.
	 * @todo delete during refactoring
	 */
	/*public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}*/
	
	/**
	 * Registration user
	 */
	public function actionRegistration() {
            $model   = new RegistrationForm;
            $profile = new Profile;
            $profile->regMode = true;
            
			// ajax validator
			if(isset($_POST['ajax']) && $_POST['ajax']==='registration-form')
			{
				echo RegistrationForm::ajaxValidate(array($model));
				Yii::app()->end();
			}
			
		    if (Yii::app()->user->id)
		    {
		    	$this->redirect(Yii::app()->controller->module->profileUrl);
		    }else
		    {
		    	if ( isset($_POST['RegistrationForm']) )
		    	{
					$model->attributes   = $_POST['RegistrationForm'];
					//CVarDumper::dump($model->attributes, 10, true);
					//CVarDumper::dump($_POST['RegistrationForm'], 10, true);
					$soucePassword  = $model->password;
					$verifyPassword = $model->verifyPassword;
					// если логин или пароль не задан - попробуем автоматически создать их
					if ( ! trim($soucePassword) )
					{// password not set - generate it
					    $soucePassword  = $model->generatePassword();
					    $verifyPassword = $soucePassword;
					}
					if ( ! trim($model->username) )
					{// login not set - generate it
					    $model->username = $model->getLoginByEmail($model->email);
					}
					
					if ( $model->validate() )
					{// begin registration
						$this->saveUser($model, $soucePassword, $verifyPassword);
						$this->redirect(Yii::app()->createAbsoluteUrl('/questionary/questionary/view'));
						Yii::app()->end();
					}
				}
			    $this->render('/user/registration', array('model'=>$model,'profile'=>$profile));
		    }
	}
	
	/**
	 * Save user afrer registration
	 * @param User $model
	 * @param string $soucePassword
	 * @param string $verifyPassword
	 * @param bool $redirect
	 * @return null
	 */
	protected function saveUser($model, $soucePassword, $verifyPassword, $redirect=true)
	{
	    $model->activkey = UserModule::encrypting(microtime().$model->password);
	    $model->password = UserModule::encrypting($soucePassword);
	    $model->verifyPassword = UserModule::encrypting($verifyPassword);
	    $model->superuser = 0;
	    $model->status = ((Yii::app()->controller->module->activeAfterRegister)?User::STATUS_ACTIVE:User::STATUS_NOACTIVE);
	    
	    if ( $model->save() )
	    {
	        if ( Yii::app()->controller->module->sendActivationMail )
	        {// отправляем письмо с данными о регистрации
	            $this->sendActivationEmail($model, $soucePassword);
	        }
	        	
	        if ((Yii::app()->controller->module->loginNotActiv||(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false))&&Yii::app()->controller->module->autoLogin) {
	            $identity=new UserIdentity($model->username, $soucePassword);
	            $identity->authenticate();
	            Yii::app()->user->login($identity, 3600*24*30);
	            if ( $redirect )
	            {
	                $this->redirect(Yii::app()->controller->module->returnUrl);
	            }
	        } else {
	            if (!Yii::app()->controller->module->activeAfterRegister&&!Yii::app()->controller->module->sendActivationMail) {
	                Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Contact Admin to activate your account."));
	            } elseif(Yii::app()->controller->module->activeAfterRegister&&Yii::app()->controller->module->sendActivationMail==false) {
	                Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please {{login}}.",array('{{login}}'=>CHtml::link(UserModule::t('Login'),Yii::app()->controller->module->loginUrl))));
	            } elseif(Yii::app()->controller->module->loginNotActiv) {
	                Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email or login."));
	            } else {
	                Yii::app()->user->setFlash('registration',UserModule::t("Thank you for your registration. Please check your email."));
	            }
	        }
	    }
	}
	
	/**
	 * Register new user via AJAX
	 * @return null
	 */
	public function actionAjaxRegistration()
	{
	    $model = new RegistrationForm;
	    
	    $validationResult = RegistrationForm::ajaxValidate(array($model));
	    echo $validationResult;
	    if ( $validationResult != '[]' )
	    {
	        Yii::app()->end();
	    }
	    
	    $model->attributes   = $_POST['RegistrationForm'];
	    $soucePassword  = $model->password;
	    $verifyPassword = $model->verifyPassword;
	    // если логин или пароль не задан - попробуем автоматически создать их
	    if ( ! trim($soucePassword) )
	    {// password not set - generate it
	        $soucePassword  = $model->generatePassword();
	        $verifyPassword = $soucePassword;
	    }
	    if ( ! trim($model->username) )
	    {// login not set - generate it
	        $model->username = $model->getLoginByEmail($model->email);
	    }
	    $this->saveUser($model, $soucePassword, $verifyPassword, false);
	    Yii::app()->end();
	}
	
	/**
	 * Отправить письмо с уведомлением о регистрации
	 * @param User $model
	 * @param string $password
	 * @return null
	 * 
	 * @deprecated
	 * @todo использовать обращение к UserModule::sendActivationEmail()
	 */
	public function sendActivationEmail($model, $password=null)
	{
	    $activation_url = $this->createAbsoluteUrl(
	    '/user/activation/activation',
	            array("activkey" => $model->activkey, "email" => $model->email)
	        );
	    
	    if ( Yii::app()->user->isSuperuser )
	    {// анкету заводит админ - сообщаем пользователю чтобы он подождал
	        $theme = 'Вы стали участником проекта EasyCast';
    	    // @todo языковые строки
    	    $message = 'Добрый день.<br>
                Если вы получили это сообщение, значит наш менеджер зарегистрировал вас в базе актеров на сайте EasyCast.ru,
    	        где вы сможете получать приглашения и подавать заявки на участие в съемках.<br>
                Сейчас мы заполняем вашу анкету, используя ту информацию которую вы согласились нам предоставить.<br>
        	    Примерно через 20 минут мы закончим ввод данных и сообщим вам об этом.<br>
        	    После этого вы получите доступ к нашему сервису а также сможете уточнить информацию о себе.<br>';
	    }else
	    {// Пользователь регистрируется сам - стандартное сообщение
	        $theme   = UserModule::t("You registered from {site_name}", array('{site_name}' => Yii::app()->name));
	        $message = UserModule::t("Please activate you account go to {activation_url}",
	                array('{activation_url}' => $activation_url)
	            );
	    }
	    $message .= "<br><br>";
	    $message .= "Данные для доступа к сайту:<br>";
	    $message .= "\n Логин: ".$model->username."<br>";
	    $message .= "\n Пароль: ".$password."<br>";
	    $message .= "<br><br>";
	    $message .= 'Если вы считаете что получили это письмо по ошибке или у вас возникли другие вопросы, то вы можете задать их нам, просто ответив на это письмо или позвонив по телефону +7(906)098-32-07 .';
	    $message .= "<br><br>";
	    $message .= "С уважением, команда проекта EasyCast";
	    // отсылаем письмо немедленно
	    UserModule::sendMail($model->email, $theme, $message, true);
	}
}