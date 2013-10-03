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
     *              default is false
     */
    const LOG_EMAIL_MESSAGES = true;
    
    /**
     * @var bool - use amazon SES to send email
     * @deprecated
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
	
	public $tableUsers = '{{users}}';
	public $tableProfiles = '{{profiles}}';
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
	 * (non-PHPdoc)
	 * @see CModule::init()
	 */
	public function init()
	{
		// import the module-level models and components
		$this->setImport(array(
			'user.models.*',
			'user.components.*',
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
	 * (non-PHPdoc)
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
	 * Send mail method
	 */
	public static function sendMail($email, $subject, $message, $sendNow=false)
	{
	    if ( $sendNow )
	    {// нужно отправить письмо прямо сейчас
	        Yii::app()->getComponent('ecawsapi')->sendMail($email, $subject, $message);
	    }else
	    {// отправить письмо в очередь (через некоторой время)
	        Yii::app()->getComponent('ecawsapi')->pushMail($email, $subject, $message);
	    }
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
                $user = User::model()->findbyPk($id);
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
	 * Return safe user data.
	 * @param user id not required
	 * @return user object or false
	 */
	public function users()
	{
		return User;
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
}
