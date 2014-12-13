<?php

/**
 * 
 */
class RecoveryController extends Controller
{
	public $defaultAction = 'recovery';
	
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
	 * Recovery password
	 */
	public function actionRecovery()
	{
        $form  = new UserRecoveryForm;
	    $email = ( (isset($_GET['email'])) ? $_GET['email'] : '');
	    $activkey = ( (isset($_GET['activkey'])) ? $_GET['activkey'] : '');
	    if ( $email && $activkey )
        {
	        $form2 = new UserChangePassword;
	        $find = User::model()->notsafe()->findByAttributes(array('email'=>$email));
	        if( $find && $find->activkey == $activkey )
	        {
	            if ( isset($_POST['UserChangePassword']) )
	            {
	                $form2->attributes = $_POST['UserChangePassword'];
	                if ( $form2->validate() )
	                {
	                    $find->password = Yii::app()->controller->module->encrypting($form2->password);
	                    $find->activkey = Yii::app()->controller->module->encrypting(microtime().$form2->password);
	                    if ( $find->status == 0 )
	                    {
	                        $find->status = 1;
	                    }
	                    $find->save();
	                    Yii::app()->user->setFlash('recoveryMessage', UserModule::t("New password is saved."));
	                    $this->redirect(Yii::app()->controller->module->recoveryUrl);
	                }
	            }
	            $this->render('changepassword', array('form' => $form2));
	        }else
	        {
	            Yii::app()->user->setFlash('recoveryMessage', UserModule::t("Incorrect recovery link."));
	            $this->redirect(Yii::app()->controller->module->recoveryUrl);
	        }
	    }else
	    {
	        if ( isset($_POST['UserRecoveryForm']) )
	        {
	            $form->attributes = $_POST['UserRecoveryForm'];
	            if ( $form->validate() )
	            {// запрошено восстановление пароля
	                $user = User::model()->notsafe()->findbyPk($form->user_id);
	                $subject = UserModule::t("You have requested the password recovery site {site_name}",
	                    array(
	                        '{site_name}' => Yii::app()->name,
	                    ));
	                if ( Yii::app()->getModule('user')->recoveryType == 'text' )
	                {// письмо с восстановлением пароля - генерим пароль сами, и отсылаем то что получилось
	                    $newPassword = $user->generatePassword();
	                    $user->password = Yii::app()->controller->module->encrypting($newPassword);
	                    $user->activkey = Yii::app()->controller->module->encrypting(microtime().$newPassword);
	                    if ( ! $user->save() )
	                    {// @todo языковые строки
	                        throw new CException('Ошибка при при смене пароля');
	                    }
	                    $message = 'Вы воспользовались функцией восстановления пароля на сайте easycast.ru.<br> Ваш новый пароль:<b>'.$newPassword.'</b>';
	                }else
	                {// письмо с восстановлением пароля - задать свой пароль
	                    $activation_url = 'http://'.$_SERVER['HTTP_HOST'].$this->createUrl(implode(Yii::app()->controller->module->recoveryUrl), array("activkey" => $user->activkey, "email" => $user->email));
	                    $message = UserModule::t("You have requested the password recovery site {site_name}. To receive a new password, go to {activation_url}.",
	                        array(
	                            '{site_name}'      => Yii::app()->name,
	                            '{activation_url}' => $activation_url,
	                        ));
	                }
	                // отсылаем письмо немедленно
	                UserModule::sendMail($user->email, $subject, $message, true);
	                // обновляем страницу и показываем сообщение
	                Yii::app()->user->setFlash('recoveryMessage',
	                   UserModule::t("Please check your email. An instructions was sent to your email address."));
	                $this->refresh();
	            }
	        }
	        $this->render('recovery', array('form' => $form));
	    }
	}
    
	/**
	 * 
	 * @param  User $user
	 * @param  string $plainPassword
	 * @return null
	 */
	public function sendRawPassword($user, $plainPassword)
	{
	    
	}
}