<?php

/**
 * Модель для работы с проектами
 *
 * Таблица '{{projects}}':
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $description
 * @property string $shortdescription
 * @property string $customerdescription
 * @property string $galleryid
 * @property string $timestart
 * @property string $notimestart
 * @property string $timeend
 * @property string $notimeend
 * @property string $timecreated
 * @property string $timemodified
 * @property string $leaderid
 * @property string $supportid
 * @property string $customerid
 * @property string $orderid
 * @property integer $isfree
 * @property string $memberscount
 * @property string $status
 * @property string $rating
 * @property string $virtual - означает что весь проект состоит только из "виртуальных" мероприятий 
 *                             (настоящие в нем создать нельзя).
 *                             По смыслу идея чем-то напоминает абстрактный класс в программировании.
 * @property string $email - почта на которую будут приходить вопросы и заявки участников
 * 
 * Relations:
 * @property User $leader
 * @property User $support
 * @property array $events
 * @property array $userevents
 * @property array $activeevents
 * @property array $finishedevents
 * @property array $videos
 * @property ProjectEvent[] $groups
 * @property array $opengroups
 * @property array $activegroups
 * 
 * @todo переписать relations через именованные группы условий
 * @todo сделать список типов проекта настраиваемым
 * @todo если понадобится сделать 2 поля email: простой и zendesk
 * @todo созданные и приркрепленные группы полей и заявок при запуске проекта
 *       если они не использовались (в категории разделов нет разделов + в категории полей нет полей)
 * @todo запоминать в настройках созданную для этого проекта категорию заявок
 */
class Project extends SWActiveRecord
{
    /**
     * @var string - статус проекта: черновик. Проект только что создан. Необходимая инофрмация еще либо не внесена
     *               либо вносится в данный момент. Проект в этом статусе можно удалить.
     */
    const STATUS_DRAFT    = 'swProject/draft';
    /**
     * @var string - статус проекта: внесена вся информация (проект готов к запуску).
     *               Все мероприятия и вакансии (роли) созданы и описаны.
     * @deprecated не используется, оставлено для совместимости, удалить при рефакторинге
     */
    const STATUS_FILLED   = 'swProject/filled';
    /**
     * @var string - статус проекта: готов к запуску. Есть логотип, есть описание проекта.
     *               Все мероприятия и роли созданы, описаны ир настроены.
     */
    const STATUS_READY    = 'swProject/ready';
    /**
     * @var string - статус проекта: активен. Проект опубликован, идет набор людей или съемки.
     */
    const STATUS_ACTIVE   = 'swProject/active';
    /**
     * @var string - статус проекта: завершен.
     */
    const STATUS_FINISHED = 'swProject/finished';
    
    /**
     * @var string - тип проекта: не задан 
     *               Служебный тип, не может быть установлен вручную. 
     *               Создан "про запас", чтобы не было дыр в множестве типов. 
     *               Используется только в случае, когда тип проекта определить невозможно 
     *               (пока что таких ситуаций нет)
     */
    const TYPE_PROJECT     = 'project';
    /**
     * @var string - тип проекта: фотореклама
     */
    const TYPE_AD          = 'ad';
    /**
     * @var string - тип проекта: видеореклама
     */
    const TYPE_VIDEOAD     = 'videoad';
    /**
     * @var string - тип проекта: полнометражный фильм
     */
    const TYPE_FILM        = 'film';
    /**
     * @var string - тип проекта: документальный фильм
     */
    const TYPE_DOCUMENTARY = 'documentary';
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
     * @var string - тип проекта: видеоролик (например для канала youtube)
     */
    const TYPE_VIDEO       = 'video';
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
    const TYPE_ONLINECASTING = 'onlinecasting';
    
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
	 * @see CActiveRecord::beforeSave()
	 */
	protected function beforeSave()
	{
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 */
	public function afterSave()
	{
	    // после создания проекта автоматически создаем 
	    // и прикрепляем к нему группы разделов заявок и группы дополнительных полей
	    // @todo обработать ошибки
	    // @todo убрать использование type при создании категории 
	    if ( $this->isNewRecord )
	    {
	        // группы заявок
	        $sectionCategory = new Category();
	        $sectionCategory->name     = 'Группы заявок проекта '.$this->name;
	        $sectionCategory->parentid = 4;
	        $sectionCategory->type     = 'sections';
	        $sectionCategory->save();
	        // наборы дополнительных полей
	        $sectionCategory = new Category();
	        $sectionCategory->name     = 'Дополнительные вопросы проекта '.$this->name;
	        $sectionCategory->parentid = 5;
	        $sectionCategory->type     = 'extrafields';
	        $sectionCategory->save();
	        // шаблон создания анкеты
	        $sectionCategory = new Category();
	        $sectionCategory->name     = 'Шаблон создания анкеты для проекта '.$this->name;
	        $sectionCategory->parentid = 6;
	        $sectionCategory->type     = 'userfields';
	        $sectionCategory->save();
	        // шаблон отображения анкеты
	        $sectionCategory = new Category();
	        $sectionCategory->name     = 'Внешний вид заявки для проекта '.$this->name;
	        $sectionCategory->parentid = 7;
	        $sectionCategory->type     = 'categories';
	        $sectionCategory->save();
	    }
	    parent::afterSave();
	}
	
	/**
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
            'class'       => 'GalleryBehavior',
            'idAttribute' => 'galleryid',
            'limit'       => 1,
            // лого проекта масштабируется в двух размерах
            'versions' => array(
                'small' => array(
                    'centeredpreview' => array(150, 150),
                ),
                'full' => array(
                    'resize'          => array(530, 530),
                ),
            ),
            'name'        => false,
            'description' => true,
        );
	    // Настройки фотогалереи проекта
	    $photoGallerySettings = array(
	        'class'       => 'GalleryBehavior',
	        'idAttribute' => 'photogalleryid',
	        'limit'       => self::MAX_GALLERY_PHOTOS,
	        // фотографии проекта масштабируются в трех размерах
	        'versions' => array(
	            'small' => array(
	                'centeredpreview' => array(150, 150),
	            ),
	            'medium' => array(
	                'resize'          => array(530, 330),
	            ),
	            'full' => array(
	                'resize'          => array(800, 1000),
	            ),
	        ),
	        'name'        => true,
	        'description' => true,
	    );
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class'           => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        ),
	        // логотип
	        'galleryBehavior'      => $logoSettings,
	        // фотогалерея
	        'photoGalleryBehavior' => $photoGallerySettings,
	        // для отображения раскрывающейся сетки проектов с помощью css3
	        'CdGridViewProjectBehavior' => array(
	            'class'       => 'projects.behaviors.CdGridViewProjectBehavior',
	            'ajaxOptions' => array(
	                'url' => Yii::app()->createUrl('//projects/project/ajaxInfo'),
                ),
	        ),
	        // подключаем расширение для работы со статусами
	        'swBehavior' => array(
	            'class' => 'application.extensions.simpleWorkflow.SWActiveRecordBehavior',
	        ),
	    );
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, type, description', 'required'),
			array('isfree, virtual', 'numerical', 'integerOnly' => true),
			array('name, email', 'length', 'max' => 255),
			array('email', 'email'),
			array('email', 'unique'),
			array('type, status', 'length', 'max' => 50),
			array('description, shortdescription, customerdescription', 'length', 'max' => 4095),
			array('photogalleryid, galleryid, timestart, timeend, timecreated, timemodified, 
			    leaderid, supportid, customerid, orderid, memberscount, rating, notimestart, notimeend', 'length', 'max' => 12),
		    
		    /*array('timeend', 'type', 'type' => 'date', 
		        'message' => 'Неправильный формат даты', 'dateFormat' => 'dd.MM.yyyy'),*/
		    // делаем обязательными дату начала и окончания проекта, только если не установлены галочки
		    // "без даты начала" или "без даты окончания"
		    array('timestart', 'ext.YiiConditionalValidator',
		        'if' => array(
		            array('notimestart', 'compare', 'compareValue' => '0'),
		        ),
		        'then' => array(
		            array('timestart', 'required'),
		        ),
		    ),
		    array('timeend', 'ext.YiiConditionalValidator',
		        'if' => array(
		            array('notimeend', 'compare', 'compareValue' => '0'),
		        ),
		        'then' => array(
		            array('timeend', 'required'),
		        ),
		    ),
		    array('timestart', 'parseDateInput'),
		    array('timeend', 'parseDateInput'),
		    
			// The following rule is used by search().
			array('id, name, type, description, galleryid, timestart, timeend, timecreated, timemodified, 
			    leaderid, customerid, orderid, isfree, virtual, memberscount, status, rating', 'safe', 'on' => 'search'),
		);
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    return array(
	        // последние созданные записи
	        'lastCreated' => array(
	            'order' => $this->getTableAlias(true).'.`timecreated` DESC'
	        ),
	        // последние измененные записи
	        'lastModified' => array(
	            'order' => $this->getTableAlias(true).'.`timemodified` DESC'
	        ),
	        // лучшие по рейтингу
	        'bestRated' => array(
	            'order' => $this->getTableAlias(true).'.`rating` DESC'
	        ),
	        // хучшие по рейтингу
	        'worstRated' => array(
	            'order' => $this->getTableAlias(true).'.`rating` DESC'
	        ),
	    );
	}
	
	/**
	 * Именованная группа условий поиска - выбрать записи по статусам
	 * @param array|string $statuses - массив статусов или строка если статус один
	 * @return Project
	 */
	public function withStatus($statuses=array())
	{
	    $criteria = new CDbCriteria();
	    if ( ! is_array($statuses) )
	    {// нужен только один статус, и он передан строкой - сделаем из нее массив
	        $statuses = array($statuses);
	    }
	    if ( empty($statuses) )
	    {// Если статус не указан - выборка по этому параметру не требуется
	        return $this;
	    }
	    
	    $criteria->addInCondition($this->getTableAlias(true).'.`status`', $statuses);
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска - выбрать проекты по типу
	 * @param array $types
	 * @return Project
	 */
	public function withType($types=array())
	{
	    $criteria = new CDbCriteria();
	    if ( ! is_array($types) )
	    {
            $types = array($types);
	    }
	    if ( empty($types) )
	    {// тип не указан - выборка по этому параметру не требуется
            return $this;
	    }
	     
	    $criteria->addInCondition($this->getTableAlias(true).'.`type`', $types);
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // Руководитель проекта
		    'leader' => array(self::BELONGS_TO, 'User', 'leaderid'),
		    // Помошник руководителя
		    'support' => array(self::BELONGS_TO, 'User', 'supportid'),
		    
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
			'notimestart' => 'Дата начала уточняется',
			'notimeend' => 'Без даты окончания',
			'rating' => 'Рейтинг',
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
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('type', $this->type, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('timestart', $this->timestart, true);
		$criteria->compare('timeend', $this->timeend, true);
		$criteria->compare('timecreated', $this->timecreated, true);
		$criteria->compare('timemodified', $this->timemodified, true);
		$criteria->compare('leaderid', $this->leaderid,true);
		$criteria->compare('customerid', $this->customerid, true);
		$criteria->compare('orderid', $this->orderid, true);
		$criteria->compare('memberscount', $this->memberscount, true);
		$criteria->compare('status', $this->status, true);
		$criteria->order = 'timecreated DESC';

		return new CActiveDataProvider($this, array(
			'criteria'   => $criteria,
		    'pagination' => false,
		));
	}
	
	/**
	 * Получить описание проекта (для участника или заказчика)
	 * Если нужное описание отсутствует - подставляется то которое есть
	 * @param string - режим просмотра сайта: для участника или для заказчика
	 * @return string
	 */
	public function getFullDescription($userMode='')
	{
	    $cutomerDescription = $this->customerdescription;
	    $userDescription    = $this->description;
	    
	    if ( ! $userMode )
	    {
	        $userMode = Yii::app()->getModule('user')->getViewMode();
	    }
	    if ( ! trim(strip_tags($cutomerDescription)) )
	    {
	        $cutomerDescription = $this->description;
	    }
	    if ( ! trim(strip_tags($userDescription)) )
	    {
	        $userDescription = $this->customerdescription;
	    }
	    if ( $userMode == 'user' )
	    {
	        return $userDescription;
	    }else
	    {
	        return $cutomerDescription;
	    }
	}
	
	/**
	 * Получить список возможных менеджеров проекта для выпадающего меню
	 * @return array
	 * 
	 * @deprecated использовать метод из модуля User, оставлено для совместимости
	 */
	public function getManagerList($emptyOption=false)
	{
	    $userModule = Yii::app()->getModule('user');
	    return $userModule::getAdminList($emptyOption);
	}
	
	/**
	 * Получить список возможных типов проекта
	 * @return array
	 * 
	 * @todo получать варианты из настраиваемого списка
	 */
	public function getTypeList()
	{
	    $result = array('' => '--- '.Yii::t('coreMessages', 'choose').' ---');
	    $types  = array('ad', 'videoad', 'film', 'documentary', 'series', 'tvshow', 'expo', 'promo',
	        'flashmob', 'videoclip', 'video', 'docureality', 'realityshow', 'shortfilm', 'conference', 
	        'concert', 'theatreperfomance', 'musical', 'corporate', 'festival');
	    foreach ( $types as $type )
	    {
	        $result[$type] = ProjectsModule::t('project_type_'.$type);
	    }
	    asort($result);
	    
	    $result = CMap::mergeArray(array('' => Yii::t('coreMessages', 'choose')), $result);
	    return $result;
	}
	
	/**
	 * Получить тип проекта для отображения пользователю
	 * @param string $type
	 * @return string
	 * 
	 * @deprecated заменить на getTypeLabel при рефакторинге
	 */
	public function getTypeText($type=null)
	{
	    if ( ! $type )
	    {
	        $type = $this->type;
	    }
	    $projectsModule = Yii::app()->getModule('projects');
	    return $projectsModule::t('project_type_'.$type);
	}
	
	/**
	 * Получить тип проекта для отображения пользователю
	 * @param string $type
	 * @return string
	 * 
	 * @todo стандартизировать имена функций получения типа: для любого объекта, обладающего типом
	 *       эта функция должна называться getTypeLabel
	 */
	public function getTypeLabel($type=null)
	{
	    return $this->getTypeText($type);
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
	 * @return array
	 * 
	 * @deprecated
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
	public function getStatusText($status=null)
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
	 * @deprecated
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
	    {// определяем id текущего участника, если он не задан извне
	        $questionaryId = Yii::app()->getModule('questionary')->getCurrentQuestionaryId();
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
     * @deprecated 
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
	 * @return string - url картинки или заглушка, если нет аватара
	 */
	public function getAvatarUrl($size='small', $insertPlaceholder=false)
	{
	    $nophoto = '';
	    if ( $insertPlaceholder )
	    {// вставляем заглушку вместо изображения
	        $nophoto = Yii::app()->getBaseUrl(true).'/images/project_placeholder.png';
	    }
	    if ( ! $avatar = $this->getGalleryCover() )
	    {// изображения проекта нет - выводим заглушку
	        return $nophoto;
	    }
	    // Изображение загружено - получаем нужную версию
	    if ( ! $avatar = $avatar->getUrl($size) )
	    {// нет версии нужного размера
	        return $nophoto;
	    }
	    
	    return $avatar;
	}
	
	/**
	 * Определить, существует ли аватар для проекта
	 * @return bool
	 */
	public function hasAvatar()
	{
	    return (bool)$this->getGalleryCover();
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
	
	/**
	 * 
	 * @param string $attribute
	 * @param array $attribute
	 * @return int
	 */
	public function parseDateInput($attribute, $params)
	{
	    if ( ! $this->hasErrors() )
	    {
	        if ( $date = CDateTimeParser::parse($this->$attribute, Yii::app()->params['inputDateFormat']) )
	        {
	            $this->$attribute = $date;
	        }
	    }
	}
    
    
	/**
	 * Действия, выполняемые при запуске проекта
	 * 
	 * @param Project $model
	 * @param string $srcStatus
	 * @param string $destStatus
	 * @return bool
	 */
	public function toActive($model, $srcStatus, $destStatus)
	{
	    // сначала активируем все группы событий
	    $groups = ProjectEvent::model()->forProject($this->id)->
            withStatus(ProjectEvent::STATUS_DRAFT)->groupsOnly()->findAll();
	    foreach ( $groups as $group )
	    {
    	    $group->setStatus('active');
	    }
	    
	    // активируем только те мероприятия на которые уже созданы роли
	    $filledEvents = ProjectEvent::model()->forProject($this->id)->
            withStatus(ProjectEvent::STATUS_DRAFT)->withVacancies()->findAll();
	    foreach ( $filledEvents as $event )
	    {// или те которые находятся в группе
	       $event->setStatus('active');
	    }
	    
	    // затем все отдельные мероприятия проекта
	    $groupEvents = ProjectEvent::model()->forProject($this->id)->
            withStatus(ProjectEvent::STATUS_DRAFT)->hasGroup()->findAll();
	    foreach ( $groupEvents as $event )
	    {// или те которые находятся в группе
	        $event->setStatus('active');
	    }
	    
	    return true;
	}
	
	/**
	 * Действия, выполняемые при завершении проекта
	 *
	 * @param Project $model
	 * @param string $srcStatus
	 * @param string $destStatus
	 * @return bool
	 */
	public function toFinished($model, $srcStatus, $destStatus)
	{
	    // сначала завершаем все активные группы событий
	    $activeGroups = ProjectEvent::model()->forProject($this->id)->
            withStatus(array(ProjectEvent::STATUS_ACTIVE))->groupsOnly()->findAll();
	    foreach ( $activeGroups as $group )
	    {
	        $group->setStatus('finished');
	    }
	    
	    // не начатые группы событий удаляем
	    $draftGroups = ProjectEvent::model()->forProject($this->id)->
            withStatus(array(ProjectEvent::STATUS_DRAFT))->groupsOnly()->findAll();
	    foreach ( $draftGroups as $group )
	    {
    	    $group->delete();
	    }
	    
	    // завершаем все отдельные мероприятия проекта
	    $activeEvents = ProjectEvent::model()->forProject($this->id)->
            withStatus(array(ProjectEvent::STATUS_ACTIVE))->exceptGroups()->findAll();
	    foreach ( $activeEvents as $event )
	    {
	        $event->setStatus('finished');
	    }
	    
	    // удаляем все события которые так и не начались
	    $draftEvents = ProjectEvent::model()->forProject($this->id)->
            withStatus(array(ProjectEvent::STATUS_DRAFT))->exceptGroups()->findAll();
	    foreach ( $draftEvents as $event )
	    {
	        $event->delete();
	    }
	    
	    return true;
	}
	
	/**
	 * Можно ли отметить проект как готовый к запуску?
	 * @return boolean
	 */
	protected function isReady()
	{
	    return true;
    }
    
	/**
	 * Можно ли запустить проект?
	 * @return boolean
	 * 
	 * @todo до запуска проверять что для всех событий проекта есть описание
	 * @todo до запуска проверять что для всех ролей установлены критерии поиска и описание
	 */
	protected function canActivate()
	{
	    return true;
	}
	
	/**
	 * Можно ли приостановить проект?
	 * @return boolean
	 * 
	 * @todo проверять наличие хотя бы одного активного мероприятия
	 */
	protected function canSuspend()
	{
	    return true;
	}
	
	/**
	 * Можно ли сейчас завершить проект?
	 * @return boolean
	 */
	protected function canFinish()
	{
	    return true;
	}
}