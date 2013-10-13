<?php

class AdminController extends Controller
{
	public $defaultAction = 'admin';
	public $layout='//layouts/column2';
	
	private $_model;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return CMap::mergeArray(parent::filters(),array(
			'accessControl', // perform access control for CRUD operations
		));
	}
	
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions' => array('admin', 'delete', 'create', 'update', 'view'),
				'users'   => UserModule::getAdmins(),
			),
			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
	}
	
	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new User('search');
        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['User']))
            $model->attributes=$_GET['User'];

        $this->render('index',array(
            'model'=>$model,
        ));
		/*$dataProvider=new CActiveDataProvider('User', array(
			'pagination'=>array(
				'pageSize'=>Yii::app()->controller->module->user_page_size,
			),
		));

		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));//*/
	}

	/**
	 * Displays a particular model.
	 */
	public function actionView()
	{
		$model = $this->loadModel();
		$this->render('view',array(
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model   = new User;
		// @todo not create profile
		$profile = new Profile;
		
		// AJAX validation goes first
		$this->performAjaxValidation(array($model, $profile));
		
		if ( $attributes = Yii::app()->request->getPost('User') )
		{// форма сохранена
			$model->attributes = $attributes;
			$model->activkey   = Yii::app()->controller->module->encrypting(microtime().$model->password);
			
			if ( ! trim($model->password) )
			{// password not set - generating...
			    $model->password = $model->generatePassword();
			}
			// store original password (for activation email)
			$password = $model->password;
			
			if ( ! trim($model->username) )
			{// login not set - generating...
			    $model->username = $model->getLoginByEmail($model->email);
			}
			// узнаем, анкета из нашей базы или предоставлена партнером
			$ownerId = Yii::app()->request->getPost('ownerId', 1);
			
			if ( $model->validate() )
			{// data validated - creatig user
				// hashing password
			    $model->password = Yii::app()->controller->module->encrypting($model->password);
			    if ( $ownerId AND $ownerId != 1 )
			    {// анкета из партнерской базы - указываем партнера
			        $model->setOwnerId($ownerId);
			    }
				if ( ! $model->save() )
				{// error: user not created
					throw new CHttpException(500, 'Ошибка при создании пользователя');
					return;
				}
				// отсылаем письмо с сылкой активации и паролем
				$this->sendActivationEmail($model, $password, $ownerId);
				// перенаправляем админа на страницу редактирования анкеты
				$this->redirect(array('/questionary/questionary/update', 'id' => $model->questionary->id));
			}
		}
		if ( $model->isNewRecord )
		{// все созданные админом участники активируются автоматически
		    $model->status = User::STATUS_ACTIVE;
		}
		// отрисовка формы
		$this->render('create', array(
			'model'   => $model,
			'profile' => $profile,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		$model=$this->loadModel();
		
		$this->performAjaxValidation(array($model));
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			//$profile->attributes=$_POST['Profile'];
			
			if( $model->validate() ) {
				$old_password = User::model()->notsafe()->findByPk($model->id);
				if ($old_password->password!=$model->password) {
					$model->password=Yii::app()->controller->module->encrypting($model->password);
					$model->activkey=Yii::app()->controller->module->encrypting(microtime().$model->password);
				}
				$model->save();
				
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			//'profile'=>$profile,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$model = $this->loadModel();
			if ( $profile = Profile::model()->findByPk($model->id) )
			{
			    $profile->delete();
			}
			$model->delete();
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(array('/user/admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	/**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($validate)
    {
        if( isset($_POST['ajax']) && $_POST['ajax']==='user-form' )
        {
            echo CActiveForm::validate($validate);
            Yii::app()->end();
        }
    }
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if ( $this->_model === null )
		{
			if ( isset($_GET['id']) )
			{
			    $this->_model = User::model()->notsafe()->findbyPk($_GET['id']);
			}
			if( $this->_model === null )
			{
			    throw new CHttpException(404, 'The requested page does not exist.');
			}
		}
		return $this->_model;
	}
	
	/**
	 * Отправить письмо с уведомлением о регистрации
	 * @param User $model
	 * @param string $password
	 * @param int $ownerId - id пользователя (админа, партнера, или заказчика) который предоставил данные этой анкеты
	 * @return null
	 * 
	 * @todo избавиться от дублирования кода
	 */
	public function sendActivationEmail($model, $password=null, $ownerId=1)
	{
	    if ( $ownerId AND $ownerId != 1 )
	    {// анкета из партнерской базы - не высылаем активационное письмо сразу
	        return;
	    }
	    $activation_url = $this->createAbsoluteUrl('/user/activation/activation',
	        array("activkey" => $model->activkey, "email" => $model->email)
	    );
	     
	    if ( Yii::app()->user->isSuperuser )
	    {// анкету заводит админ - сообщаем пользователю чтобы он подождал
	        $theme = 'Вы стали участником проекта EasyCast';
	        // @todo языковые строки
	        $message = 'Добрый день.<br>
                Если вы получили это сообщение, значит наш менеджер зарегистрировал вас в базе актеров на сайте EasyCast.ru, где вы сможете получать приглашения и подавать заявки на участие в съемках.<br>
                Сейчас мы заполняем вашу анкету, используя ту информацию которую вы согласились нам предоставить.<br>
        	    Примерно через 20 минут мы закончим ввод данных и сообщим вам об этом.<br>
        	    После этого вы получите доступ к нашему сервису а также сможете уточнить информацию о себе.<br>';
	    }else
	    {// Пользователь регистрируется сам - стандартное сообщение
	        $theme   = UserModule::t("You registered from {site_name}",array('{site_name}'=>Yii::app()->name));
	        $message = UserModule::t("Please activate you account go to {activation_url}",
	            array('{activation_url}'=>$activation_url)
	        );
	    }
	    $message .= "<br><br>";
	    $message .= "Данные для доступа к сайту:<br>";
	    $message .= "\n Логин: ".$model->username."<br>";
	    $message .= "\n Пароль: ".$password."<br>";
	    $message .= "<br><br>";
	    $message .= 'Если вы считаете что получили это письмо по ошибке или у вас возникли вопросы, то вы можете задать их нам, просто ответив на это письмо или позвонив по телефону +7(926)582-90-66.';
	    $message .= "<br><br>";
	    $message .= "С уважением, команда проекта EasyCast";
	     
	    UserModule::sendMail($model->email, $theme, $message, true);
	}
}