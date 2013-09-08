<?php

/**
 * The followings are the available columns in table 'users':
 * @var integer $id
 * @var string $username
 * @var string $password
 * @var string $email
 * @var string $activkey
 * @var integer $timecreated
 * @var integer $lastvisit
 * @var integer $superuser
 * @var integer $status
 * @var integer $firstaccess
 * @var timestamp $create_at
 * @var timestamp $lastaccess
 */
class User extends CActiveRecord
{
	const STATUS_NOACTIVE=0;
	const STATUS_ACTIVE=1;
	const STATUS_BANNED=-1;
	
	//TODO: Delete for next version (backward compatibility)
	const STATUS_BANED=-1;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return Yii::app()->getModule('user')->tableUsers;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.CConsoleApplication
		return ((get_class(Yii::app())=='CConsoleApplication' || (get_class(Yii::app())!='CConsoleApplication' && Yii::app()->user->isSuperuser))?array(
			array('username', 'length', 'max'=>255, 'min' => 2,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			array('password', 'length', 'max'=>128, 'min' => 4,'message' => UserModule::t("Incorrect password (minimal length 4 symbols).")),
			array('email', 'email'),
			array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
			array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
			array('username', 'match', 'pattern' => '/^[A-Za-z0-9_\.()-]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			array('status', 'in', 'range'=>array(self::STATUS_ACTIVE,self::STATUS_NOACTIVE,self::STATUS_BANNED)),
			array('superuser', 'in', 'range'=>array(0,1)),
            array('create_at', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),
            array('lastaccess', 'default', 'value' => '0000-00-00 00:00:00', 'setOnEmpty' => true, 'on' => 'insert'),
		    array('firstaccess', 'default', 'value' => '0', 'setOnEmpty' => true, 'on' => 'insert'),
			array('email, superuser, status', 'required'),
			array('superuser, status', 'numerical', 'integerOnly'=>true),
			array('policyagreed', 'in', 'range'=>array(0,1)),
			array('id, username, password, email, activkey, create_at, lastaccess, superuser, status', 'safe', 'on'=>'search'),
		):((Yii::app()->user->id==$this->id)?array(
			array('email', 'required'),
			array('username', 'length', 'max'=>255, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			array('email', 'email'),
			array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
			array('username', 'match', 'pattern' => '/^[A-Za-z0-9_\.()-]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
			array('policyagreed', 'in', 'range'=>array(0,1)),
		):array(array('policyagreed', 'in', 'range'=>array(0,1)))));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        $relations = Yii::app()->getModule('user')->relations;
        if ( ! isset($relations['profile']) )
            $relations['profile'] = array(self::HAS_ONE, 'Profile', 'user_id');
        return $relations;
	}
	
	/**
	 * @todo добавить в БД пользователя поле questionaryid и прописывать его при создании
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 */
	protected function beforeSave()
	{
	    if ( ! $this->unsubscribekey )
	    {// автоматически создаем ключ для отписки от рассылок если он еще не создан
	        $this->unsubscribekey = UserModule::encrypting(microtime().$this->password);
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 * @return null
	 */
	protected function afterSave()
	{
	    if ( $this->isNewRecord )
	    {// при создании пользователя автоматически создаем для него пустую анкету
	        $questionary = new Questionary();
	        $questionary->userid = $this->id;
	        $questionary->save();
	    }
	    // назначаем пользователю роль по умолчанию или меняем ее если она изменилась
	    $this->setUpRoles();
	    
	    parent::afterSave();
	}
	
	/**
	 * Установить роль при создании пользователя или изменить ее при редактировании
	 * 
	 * @return null
	 * 
	 * @todo добавить возможность создавать участников, заказчиков и членов команды
	 * @todo выяснить, нужна ли проверка getAssignedRoles() перед назначением роли
	 */
	protected function setUpRoles()
	{
	    $roles = Rights::getAssignedRoles($this->id);
	    
	    if ( $this->superuser )
	    {
	        if ( ! in_array('Admin', $roles) )
	        {// назначаем роль админа, если она еще не назначена
	            Rights::assign('Admin', $this->id);
	        }
	    }else
	    {
	        if ( ! in_array('User', $roles) )
	        {// назначаем роль участника, если она еще не назначена
	            Rights::assign('User', $this->id);
	        }
	        if ( in_array('Admin', $roles) )
	        {// если пользователю была назначена роль админа - уберем ее
	            Rights::revoke('Admin', $this->id);
	        }
	    }
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeDelete()
	 */
	protected function beforeDelete()
	{
	    if ( $this->questionary )
	    {
	        $this->questionary->delete();
	    }
	    
	    return parent::beforeDelete();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => UserModule::t("Id"),
			'username'=>UserModule::t("username"),
			'password'=>UserModule::t("password"),
			'verifyPassword'=>UserModule::t("Retype Password"),
			'email'=>UserModule::t("E-mail"),
			'verifyCode'=>UserModule::t("Verification Code"),
			'activkey' => UserModule::t("activation key"),
			'timecreated' => UserModule::t("Registration date"),
			'create_at' => UserModule::t("Registration date"),
			'firstaccess' => UserModule::t("First access"),
			'lastaccess' => UserModule::t("Last visit"),
			'superuser' => UserModule::t("Superuser"),
			'status' => UserModule::t("Status"),
		    'policyagreed' => UserModule::t("policyagreed_label"),
		    'fullname' => UserModule::t("fullname"),
		);
	}
	
	public function scopes()
    {
        return array(
            'active'=>array(
                'condition'=>'status='.self::STATUS_ACTIVE,
            ),
            'notactive'=>array(
                'condition'=>'status='.self::STATUS_NOACTIVE,
            ),
            'banned'=>array(
                'condition'=>'status='.self::STATUS_BANNED,
            ),
            'superuser'=>array(
                'condition'=>'superuser=1',
            ),
            'notsafe'=>array(
            	'select' => 'id, username, password, email, activkey, create_at, lastaccess, superuser, status',
            ),
        );
    }
	
	public function defaultScope()
    {
        return CMap::mergeArray(Yii::app()->getModule('user')->defaultScope,array(
            'alias'=>'user',
            'select' => 'user.*',
        ));
    }
	
	public static function itemAlias($type,$code=NULL) {
		$_items = array(
			'UserStatus' => array(
			    self::STATUS_ACTIVE => UserModule::t('Active'),
			    self::STATUS_NOACTIVE => UserModule::t('Not active'),
				self::STATUS_BANNED => UserModule::t('Banned'),
			),
			'AdminStatus' => array(
				'0' => UserModule::t('No'),
				'1' => UserModule::t('Yes'),
			),
		);
		if (isset($code))
			return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
		else
			return isset($_items[$type]) ? $_items[$type] : false;
	}
	
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        
        $criteria->compare('id',$this->id);
        $criteria->compare('username',$this->username,true);
        $criteria->compare('password',$this->password);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('activkey',$this->activkey);
        $criteria->compare('create_at',$this->create_at);
        $criteria->compare('lastaccess',$this->lastaccess);
        $criteria->compare('superuser',$this->superuser);
        $criteria->compare('status',$this->status);
        $criteria->compare('policyagreed',$this->policyagreed);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
        	'pagination'=>array(
				'pageSize'=>Yii::app()->getModule('user')->user_page_size,
			),
        ));
    }

    public function gettimecreated() {
        return strtotime($this->create_at);
    }

    public function settimecreated($value) {
        $this->create_at=date('Y-m-d H:i:s',$value);
    }

    public function getLastvisit() {
        return strtotime($this->lastaccess);
    }

    public function setLastvisit($value) {
        $this->lastaccess=date('Y-m-d H:i:s',$value);
    }
    
    /** Получить анкету пользователя
     * @todo переписать после добавления в модель поля questionaryid
     * 
     * @return NULL|Ambigous <CActiveRecord, mixed, NULL, multitype:, multitype:unknown Ambigous <CActiveRecord, NULL> , multitype:unknown >
     */
    public function getQuestionary()
    {
        if ( ! isset($this->id) OR ! $this->id )
        {
            return null;
        }
        
        return Questionary::model()->find(array('userid' => $this->id));
    }
    
    /**
     * Получить полное имя пользователя (или логин, если анкета не заполнена)
     * @return string
     */
    public function getfullname()
    {
        $fullname = $this->questionary->firstname.' '.$this->questionary->lastname;
        
        if ( ! trim($fullname) )
        {// Если пользователь еще не заолнил анкету - выводим только его логин
            $fullname = $this->username;
        }
        
        return CHtml::encode($fullname);
    }
    
    public function getpolicyagreed()
    {
        return $this->policyagreed;
    }
    
    /**
     * generates a random password
     * @todo use a custom password generator
     * 
     * @return string
     */
    public function generatePassword()
    {
        $password = sha1('j3qq4'.microtime());
        return substr($password, -6);
    }
    
    /**
     * Generates unique login by given email
     * @param string $email
     * @return string
     */
    public function getLoginByEmail($email)
    {
        $email = explode('@', $email);
        $login = $email[0];
        
        return $this->getUniqueLogin($login);
    }
    
    /**
     * Generate unique login by given login
     * @param string $login
     * @return string
     */
    public function getUniqueLogin($login)
    {
        if ( ! self::exists('username = :username', array(':username' => $login)) )
        {
            return $login;
        }
        
        $count = 1;
        $newLogin = $login.$count;
        while ( self::exists('username = :username', array(':username' => $newLogin)) )
        {
            $count++;
            $newLogin = $login.$count;
        }
        
        return $newLogin;
    }
}