<?php

/**
 * Модель для работы с проектами
 *
 * Таблица '{{projects}}':
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
 * @property string $virtual - означает что весь проект состоит только из виртуальных мероприятий 
 *                             (настоящие в нем создать нельзя).
 *                             По смыслу идея чем-то напоминает абстрактный класс в программировании.
 * 
 * Relations:
 * @property User $leader
 * @property array $events
 * @property array $userevents
 * @property array $activeevents
 * @property array $finishedevents
 * @property array $videos
 * @property array $groups
 * @property array $opengroups
 * @property array $activegroups
 * 
 * @todo переписать relations через именованные группы условий
 */
class Project extends CActiveRecord
{
    /**
     * @var string - статус проекта: черновик. Проект только что создан. Необходимая инофрмация еще либо не внесена
     *               либо вносится в данный момент. Проект в этом статусе можно удалить.
     */
    const STATUS_DRAFT    = 'draft';
    /**
     * @var string - статус проекта: внесена вся информация (проект готов к запуску).
     *               Все мероприятия и вакансии (роли) созданы и описаны.
     */
    const STATUS_FILLED   = 'filled';
    /**
     * @var string - статус проекта: активен. Проект опубликован, идет набор людей или съемки.
     */
    const STATUS_ACTIVE   = 'active';
    /**
     * @var string - статус проекта: завершен.
     */
    const STATUS_FINISHED = 'finished';
    
    /**
     * @var string - тип проекта: не задан 
     *               Служебный тип, не может быть установлен вручную. 
     *               Создан "про запас", чтобы не было дыр в множестве типов. 
     *               Используется только в случае, когда тип проекта определить невозможно 
     *               (пока что таких ситуаций нет)
     */
    const TYPE_PROJECT     = 'project';
    /**
     * @var string - тип проекта: реклама
     */
    const TYPE_AD          = 'ad';
    /**
     * @var string - тип проекта: полнометражный фильм
     */
    const TYPE_FILM        = 'film';
    /**
     * @var string - тип проекта: сериал
     */
    const TYPE_SERIES      = 'series';
    /**
     * @var string - тип проекта: телешоу
     */
    const TYPE_TVSHOW      = 'tvshow';
    /**
     * @var string - тип проекта: показ
     */
    const TYPE_EXPO        = 'expo';
    /**
     * @var string - тип проекта: промо-акция
     */
    const TYPE_PROMO       = 'promo';
    /**
     * @var string - тип проекта: флешмоб
     */
    const TYPE_FLASHMOB    = 'flashmob';
    /**
     * @var string - тип проекта: видеоклип
     */
    const TYPE_VIDEOCLIP   = 'videoclip';
    /**
     * @var string - реалити-шоу
     */
    const TYPE_REALITYSHOW = 'realityshow';
    /**
     * @var string - докуреалити
     */
    const TYPE_DOCUREALITY = 'docureality';
    /**
     * @var string - короткометражный фильм
     */
    const TYPE_SHORTFILM   = 'shortfilm';
    /**
     * @var string - конференция
     */
    const TYPE_CONFERENCE  = 'conference';
    /**
     * @var string - концерт
     */
    const TYPE_CONCERT     = 'concert';
    /**
     * @var string - театральная постановка
     */
    const TYPE_THEATREPERFOMANCE = 'theatreperfomance';
    /**
     * @var string - мюзикл
     */
    const TYPE_MUSICAL   = 'musical';
    /**
     * @var string - корпоратив
     */
    const TYPE_CORPORATE = 'corporate';
    /**
     * @var string - фестиваль
     */
    const TYPE_FESTIVAL  = 'festival';
    /**
     * @var string - онлайн-кастинг
     */
    const TYPE_ONLINECASTING = 'festival';
    
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
	    foreach ( $this->events as $event )
	    {// При удалении проекта удаляем все его мероприятия
	        $event->delete();
	    }
	    
	    return parent::beforeDelete();
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
            'class' => 'GalleryBehavior',
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
	        'class' => 'GalleryBehavior',
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
		return array(
			array('name, type, description, timestart, timeend', 'required'),
			array('isfree, virtual', 'numerical', 'integerOnly' => true),
			array('name', 'length', 'max' => 255),
			array('type, status', 'length', 'max' => 9),
			array('description, shortdescription, customerdescription', 'length', 'max' => 4095),
			array('photogalleryid, galleryid, timestart, timeend, timecreated, timemodified, 
			    leaderid, customerid, orderid, memberscount', 'length', 'max' => 11),
			// The following rule is used by search().
			array('id, name, type, description, galleryid, timestart, timeend, timecreated, timemodified, 
			    leaderid, customerid, orderid, isfree, virtual, memberscount, status', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // Руководитель проекта
		    'leader' => array(self::BELONGS_TO, 'User', 'leaderid'),
		    
		    // Все группы проекта
		    'groups' => array(self::HAS_MANY, 'ProjectEvent', 'projectid', 
		        'condition' => "`groups`.`type` = 'group'"),
		    // Открытые группы событий (те в которые можно добавить мероприятия)
		    'opengroups' => array(self::HAS_MANY, 'ProjectEvent', 'projectid', 
		        'condition' => "(`opengroups`.`type` = 'group') AND (`opengroups`.`status` IN ('draft', 'active'))"),
		    // Активные группы проекта
		    'activegroups' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
		        'condition' => "(`activegroups`.`type` = 'group') AND (`activegroups`.`status` = 'active')"),
		    
		    // Все мероприятия проекта
		    'events' => array(self::HAS_MANY, 'ProjectEvent', 'projectid', 
		        'condition' => "`events`.`type` != 'group'"),
		    // Все видимые пользователю мероприятия
		    'userevents' => array(self::HAS_MANY, 'ProjectEvent', 'projectid', 
		        'condition' => "(`userevents`.`status` IN ('active', 'finished')) AND (`userevents`.`type` != 'group')",
		        'order' => "`userevents`.`status` ASC, `userevents`.`timestart` DESC"),
		    // Все активные предстоящие мероприятия
		    'activeevents' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
		        'condition' => "(`activeevents`.`status` = 'active') AND (`activeevents`.`type` != 'group')",
		        'order' => "`activeevents`.`timestart` DESC"),
		    // Все завершенные мероприятия
		    'finishedevents' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
		        'condition' => "`finishedevents`.`status` = 'finished' AND (`finishedevents`.`type` != 'group')",
		        'order' => "`finishedevents`.`timeend` DESC"),
		    
		    // участники проекта
		    // @todo изучить и применить связь типа "мост"
		    // 'members' =>
		     
		    // Видео проекта
		    'videos' => array(self::HAS_MANY, 'Video', 'objectid', 
		        'condition' => "`videos`.`objecttype`='project'"),
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
			'groups' => 'Группы',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 * 
	 * @todo убрать отсюда те критерии, которые не используются в админском поиске
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('description',$this->description,true);
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
		$criteria->order = 'timecreated DESC';

		return new CActiveDataProvider($this, array(
			'criteria'   => $criteria,
		    'pagination' => false,
		));
	}
	
	/**
	 * Получить список возможных менеджеров проекта для выпадающего меню
	 * @return array
	 * 
	 * @todo заменить вызовом метода из модуля User
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
	 * 
	 * @todo заменить строковые значения константами класса
	 */
	public function getTypeList()
	{
	    $result = array('' => Yii::t('coreMessages', 'choose'));
	    $types = array('ad', 'film', 'series', 'tvshow', 'expo', 'promo', 'flashmob', 'videoclip',
	        'docureality', 'realityshow', 'shortfilm', 'conference', 'concert', 'theatreperfomance',
	        'musical', 'corporate', 'festival');
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
	    if ( ! in_array($status, array(self::STATUS_ACTIVE, self::STATUS_DRAFT, self::STATUS_FILLED, self::STATUS_FINISHED)) )
	    {
	        return '';
	    }
	    return ProjectsModule::t('project_status_'.$status);
	}
	
	/**
	 * Перевести объект из одного статуса в другой, выполнив все необходимые действия
	 * @param string $newStatus
	 * @return bool
	 * 
	 * @todo вынести разные статусы по разным функциям
	 */
	public function setStatus($newStatus)
	{
	    if ( ! in_array($newStatus, $this->getAllowedStatuses()) )
	    {
	        return false;
	    }
	    
	    // сначала меняем статус самого проекта
	    $this->status = $newStatus;
	    $this->save();
	    
	    if ( $newStatus == 'active' )
	    {// проект запускается
	        foreach ( $this->groups as $group )
	        {// сначала активируем все группы событий
	            if ( $group->status == 'draft' )
	            {
	                $group->setStatus('active');
	            }
	        }
	        foreach ( $this->events as $event )
	        {// затем все отдельные мероприятия проекта
	            if ( $event->status == 'draft' AND ( $event->vacancies OR $event->group ) )
	            {// активируем только те мероприятия на которые уже созданы вакансии
	                // или те которые находятся в группе
	                $event->setStatus('active');
	            }
	        }
	    }
	    
	    if ( $newStatus == 'finished' )
	    {// проект завершается
	        foreach ( $this->opengroups as $group )
	        {// сначала завершаем все группы событий
    	        if ( $group->status == 'active' )
    	        {
    	            $group->setStatus('finished');
    	        }elseif ( $group->status == 'draft' )
    	        {// не начатые группы событий удаляем
    	            $group->delete();
    	        }
	        }
	        foreach ( $this->events as $event )
	        {// завершаем все отдельные мероприятия проекта
	            if ( $event->status == 'active' )
	            {
	                $event->setStatus('finished');
	            }elseif ( $event->status == 'draft' )
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
	 * @param bool $withGroups - получить ли вакансии групп мероприятий вместе с вакансиями для отдельных мероприятий?
	 * 
	 * @return array
	 */
	public function getActiveVacancies($withGroups=true)
	{
	    $result = array();
	    
	    foreach ( $this->activeevents as $event )
	    {// получаем вакансии для отдельных мероприятий 
	        foreach ( $event->activevacancies as $vacancy )
	        {
	            $result[$vacancy->id] = $vacancy;
	        }
	        if ( $event->group )
	        {// если мероприятие входит в группу - то добавим вакансии группы
	            foreach ( $event->group->activevacancies as $groupVacancy )
	            {
	                $result[$groupVacancy->id] = $groupVacancy;
	            }
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
	        return '';
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