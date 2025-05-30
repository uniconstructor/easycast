<?php
/**
 * Yii-User module
 * 
 * @author Mikhail Mangushev <mishamx@gmail.com> 
 * @link http://yii-user.2mx.org/
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @version $Id: UserModule.php 132 2011-10-30 10:45:01Z mishamx $
 */
class UserModule extends CWebModule
{
    /**
     * @var bool - log output email messages? (FOR DEBUG ONLY!)
     *             default is false
     * @deprecated вся работа с amazon AWS переехала в отдельный модуль - удалить эту константу при рефакторинге
     */
    const LOG_EMAIL_MESSAGES = false;
    
    /**
     * @var bool - use amazon SES to send email
     * @deprecated вся работа с amazon AWS переехала в отдельный модуль - удалить эту константу при рефакторинге
     */
    const USE_AMAZON_API = false;
    
    /**
     * @var string - тип восстановления пароля:
     *               form - на почту приходит ссылка с предложением задать новый пароль
     *               text - на почту сразу приходит автоматически созданный пароль
     */
    public $recoveryType = 'text';
    
	/**
	 * @var int
	 * @desc items on page
	 */
	public $user_page_size = 50;
	
	/**
	 * @var int
	 * @desc items on page
	 */
	public $fields_page_size = 10;
	
	/**
	 * @var string
	 * @desc hash method (md5,sha1 or algo hash function http://www.php.net/manual/en/function.hash.php)
	 */
	public $hash='md5';
	
	/**
	 * @var boolean
	 * @desc use email for activation user account
	 */
	public $sendActivationMail=true;
	
	/**
	 * @var boolean
	 * @desc allow auth for is not active user
	 */
	public $loginNotActiv=false;
	
	/**
	 * @var boolean
	 * @desc activate user on registration (only $sendActivationMail = false)
	 */
	public $activeAfterRegister=false;
	
	/**
	 * @var boolean
	 * @desc login after registration (need loginNotActiv or activeAfterRegister = true)
	 */
	public $autoLogin=true;
	
	public $registrationUrl = array("/user/registration");
	public $recoveryUrl = array("/user/recovery/recovery");
	public $loginUrl = array("/user/login");
	public $logoutUrl = array("/user/logout");
	public $profileUrl = array("/user/profile");
	public $returnUrl = array("index");//array("/user/profile");
	public $returnLogoutUrl = array("index");//array("/user/login");
	
	
	/**
	 * @var int
	 * @desc Remember Me Time (seconds), defalt = 2592000 (30 days)
	 */
	public $rememberMeTime = 2592000; // 30 days
	
	public $fieldsMessage = '';
	
	/**
	 * @var array
	 * @desc User model relation from other models
	 * @see http://www.yiiframework.com/doc/guide/database.arr
	 */
	public $relations = array(
        'questionary' => array(CActiveRecord::HAS_ONE, 'Questionary', 'userid'),
    );
	
	/**
	 * @var array
	 * @desc Profile model relation from other models
	 */
	public $profileRelations = array();
	
	/**
	 * @var boolean
	 */
	public $captcha = array('registration' => true);
	
	/**
	 * @var boolean
	 */
	//public $cacheEnable = false;
	
	public $tableUsers         = '{{users}}';
	public $tableProfiles      = '{{profiles}}';
	public $tableProfileFields = '{{profiles_fields}}';

    public $defaultScope = array(
            'with' => array('profile'),
    );
	
	static private $_user;
	static private $_users=array();
	static private $_userByName=array();
	static private $_admin;
	static private $_admins;
	
	/**
	 * @var array
	 * @desc Behaviors for models
	 */
	public $componentBehaviors=array();
	
	/**
	 * @see CModule::init()
	 */
	public function init()
	{
		$this->setImport(array(
			'user.models.*',
			'user.components.*',
		    // модуль анкеты связан с пользователями, поэтому загружаем его при инициализации
		    'questionary.extensions.behaviors.*',
		    'questionary.models.*',
		    'questionary.models.complexValues.*',
		));
	}
	
	/**
	 * @param string $componentName
	 * @return array
	 */
	public function getBehaviorsFor($componentName)
	{
        if ( isset($this->componentBehaviors[$componentName]) )
        {
            return $this->componentBehaviors[$componentName];
        }else
        {
            return array();
        }
	}

	/**
	 * @see CWebModule::beforeControllerAction()
	 */
	public function beforeControllerAction($controller, $action)
	{
		if ( parent::beforeControllerAction($controller, $action) )
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}else
		{
		    return false;
		}
	}
	
	/**
	 * Залогинить пользователя без ввода логина и пароля (например по одноразовому ключу)
	 * @param User $user
	 * @return bool
	 */
	public function forceLogin($user, $reLogin=false)
	{
	    if ( ! Yii::app()->user->isGuest AND ! $reLogin )
	    {
	        return true;
	    }
	    if ( Yii::app()->user->checkAccess('Admin') AND $reLogin )
	    {// если заходим под другим пользователем - то сначала разлогиниваемся 
	        Yii::app()->user->logout();
	    }
	    if ( ! ( $user instanceof User) )
	    {// @todo подробнее обработать ошибку
	        return false;
	    }
	    
	    $identity = new UserIdentity($user->username, null);
	    // хак с Identity для того чтобы залогинить пользователя, не зная его пароля
	    $identity->setState('inviteLogin', true);
	    $identity->authenticate();
	    $identity->setState('inviteLogin', false);
	    $identity->clearState('inviteLogin');
	    Yii::app()->user->login($identity, 3600 * 24 * 60);
	
	    return true;
	}
	
	/**
	 * @param $str
	 * @param $params
	 * @param $dic
	 * @return string
	 */
	public static function t($str='', $params=array(), $dic='user')
	{
		if (Yii::t("UserModule", $str)==$str)
		{
		    return Yii::t("UserModule.".$dic, $str, $params);
		}else
		{
		    return Yii::t("UserModule", $str, $params);
		}
	}
	
	/**
	 * @return hash string.
	 */
	public static function encrypting($string="")
	{
		$hash = Yii::app()->getModule('user')->hash;
		if ($hash=="md5")
			return md5($string);
		if ($hash=="sha1")
			return sha1($string);
		else
			return hash($hash,$string);
	}
	
	/**
	 * @param $place
	 * @return boolean 
	 */
	public static function doCaptcha($place = '')
	{
		if(!extension_loaded('gd'))
			return false;
		if (in_array($place, Yii::app()->getModule('user')->captcha))
			return Yii::app()->getModule('user')->captcha[$place];
		return false;
	}
	
	/**
	 * Return admin status.
	 * @return boolean
	 * 
	 * @deprecated эта функция осталась от оригинального модуля. В нашей системе используется RBAC
	 */
	public static function isAdmin()
	{
		if(Yii::app()->user->isGuest)
			return false;
		else {
			if (!isset(self::$_admin)) {
				if( self::user()->superuser OR Yii::app()->user->isSuperuser )
					self::$_admin = true;
				else
					self::$_admin = false;	
			}
			return self::$_admin;
		}
	}

	/**
	 * Return admins.
	 * @return array syperusers names
	 */	
	public static function getAdmins()
	{
		if ( ! self::$_admins )
		{
			$admins = User::model()->active()->superuser()->findAll();
			$return_name = array();
			foreach ($admins as $admin)
				array_push($return_name,$admin->username);
			self::$_admins = ($return_name)?$return_name:array('');
		}
		return self::$_admins;
	}
	
	/**
	 * Отправить письмо пользователю
	 * 
	 * @param string $email - получатель
	 * @param string $subject - тема письма
	 * @param string $message - тест письма (html)
	 * @param bool $sendNow - отправить письмо сейчас или поставить в очередь?
	 * @param string $from - с какого адреса отправлять письма?
	 * @return void
	 * 
	 * @deprecated почта теперь отправляется только по событиям: переписать все обращения 
	 *             к этой функции через notify, после чего удалить
	 */
	public static function sendMail($email, $subject, $message, $sendNow=false, $from=null)
	{
	    if ( $sendNow )
	    {// нужно отправить письмо прямо сейчас
	        Yii::app()->getComponent('ecawsapi')->sendMail($email, $subject, $message, $from);
	    }else
	    {// отправить письмо в очередь (через некоторой время)
	        Yii::app()->getComponent('ecawsapi')->pushMail($email, $subject, $message, $from);
	    }
	}
	
	/**
	 * Оповестить одного или нескольких участников по выбранным каналам
	 * @param CModelEvent $event - событие с данными оповещения
	 * @return bool
	 * 
	 * @todo добавить поддержку SMS
	 * @todo вынести в отдельный модуль notifications
	 * @todo прописать try-catch, обработать ошибки
	 */
	public function notify($event)
	{
	    /* @var $mailModule MailComposerModule  */
	    $mailModule = Yii::app()->getModule('mailComposer');
	    // @todo пока что единственный канал отправки - почта
	    $defaults = array(
	        'channel' => 'email',
	        'action'  => '',
	        'params'  => array(),
	        'email'   => '',
	        'subject' => '',
	        'message' => '',
	        'sendNow' => false,
	        'from'    => '',
	    );
	    // получаем настройки для составления и отправки письма
	    $options = CMap::mergeArray($defaults, $event->params);
	    if ( $options['action'] )
	    {
	        if ( ! $options['subject'] )
	        {
	            $options['subject'] = $mailModule::getSubject($options['action'], $options['params']);
	        }
	        if ( ! $options['message'] )
	        {
	            $options['message'] = $mailModule::getMessage($options['action'], $options['params']);
	        }
	    }
	    if ( $options['sendNow'] )
	    {// нужно отправить письмо прямо сейчас
            Yii::app()->getComponent('ecawsapi')->
                sendMail($options['email'], $options['subject'], $options['message'], $options['from']);
	    }else
	    {// отправить письмо в очередь (через некоторой время)
            Yii::app()->getComponent('ecawsapi')->
                pushMail($options['email'], $options['subject'], $options['message'], $options['from']);
	    }
        
	    // @todo return $event->isValid;
	    return true;
	}
	
	/**
	 * Return safe user data.
	 * @param user id not required
	 * @return user object or false
	 */
	public static function user($id=0, $clearCache=false)
	{
        if ( ! $id && ! Yii::app()->user->isGuest )
        {
            $id = Yii::app()->user->id;
        }
		if ( $id ) 
		{
            if ( ! isset(self::$_users[$id]) || $clearCache)
            {
                if ( ! $user = User::model()->findbyPk($id) )
                {// @todo более подробно разобрать ошибку запроса несуществующего пользователя
                    return false;
                }
                $user->superuser = (int)Yii::app()->user->isSuperuser;
                self::$_users[$id] = $user;
                return self::$_users[$id];
            }else
            {
                return self::$_users[$id];
            }
        }else 
        {
            Yii::app()->user->logout();
            return false;
        }
	}
	
	/**
	 * Return safe user data.
	 * @param user name
	 * @return user object or false
	 */
	public static function getUserByName($username)
	{
		if (!isset(self::$_userByName[$username])) {
			$_userByName[$username] = User::model()->findByAttributes(array('username'=>$username));
		}
		return $_userByName[$username];
	}
	
	/**
	 * Получить список админов для выпадающего меню
	 * @param bool|array $emptyOption - отображать ли пустое значение?
	 *                                  Если передан массив - он подставится вместо первого элемента
	 * @return array
	 */
	public static function getAdminList($emptyOption=false)
	{
	    $result = array();
	    if ( $emptyOption === true )
	    {
	        $result = array(0 => 'Нет');
	    }elseif ( is_array($emptyOption) )
	    {
	        $result = $emptyOption;
	    }
	     
	    $criteria = new CDbCriteria();
	    $criteria->compare('superuser', '1');
	     
	    $users = User::model()->findAll($criteria);
	    foreach ( $users as $user )
	    {
	        $result[$user->id] = $user->fullname.' ['.$user->username.']';
	    }
	    return $result;
	}
	
	/**
	 * Return safe user data.
	 * @param user id not required
	 * @return user object or false
	 */
	public function users()
	{
		return User;
	}
	
	/**
	 * Получить текущий режим просмотра сайта: для участников или для заказчиков
	 * @param bool $forceDefault - устанавливать ли режим просмотра автоматически, если он не задан?
	 *                             Используется для того чтобы определить, впервые ли пользователь на сайте
	 *                             Если впервые - то при заходе на главную страницу он должен будет выбрать режим просмотра
	 * @return string|null
	 */
	public function getViewMode($forceDefault=true)
	{
	    if ( Yii::app()->user->checkAccess('Admin') )
	    {// админов сразу после входа нужно перенаправлять на страницу заказчика
	        $defaultState = 'customer';
	    }elseif ( $forceDefault )
	    {
	        $defaultState = 'user';
	    }else
	    {
	        $defaultState = null;
	    }
	    if ( ! Yii::app()->user->hasState('userMode') AND $forceDefault )
	    {// инициализируем режим просмотра, если пользователь зашел первый раз, и еще не определился
	        $this->setViewMode($defaultState);
	    }
	    
	    return Yii::app()->user->getState('userMode', $defaultState);
	}
	
	/**
	 * Изменить текущий режим просмотра сайта: для участников или для заказчиков
	 * @param string $mode - новый режим просмотра сайта
	 *                     user
	 *                     customer
	 * @return string
	 * 
	 * @todo проверка на недопустимые значения
	 */
	public function setViewMode($mode='user')
	{
	    return Yii::app()->user->setState('userMode', $mode);
	}
	
	/**
	 * Очистить текущий режим просмотра - используется для того чтобы вернуться к странице выбора на главной
	 * @return void
	 */
	public function clearViewMode()
	{
	    //Yii::app()->user->clearStates(array('userMode'));
	    $this->setViewMode('');
	}
	
	/**
	 * Отправить письмо с уведомлением о регистрации
	 * @param User $model
	 * @param string $password
	 * @param int $ownerId - id пользователя (админа, партнера, или заказчика)
	 *                       который предоставил данные этой анкеты
	 * @param string $type - тип текста в письме с активацией 
	 * @return null
	 * 
	 * @todo запихнуть текст и верстку письма в модуль писем
	 */
	public function sendActivationEmail($model, $password=null, $ownerId=1, $type='default')
	{
	    if ( $ownerId == 823 )
	    {// анкета из партнерской базы - не высылаем активационное письмо сразу
	        return;
	    }
	    $theme   = 'Вы стали участником проекта EasyCast';
	    $message = '';
	    
	    if ( Yii::app()->user->checkAccess('Admin') )
	    {// анкету заводит админ - сообщаем пользователю чтобы он подождал
	        // @todo языковые строки
	        $message .= 'Добрый день.<br>
                Если вы получили это сообщение, значит наш менеджер зарегистрировал вас в базе актеров на сайте EasyCast.ru, где вы сможете получать приглашения и подавать заявки на участие в съемках.<br>
                Сейчас мы заполняем вашу анкету, используя ту информацию которую вы согласились нам предоставить.<br>
        	    Примерно через 20 минут мы закончим ввод данных и сообщим вам об этом.<br>
        	    После этого вы получите доступ к нашему сервису а также сможете уточнить информацию о себе.<br>';
	    }else
	    {// Пользователь регистрируется сам - стандартное сообщение
	        $message .= "Регистрация завершена.";
	        //$activation_url = Yii::app()->createAbsoluteUrl('/user/activation/activation',
	        //    array("activkey" => $model->activkey, "email" => $model->email)
	        //);
	        //$message = UserModule::t("Please activate you account go to {activation_url}",
	        //    array('{activation_url}' => $activation_url)
	        //);
	    }
	    
	    $message .= "<br><br>";
	    $message .= "Данные для доступа к сайту:<br>";
	    $message .= "\n email: ".$model->email."<br>";
	    $message .= "\n Пароль: ".$password."<br>";
	    $message .= "<br><br>";
	    $message .= 'Если вы считаете что получили это письмо по ошибке или у вас возникли вопросы,
	        то вы можете задать их нам, просто ответив на это письмо или позвонив по телефону '.Yii::app()->params['userPhone'].'.';
	    $message .= "<br><br>";
	    $message .= "С уважением, команда проекта EasyCast.";
	
	    UserModule::sendMail($model->email, $theme, $message, true);
	}
}