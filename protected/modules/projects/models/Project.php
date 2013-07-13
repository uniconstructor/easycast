<?php

/**
 * This is the model class for table "{{projects}}".
 *
 * The followings are the available columns in table '{{projects}}':
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $description
 * @property string $galleryid
 * @property string $timestart
 * @property string $timeend
 * @property string $timecreated
 * @property string $timemodified
 * @property string $leaderid
 * @property string $customerid
 * @property string $orderid
 * @property integer $isfree
 * @property string $memberscount
 * @property string $status
 */
class Project extends CActiveRecord
{
    /**
     * @var int - максимальное количество фотогрфвий в галерее проекта
     */
    const MAX_GALLERY_PHOTOS = 10;
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Project the static model class
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
		return '{{projects}}';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 */
	protected function beforeSave()
	{
	    if ( $timestart = CDateTimeParser::parse($this->timestart,'dd/MM/yyyy') )
	    {
	        $this->timestart = $timestart;
	    }
	    if ( $timeend = CDateTimeParser::parse($this->timeend,'dd/MM/yyyy') )
	    {
	        $this->timeend = $timeend;
	    }
	    
	    return parent::beforeSave();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeDelete()
	 */
	protected function beforeDelete()
	{
	    // При удалении проекта удаляем все его мероприятия
	    foreach ( $this->events as $event )
	    {
	        $event->delete();
	    }
	}
	
	/**
	 * @see parent::behaviors
	 * 
	 * @todo возможно стоит сделать все версии изображения квадратными
	 */
	public function behaviors()
	{
	    Yii::import('ext.galleryManager.*');
	    Yii::import('ext.galleryManager.models.*');
	    // настройки сохранения логотипа
	    $logoSettings = array(
            'class' => 'GalleryBehaviorS3',
            'idAttribute' => 'galleryid',
            'limit' => 1,
            // картинка проекта масштабируется в трех размерах
            'versions' => array(
                'small' => array(
                    'centeredpreview' => array(150, 150),
                ),
                'full' => array(
                    'resize' => array(530, 530),
                ),
            ),
            // галерея будет без имени
            'name'        => false,
            'description' => true,
        );
	    // Настройки фотогалереи проекта
	    $photoGallerySettings = array(
	        'class' => 'GalleryBehaviorS3',
	        'idAttribute' => 'photogalleryid',
	        'limit' => self::MAX_GALLERY_PHOTOS,
	        // фотографии проекта масштабируются в трех размерах
	        'versions' => array(
	            'small' => array(
	                'centeredpreview' => array(150, 150),
	            ),
	            'medium' => array(
	                'resize' => array(530, 330),
	            ),
	            'full' => array(
	                'resize' => array(800, 1000),
	            ),
	        ),
	        // галерея будет без имени
	        'name'        => false,
	        'description' => true,
	    );
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        ),
	        // логотип
	        'galleryBehavior' => $logoSettings,
	        // фотогалерея
	        'photoGalleryBehavior' => $photoGallerySettings,
	    );
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, type, description, timestart, timeend', 'required'),
			array('isfree', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('type, status', 'length', 'max'=>9),
			array('description, shortdescription, customerdescription', 'length', 'max'=>4095),
			array('photogalleryid, galleryid, timestart, timeend, timecreated, timemodified, leaderid, customerid, orderid, memberscount', 'length', 'max'=>11),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, type, description, galleryid, timestart, timeend, timecreated, timemodified, leaderid, customerid, orderid, isfree, memberscount, status', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		    // руководитель проекта
		    'leader' => array(self::BELONGS_TO, 'User', 'leaderid'),
		    // Все мероприятия проекта
		    'events' => array(self::HAS_MANY, 'ProjectEvent', 'projectid'),
		    // Все видимые пользователю мероприятия
		    'userevents' => array(self::HAS_MANY, 'ProjectEvent', 'projectid', 
		        'condition' => "`status` IN ('active', 'finished')",
		        'order' => "`status` ASC, `timestart` DESC"),
		    // Все активные предстоящие мероприятия
		    'activeevents' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
		        'condition' => "`status` = 'active'",
		        'order' => "`timestart` DESC"),
		    // Все завершенные мероприятия
		    'finishedevents' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
		        'condition' => "`status` = 'finished'",
		        'order' => "`timeend` DESC"),
		    // участники проекта
		    // @todo (пока непонятно как прописывать связь через 3 таблицы)
		    // 'members' => 
		    // Видео проекта
		    'videos' => array(self::HAS_MANY, 'Video', 'objectid', 
		        'condition' => "`objecttype`='project'"),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => ProjectsModule::t('name'),
			'type' => ProjectsModule::t('type'),
			'typetext' => ProjectsModule::t('type'),
			'description' => ProjectsModule::t('project_description'),
			'customerdescription' => ProjectsModule::t('project_customerdescription'),
			'shortdescription' => ProjectsModule::t('project_shortdescription'),
			'galleryid' => ProjectsModule::t('project_galleryid'),
			'timestart' => ProjectsModule::t('project_timestart'),
			'timeend' => ProjectsModule::t('project_timeend'),
			'timecreated' => ProjectsModule::t('project_timecreated'),
			'timemodified' => ProjectsModule::t('project_timemodified'),
			'leaderid' => ProjectsModule::t('project_leaderid'),
			'supportid' => ProjectsModule::t('project_supportid'),
			'customerid' => ProjectsModule::t('project_customerid'),
			'orderid' => ProjectsModule::t('project_orderid'),
			'isfree' => ProjectsModule::t('project_isfree'),
			'memberscount' => ProjectsModule::t('project_memberscount'),
			'status' => ProjectsModule::t('status'),
			'statustext' => ProjectsModule::t('status'),
		);
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('galleryid',$this->galleryid,true);
		$criteria->compare('timestart',$this->timestart,true);
		$criteria->compare('timeend',$this->timeend,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('leaderid',$this->leaderid,true);
		$criteria->compare('customerid',$this->customerid,true);
		$criteria->compare('orderid',$this->orderid,true);
		$criteria->compare('isfree',$this->isfree);
		$criteria->compare('memberscount',$this->memberscount,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Получить список возможных менеджеров проекта для выпадающего меню
	 * @return array
	 */
	public function getManagerList($emptyOption=false)
	{
	    if ( $emptyOption )
	    {
	        $result = array(0 => 'Нет');
	    }else
       {
            $result = array();
        }
	    
	    
	    $criteria = new CDbCriteria();
	    $criteria->addCondition('superuser=1');
	    $users = User::model()->findAll($criteria);
	    foreach ( $users as $user )
	    {
	        $result[$user->id] = $user->username.' '.$user->fullname;
	    }
	    
	    return $result;
	}
	
	/**
	 * Получить список возможных типов проекта
	 * @return array
	 */
	public function getTypeList()
	{
	    $result = array('' => Yii::t('coreMessages', 'choose'));
	    $types = array('ad','film','series','tvshow','expo','promo','flashmob','videoclip');
	    foreach ( $types as $type )
	    {
	        $result[$type] = ProjectsModule::t('project_type_'.$type);
	    }
	    return $result;
	}
	
	/**
	 * Получить тип проекта для отображения пользователю
	 * @param string $type
	 * @return string
	 */
	public function getTypetext($type=null)
	{
	    if ( ! $type )
	    {
	        $type = $this->type;
	    }
	    return ProjectsModule::t('project_type_'.$type);
	}
	
	/**
	 * Разослать приглашения всем подходящим участникам в базе
	 * @return bool
	 */
	public function sendInvites()
	{
	    if ( ! $events = $this->events )
	    {
	        return false;
	    }
	    
	    foreach ( $events as $event )
	    {
	        $event->sendInvites();
	    }
	    
	    return true;
	}
	
	/**
	 * Получить список статусов, в которые может перейти проект
	 * @todo добавить статус "suspended"
	 * @return array
	 */
	public function getAllowedStatuses()
	{
	    switch ( $this->status )
	    {
	        case 'draft': 
	            return array('active');
            break;
            case 'active':
                return array('finished');
            break;
	    }
	    
	    return array();
	}
	
	/**
	 * Получить статус записи для отображения пользователю
	 * @param string $status
	 */
	public function getStatustext($status=null)
	{
	    if ( ! $status )
	    {
	        $status = $this->status;
	    }
	    return ProjectsModule::t('project_status_'.$status);
	}
	
	/**
	 * Перевести объект из одного статуса в другой, выполнив все необходимые действия
	 * @param string $newStatus
	 * @return bool
	 */
	public function setStatus($newStatus)
	{
	    if ( ! in_array($newStatus, $this->getAllowedStatuses()) )
	    {
	        return false;
	    }
	    
	    $this->status = $newStatus;
	    $this->save();
	    
	    $events = $this->events;
	    
	    if ( $newStatus == 'active' )
	    {// активируем все мероприятия проекта
	        foreach ( $events as $event )
	        {
	            if ( $event->status == 'draft' AND $event->vacancies )
	            {// активируем только те мероприятия на которые уже созданы вакансии
	                $event->setStatus('active');
	            }
	        }
	    }
	    
	    if ( $newStatus == 'finished' )
	    {// завершаем все мероприятия проекта
	        foreach ( $events as $event )
	        {
	            if ( $event->status == 'active' )
	            {
	                $event->setStatus('finished');
	            }
	            if ( $event->status == 'draft' )
	            {// если проект завершен - удаляем все события которые так и не начались
	                $event->delete();
	            }
	        }
	    }
	    
	    return true;
	}
	
	/**
	 * Получить все доступные для участника вакансии в проекте
	 * @param int $questionaryId - id анкеты для которой ищутся подходящие вакансии
	 *                              (если не указан - берется id текущего пользователя)
	 * @param bool $withApplications - добавить ли в конец списка вакансии, на которые пользователь уже подал
	 *                                  заявки, которые либо еще не рассмотрели либо уже утвердили
	 * @return array
	 * 
	 * @todo выбрать все вакансии в начале не по событиям, а одним запросом
	 * @todo показать гостям вакансии, но кнопка "подать заявку" должна открывать форму регистрации
	 */
	public function getAvailableVacancies($questionaryId=null, $withApplications=true)
	{
	    if ( ( Yii::app()->user->checkAccess('Customer') OR Yii::app()->user->isGuest )
	         AND ! Yii::app()->user->checkAccess('Admin') )
	    {// для заказчика или гостя вакансий быть не может
	        return false;
	    }
	    
	    // Получаем все активные в данный момент вакансии, чтобы потом их отфильтровать
	    $activeVacancies = $this->getActiveVacancies();
	    
	    if ( Yii::app()->user->checkAccess('Admin') )
	    {// админам показываем все вакансии, ничего не фильтруя. Они не могут подавать заявки,
	        // но им нужно смотреть что не заполнено по проекту
	        return $activeVacancies;
	    }
	    
	    if ( ! $questionaryId )
	    {
	        $questionaryId = Yii::app()->getModule('user')->user()->questionary->id;
	    }
	    
	    // Фильтруем все активные вакансии, оставляя только доступные для участника
	    // и только те, на которые нет отклоненных заявок
	    $vacancies = array();
	    // Те вакансии, на которые участник уже подал заявку выведутся в списке последними
	    $applications = array();
	    
	    foreach ( $activeVacancies as $vacancy )
	    {
	        if ( $withApplications AND $vacancy->hasApplication($questionaryId, array('draft', 'active')) )
	        {// если у участника есть на эту вакансию подтвержденная или неподтвержденная заявка
    	        // также добавим ее в список, но в самый конец
    	        $applications[$vacancy->id] = $vacancy;
    	        // сразу же переходим к следующей вакансии - раз уже есть заявка, значит
    	        // участник проходит по критериям, проверка не нужна
    	        continue;
	        }
	        if ( $vacancy->isAvailableForUser($questionaryId) )
	        {// Участник проходит по критериям вакансии - добавим ее в список
	            $vacancies[$vacancy->id] = $vacancy;
	        }
	    }
	    
	    // Склеиваем вместе вакансии с заявками и без них
	    $vacancies = CMap::mergeArray($vacancies, $applications);
	    
	    return $vacancies;
	}
	
	/**
	 * Получить все вакансии для всех активных событий проекта
	 * (для админа, используется при просмотре проекта)
	 * 
	 * @return array
	 */
	public function getActiveVacancies()
	{
	    $result = array();
	    
	    foreach ( $this->activeevents as $event )
	    {
	        foreach ( $event->activevacancies as $vacancy )
	        {
	            $result[$vacancy->id] = $vacancy;
	        }
	    }
	    
	    return $result;
	}
	
	/**
	 * Получить ссылку на картинку с аватаром проекта
	 *
	 * @return string - url картинки или заглушка, если нет аватара
	 * @todo поставить другую заглушку-картинку для проекта
	 */
	public function getAvatarUrl($size='small')
	{
	    $nophoto = Yii::app()->getBaseUrl(true).'/images/nophoto.png';
	    if ( ! $avatar = $this->getGalleryCover() )
	    {// изображения проекта нет
	        return $nophoto;
	    }
	
	    // Изображение загружено - получаем самую маленькую версию
	    if ( ! $avatar = $avatar->getUrl($size) )
	    {
	        return $nophoto;
	    }
	
	    return $avatar;
	}
	
	/**
	 * Получить список изображений для элемента Carousel в Twitter Bootstrap
	 *
	 * @return array
	 *
	 * @todo перенести эту функцию в виджет EThumbCarousel
	 */
	public function getBootstrapPhotos($size="medium")
	{
	    $tbPhotos = array();
	    if ( ! $photos = $this->photoGalleryBehavior->getGalleryPhotos() )
	    {
	        return array();
	    }
	
	    $num = 0;
	    foreach ($photos as $photo)
	    {
	        $element = array();
	        $element['id']    = $photo->id;
	        $element['num']   = $num;
	        $element['image'] = $photo->getUrl($size);
	        if ( $photo->name )
	        {
	            $element['label'] = $photo->name;
	        }
	        if ( $photo->description )
	        {
	            $element['caption'] = $photo->description;
	        }
	
	        $tbPhotos[] = $element;
	        $num++;
	    }
	
	    return $tbPhotos;
	}
}