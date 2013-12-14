<?php

/**
 * Модель для работы с обычными или срочными  заказами
 * Таблица "{{fast_orders}}".
 *
 * The followings are the available columns in table '{{fast_orders}}':
 * @property integer $id
 * @property string $timecreated
 * @property string $timemodified
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $status
 * @property string $comment
 * @property string $ourcomment
 * @property string $solverid
 * @property string $customerid
 * @property string $version
 */
class FastOrder extends CActiveRecord
{
    /**
     * @var string - тип заказа - обычный заказ
     */
    const TYPE_NORMAL = 'normal';
    /**
     * @var string - тип заказа - срочный заказ
     */
    const TYPE_FAST = 'fast';
    /**
     * @var string - тип заказа: заявка на онлайн-кастинг
     */
    const TYPE_CASTING = 'casting';
    
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('application.modules.user.UserModule');
        Yii::import('application.modules.user.models.*');
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FastOrder the static model class
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
		return '{{fast_orders}}';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 */
	protected function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {
	        $this->version = self::getOrderVersion();
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * Отправить оповещение заказчику и команде сразу после поступления заказа
	 * (non-PHPdoc)
	 * @see CActiveRecord::afterSave()
	 */
	protected function afterSave()
	{
	    if ( $this->isNewRecord )
	    {// Если заказ только что добавлен - оповестим всех об этом
	        $this->sendNewOrderNotifications();
	    }
	    parent::afterSave();
	}
	
	/**
	 * Сохранить данные заказа
	 * @param array $data
	 * @return null
	 */
	public function saveOrderData($data)
	{
	    $this->orderdata = serialize($data);
	    $this->save();
	}
	
	/**
	 * Получить данные заказа в структурированном виде
	 * @return null|array - зависит от версии заказа
	 */
	public function loadOrderData()
	{
	    if ( ! $this->orderdata OR ! $this->version )
	    {
	        return null;
	    }
	    return unserialize($this->orderdata);
	}
    
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('timecreated, timemodified, solverid, customerid, version', 'length', 'max'=>11),
		    
			array('name', 'length', 'max'=>128),
		    array('name', 'filter', 'filter'=>'trim'),
			array('name', 'required'),
		    
			array('phone', 'length', 'max'=>20),
		    array('phone', 'filter', 'filter'=>'trim'),
			array('phone', 'required'),
		    
			array('email, comment, ourcomment', 'length', 'max'=>255),
		    array('email, comment, ourcomment', 'filter', 'filter'=>'trim'),
		    
			array('status', 'length', 'max'=>20),
			array('type', 'length', 'max'=>20),
			array('orderdata', 'safe'),
			array('version', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, timecreated, timemodified, name, phone, email, status, comment, ourcomment, solverid, customerid', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'solver'   => array(self::BELONGS_TO, 'User', 'solverid'),
		    'customer' => array(self::BELONGS_TO, 'User', 'customerid'),
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	    // автоматическое заполнение дат создания и изменения
	    'CTimestampBehavior' => array(
	        'class' => 'zii.behaviors.CTimestampBehavior',
    	        'createAttribute' => 'timecreated',
    	        'updateAttribute' => 'timemodified',
	        )
	    );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'timecreated' => Yii::t('coreMessages', 'db_fastorder_timecreated'),
			'name' => Yii::t('coreMessages', 'db_fastorder_name'),
			'phone' => Yii::t('coreMessages', 'phone'),
			'email' => Yii::t('coreMessages', 'email'),
			'status' => Yii::t('coreMessages', 'status'),
			'comment' => Yii::t('coreMessages', 'db_fastorder_comment'),
			'ourcomment' => Yii::t('coreMessages', 'db_fastorder_ourcomment'),
			'solverid' => Yii::t('coreMessages', 'db_fastorder_solverid'),
			'customerid' => Yii::t('coreMessages', 'db_fastorder_customerid'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('ourcomment',$this->ourcomment,true);
		$criteria->compare('solverid',$this->solverid,true);
		$criteria->compare('customerid',$this->customerid,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Получить возможные варианты статуса заказа
	 * @param string $oldStatus - старый статус объекта
	 * 
	 * @return array
	 * @todo языковые строки
	 */
	public function getStatusVariants()
	{
	    return array(
	            'active'   => 'Ждет звонка',
	            'pending'  => 'В обработке',
	            'closed'   => 'Обработан',
	            'project'  => 'Создан проект',
	            'rejected' => 'Отклонен',
	            'finished' => 'Завершен',
	        );
	}
	
	/**
	 * Получить список статусов, в которые может перейти объект
	 * @return array
	 */
	public function getAllowedStatuses()
	{
	    switch ( $this->status )
	    {
	        case 'active':
	            return array('pending');
            break;
	        case 'pending':
	            return array('closed', 'rejected');
            break;
	        case 'closed':
	            return array('project', 'rejected');
            break;
	        case 'project':
	            return array('rejected', 'finished');
            break;
	        case 'rejected':
	            return array();
            break;
	        case 'finished':
	            return array();
            break;
	        default: return array('active');
	    }
	}
	
	/**
	 * Получить статус объекта для отображения пользователю
	 * @param string $status
	 * @return string
	 */
	public function getStatustext($status=null)
	{
	    if ( ! $status )
	    {
	        $status = $this->status;
	    }
	    return Yii::t('coreMessages', 'order_status_'.$status);
	}
	
	/**
	 * Изменить статус объекта
	 * @param string $newStatus
	 * @return boolean
	 */
	public function setStatus($newStatus)
	{
	    if ( ! in_array($newStatus, $this->getAllowedStatuses()) )
	    {// нельзя перейти из старого статуса в указанный
	        return false;
	    }
	    
	    $this->status   = $newStatus;
	    $this->solverid = Yii::app()->user->id;
	    $this->save();
	    
	    switch ( $newStatus )
	    {
	        // Заказ помещен в обработку - сообщаем всем
	        case 'pending': $this->sendPendingOrderNotifications(); break;
	        // Заказ обработан - сообщаем всем
	        case 'closed':  $this->sendClosedOrderNotifications(); break;
	    }
	    
	    return true;
	}
	
	/**
	 * Добавить анкету участника к заказу
	 * Эта функция работает с формирующимся заказом, который хранится в сессии
	 * 
	 * @param int $id - id анкеты участника
	 * @return bool
	 */
	public static function addToOrder($id)
	{
	    self::initSessionOrderData();
	    // Получаем старые данные заказа (если есть)
	    $order = self::getPendingOrder();
	    // Добавлием анкету к заказу
	    $order['users'][$id] = $id;
	    // сохраняем данные заказа обратно в сессию
	    Yii::app()->session->add('easyCastOrder', $order);
	    
	    return true;
	}
	
	/**
	 * Удалить анкету приглашенного участника из заказа
	 * Эта функция работает с формирующимся заказом, который хранится в сессии
	 * 
	 * @param int $id - id анкеты участника
	 * @return bool
	 */
	public static function removeFromOrder($id)
	{
	    self::initSessionOrderData();
	    // Получаем старые данные заказа (если есть)
	    $order = self::getPendingOrder();
	    $users = $order['users'];
	    if ( isset($users[$id]) )
	    {// удаляем анкету из заказа и обновляем массив с добавленными пользователями
	        unset($users[$id]);
	        unset($order['users']);
	        $order['users'] = $users;
	        // сохраняем данные заказа обратно в сессию
	        Yii::app()->session->add('easyCastOrder', $order);
	    }
	    
	    return true;
	}
	
	/**
	 * Проверить есть ли уже анкета участника в формирующемся заказе
	 * @param int $id - id анкеты участника
	 * 
	 * @return boolean
	 */
	public static function alreadyInOrder($id)
	{
	    if ( ! $order = self::getPendingOrder() )
	    {
	        return false;
	    }
	    return array_key_exists($id, $order['users']);
	}
	
	/**
	 * Получить набираемый в данный момент заказ
	 * 
	 * @return boolean|array
	 */
	public static function getPendingOrder()
	{
	    if ( ! Yii::app()->session->contains('easyCastOrder') )
	    {
	        return false;
	    }
	    return Yii::app()->session->itemAt('easyCastOrder');
	}
	
	/**
	 * Получить массив id пользователей, которые набраны в текущий формирующийся заказ
	 * (который хранится в сессии)
	 * 
	 * @return array
	 */
	public static function getPendingOrderUsers()
	{
	    if ( ! $order = self::getPendingOrder() )
	    {
	        return false;
	    }
	    return $order['users'];
	}
	
	/**
	 * Подсчитать количество актеров в заказе
	 * @return number
	 */
	public static function countPendingOrderUsers()
	{
	    if ( ! $users = self::getPendingOrderUsers() )
	    {
	        return 0;
	    }
	    return count($users);
	}
	
	/**
	 * Проверить, пуста ли корзина заказчика
	 * (не приглашен на съемки ни один участник)
	 * @return boolean
	 */
	public static function orderIsEmpty()
	{
	    if ( ! $order = self::getPendingOrder() )
	    {
	        return true;
	    }
	    return empty($order['users']);
	}
	
	/**
	 * Подготавливает переменную заказа в сессии к добавлению и удалению участников
	 * 
	 * @return null
	 */
	protected static function initSessionOrderData()
	{
	    if ( ! Yii::app()->session->contains('easyCastOrder') )
	    {
	        $order = array();
	        $order['users'] = array();
	        $order['timecreated'] = time();
	        Yii::app()->session->add('easyCastOrder', $order);
	    }
	    
	    // Если пользователь собрался делать заказ - позаботимся о том, чтобы он не пропал,
	    // продлив время сессии на 2 суток
	    Yii::app()->session->setTimeout(3600*24*2); 
	}
	
	/**
	 * Удалить данные заказа из сессии
	 */
	public static function clearPendingOrder()
	{
	    if ( ! self::orderIsEmpty() )
	    {
	        Yii::app()->session->remove('easyCastOrder');
	    }
	}
	
	/**
	 * Отправить все нужные оповещения при создании нового заказа
	 * 
	 * @todo добавить в письмо данные заказа
	 */
	protected function sendNewOrderNotifications()
	{
	    $viewUrl = $this->createOrderViewUrl();
	    $subjectTeam = '[EasyCast] На сайте новый заказ №'.$this->id;
	    $messageTeam = 'На сайт только что поступил новый заказ  №'.$this->id.'.<br><br>';
	    $messageTeam .= '<ul>';
	    $messageTeam .= '<li>От кого: '.$this->name.'</li>';
	    $messageTeam .= '<li>Телефон: '.$this->phone.'</li>';
	    if ( $this->email )
	    {
	        $messageTeam .= '<li>email: '.$this->email.'</li>';
	    }
	    if ( $this->comment )
	    {
	        $messageTeam .= '<li>Комментарий к заказу: '.$this->comment.'</li>';
	    }
	    $messageTeam .= '</ul>';
	    $messageTeam .= '<br><br>';
	    $messageTeam .= 'Просмотреть заказ можно по ссылке: '.CHtml::link($viewUrl, $viewUrl);
	     
	     
	    // Отправляем письмо нашей команде, чтобы она знала о заказе
	    // @todo отправлять SMS команде если заказ срочный
	    UserModule::sendMail('order@easycast.ru', $subjectTeam, $messageTeam, true);
	     
	    // Отправляем письмо заказчику (если он оставил email), чтобы он знал что его заказ принят
	    // @todo отправлять SMS заказчику при получении заказа (если не указан email)
	    // @todo отсылать письмо повторно на тот же адрес не чаще чем раз в 10 минут
	    if ( $this->email )
	    {
	        $subjectCustomer = '[EasyCast] Ваш заказ №'.$this->id.' зарегистрирован';
	        $messageCustomer = 'Добрый день.<br><br>';
	        $messageCustomer .= 'Мы получили ваш заказ на сайте <a href="http://easycast.ru">EasyCast.ru</a> и очень скоро свяжемся с вами, чтобы подтвердить его.<br>';
	        $messageCustomer .= 'Пожалуйста, проверьте правильность введенных вами данных:<br>';
	        $messageCustomer .= '<ul>';
	        $messageCustomer .= '<li>Ваше имя: '.$this->name.'</li>';
	        $messageCustomer .= '<li>Телефон: '.$this->phone.'</li>';
	        $messageCustomer .= '<li>email: '.$this->email.'</li>';
	        if ( $this->comment )
	        {
	            $messageCustomer .= '<li>Комментарий к заказу: '.$this->comment.'</li>';
	        }
	        $messageCustomer .= '</ul>';
	        $messageCustomer .= '<br>';
	        $messageCustomer .= 'С уважением, команда проекта EasyCast.';
	         
	        UserModule::sendMail($this->email, $subjectCustomer, $messageCustomer, true);
	    }
	}
	
	/**
	 * Оповестить всех о том, что заказ назначен (принят в обработку)
	 */
	protected function sendPendingOrderNotifications()
	{
	    $viewUrl = $this->createOrderViewUrl();
	    $subjectTeam = '[EasyCast] Заказ №'.$this->id.' принят в обработку пользователем '.$this->solver->fullname;
	    $messageTeam = 'Заказ  №'.$this->id.' принят в обработку пользователем'.$this->solver->fullname.'.<br><br>';
	    $messageTeam .= '<br><br>';
	    $messageTeam .= 'Просмотреть заказ можно по ссылке: '.CHtml::link($viewUrl, $viewUrl);
	     
	     
	    // Отправляем письмо нашей команде, чтобы она знала о заказе
	    // @todo отправлять SMS команде если заказ срочный
	    UserModule::sendMail('order@easycast.ru', $subjectTeam, $messageTeam);
	}
	
	/**
	 * Оповестить всех о том, что заказ обработан
	 * 
	 * @todo писать кто именно обработал заказ
	 */
	protected function sendClosedOrderNotifications()
	{
	    $viewUrl = $this->createOrderViewUrl();
	    $subjectTeam = '[EasyCast] Заказ №'.$this->id.' обработан пользователем '.$this->solver->fullname;
	    $messageTeam = 'Заказ  №'.$this->id.' обработан.<br><br>';
	    if ( $this->ourcomment )
	    {
	        $messageTeam .= 'Комментарий при обработке заказа: '.$this->comment.'<br><br>';
	    }
	    $messageTeam .= '<br><br>';
	    $messageTeam .= 'Просмотреть заказ можно по ссылке: '.CHtml::link($viewUrl, $viewUrl);
	    
	    
	    // Отправляем письмо нашей команде, чтобы она знала о заказе
	    // @todo отправлять SMS команде если заказ срочный
	    UserModule::sendMail('order@easycast.ru', $subjectTeam, $messageTeam);
	}
	
	/**
	 * Получить ссылку на просмотр заказа
	 * 
	 * @return string
	 */
	public function createOrderViewUrl()
	{
	    return Yii::app()->createAbsoluteUrl('/admin/fastOrder/view', array('id' => $this->id));
	}
	
	/**
	 * Версия заказа. Используется при создании новых записей, чтобы выбирать тип обработки
	 * @return number
	 */
	public static function getOrderVersion()
	{
	    return 20130506;
	} 
}