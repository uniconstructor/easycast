<?php

/**
 * Модель для работы с проектами
 *
 * Таблица '{{projects}}':
 * @property integer $id
 * @property string $name
 * @property string $typeid
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
 * @property string $leaderid - id руководителя проекта в таблице User
 * @property string $supportid - id помошника руководителя в таблице User
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
 * Геттеры:
 * @property string $type
 * @property SWNode $statusNode - текущий статус записи 
 * 
 * Relations:
 * @property User           $leader
 * @property User           $support
 * @property ProjectEvent[] $events
 * @property Video[]        $videos
 * @property EasyListItem   $typeItem
 * 
 * 
 * @property array $userevents (deprecated)
 * @property array $activeevents (deprecated)
 * @property array $finishedevents (deprecated)
 * @property array $groups (deprecated)
 * @property array $opengroups (deprecated)
 * @property array $activegroups (deprecated)
 * 
 * @todo переписать relations через именованные группы условий
 * @todo сделать список типов проекта настраиваемым
 * @todo если понадобится сделать 2 поля email: простой и zendesk
 * @todo созданные и приркрепленные группы полей и заявок при запуске проекта
 *       если они не использовались (в категории разделов нет разделов + в категории полей нет полей)
 * @todo запоминать в настройках созданную для этого проекта категорию заявок
 * @todo константы типов проекта больше не используются - их следует удалить из класса,
 *       и вычистить все упоминания о них из остального кода 
 * @todo сделать лого отдельным полем или настройкой, но не галереей
 * @todo (при удалении проекта) удалять элементы списка, ссылающиеся на этот проект
 * @todo (при удалении проекта) удалять настройки, ссылающиеся на этот проект
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
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_PROJECT     = 'project';
    /**
     * @var string - тип проекта: фотореклама
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_AD          = 'ad';
    /**
     * @var string - тип проекта: видеореклама
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_VIDEOAD     = 'videoad';
    /**
     * @var string - тип проекта: полнометражный фильм
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_FILM        = 'film';
    /**
     * @var string - тип проекта: документальный фильм
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_DOCUMENTARY = 'documentary';
    /**
     * @var string - тип проекта: сериал
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_SERIES      = 'series';
    /**
     * @var string - тип проекта: телешоу
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_TVSHOW      = 'tvshow';
    /**
     * @var string - тип проекта: показ
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_EXPO        = 'expo';
    /**
     * @var string - тип проекта: промо-акция
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_PROMO       = 'promo';
    /**
     * @var string - тип проекта: флешмоб
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_FLASHMOB    = 'flashmob';
    /**
     * @var string - тип проекта: видеоролик (например для канала youtube)
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_VIDEO       = 'video';
    /**
     * @var string - тип проекта: видеоклип
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_VIDEOCLIP   = 'videoclip';
    /**
     * @var string - реалити-шоу
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_REALITYSHOW = 'realityshow';
    /**
     * @var string - докуреалити
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_DOCUREALITY = 'docureality';
    /**
     * @var string - короткометражный фильм
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_SHORTFILM   = 'shortfilm';
    /**
     * @var string - конференция
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_CONFERENCE  = 'conference';
    /**
     * @var string - концерт
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_CONCERT     = 'concert';
    /**
     * @var string - театральная постановка
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_THEATREPERFOMANCE = 'theatreperfomance';
    /**
     * @var string - мюзикл
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_MUSICAL   = 'musical';
    /**
     * @var string - корпоратив
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_CORPORATE = 'corporate';
    /**
     * @var string - фестиваль
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_FESTIVAL  = 'festival';
    /**
     * @var string - онлайн-кастинг
     * @deprecated тип проекта теперь задается не в константах а настройкой в таблице config
     */
    const TYPE_ONLINECASTING = 'onlinecasting';
    
    /**
     * @var int - максимальное количество фотогрфвий в галерее проекта
     */
    const MAX_GALLERY_PHOTOS = 10;
    
    /**
     * @var Config - системная настройка хранящая список типов проекта
     */
    private $_typesListConfig;
    
	/**
	 * Returns the static model of the specified AR class.
	 * 
	 * @param string $className active record class name.
	 * @return Project the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    /**
     * 
     */
    public static function cloakedIds()
    {
        return array(345, 333, 315, 307, 305, 302, 306, 301, 295, 264, 265, 158); // публичные слушания
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
	    // @todo убрать использование категорий - использовать списки  
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
	public function beforeDelete()
	{
	    $transaction = $this->getDbConnection()->beginTransaction();
	    // удаляем все связанные с проектом записи чтобы не оставлять битых ссылок
	    try
	    {
	        // удаляем мероприятия
	        $events = ProjectEvent::model()->forProject($this->id)->findAll();
	        foreach ( $events as $event )
	        {
    	        if ( ! $event->delete() )
    	        {// не удалось удалить событие
    	            $transaction->rollback();
    	            // запоминаем ошибку в логах
    	            $msg = 'Unable to delete event (id='.CVarDumper::dumpAsString($event->id).')'.
    	                'during project deletion (id='.CVarDumper::dumpAsString($this->id).')';
    	            Yii::log($msg, CLogger::LEVEL_ERROR, 'projects');
    	            // прерываем процесс удаления
    	            return false;
    	        }
	        }
	        // удаляем видео
	        $videos = Video::model()->forObject('Project', $this->id)->findAll();
	        foreach ( $videos as $video )
	        {// @todo проверка результата удаления видео
	            $video->delete();
	        }
	        // все операции прошли успешно - завершаем транзакцию
	        $transaction->commit();
	    }catch ( Exception $e )
	    {// ошибка прир удалении проекта
	        $transaction->rollback();
	        // запоминаем ошибку в логах
	        $msg = "Exception: ".$e->getMessage().' ('.$e->getFile().':'.$e->getLine().")\n".$e->getTraceAsString()."\n";
	        Yii::log($msg, CLogger::LEVEL_ERROR, 'projects.projects');
	        // прерываем процесс удаления
	        return false;
	    }
	    return parent::beforeDelete();
	}
	
	/**
	 * @see CActiveRecord::afterDelete()
	 */
	public function afterDelete()
	{
	    parent::afterDelete();
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
	    $relations = array(
	        // Руководитель проекта
	        'leader' => array(self::BELONGS_TO, 'User', 'leaderid'),
	        // Помошник руководителя
	        'support' => array(self::BELONGS_TO, 'User', 'supportid'),
	        // мероприятия проекта
	        'events' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
	            'condition' => "`events`.`type` != 'group'",
	        ),
	        // элемент списка, содержащий тип проекта
	        'typeItem' => array(self::BELONGS_TO, 'EasyListItem', 'typeid'),
	        // видео проекта
	        'videos' => array(self::HAS_MANY, 'Video', 'objectid',
	            'scopes' => array(
    	            'withObjectType' => array('project'),
    	        ),
	        ),
	        // @todo участники проекта: (связь типа "мост")
	        // 'members' =>
	        //// устаревшие связи ////
	        // @todo удалить устаревшую связь: использовать списки вместо групп мероприятий
	        'groups' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
	            'condition' => "`groups`.`type` = 'group'"),
            // @todo удалить устаревшую связь: Открытые группы событий
	        'opengroups' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
	            'condition' => "(`opengroups`.`type` = 'group') AND (`opengroups`.`status` IN ('draft', 'active'))"),
            // @todo удалить устаревшую связь: Активные группы проекта
	        'activegroups' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
	            'condition' => "(`activegroups`.`type` = 'group') AND (`activegroups`.`status` = 'active')"),
	        // @todo удалить устаревшую связь: Все видимые пользователю мероприятия
	        'userevents' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
	            'condition' => "(`userevents`.`status` IN ('active', 'finished')) AND (`userevents`.`type` != 'group')",
	            'order' => "`userevents`.`status` ASC, `userevents`.`timestart` DESC"),
            // @todo удалить устаревшую связь: Все активные предстоящие мероприятия
	        'activeevents' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
	            'condition' => "(`activeevents`.`status` = 'active') AND (`activeevents`.`type` != 'group')",
	            'order' => "`activeevents`.`timestart` DESC"),
            // @todo удалить устаревшую связь: Все завершенные мероприятия
	        'finishedevents' => array(self::HAS_MANY, 'ProjectEvent', 'projectid',
	            'condition' => "`finishedevents`.`status` = 'finished' AND (`finishedevents`.`type` != 'group')",
	            'order'     => "`finishedevents`.`timeend` DESC"),
	    );
	    // подключаем связи для настроек
	    if ( ! $this->asa('ConfigurableRecordBehavior') )
	    {
	        $this->attachBehavior('ConfigurableRecordBehavior', array(
	            'class' => 'application.behaviors.ConfigurableRecordBehavior',
	            'defaultOwnerClass' => get_class($this),
	        ));
	    }
	    $configRelations = $this->asa('ConfigurableRecordBehavior')->getDefaultConfigRelations();
	    return CMap::mergeArray($relations, $configRelations);
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
                    'resize' => array(530, 530),
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
	                'resize' => array(530, 330),
	            ),
	            'full' => array(
	                'resize' => array(800, 1000),
	            ),
	        ),
	        'name'        => true,
	        'description' => true,
	    );
	    return array(
	        // автоматическое заполнение дат создания и изменения
            'EcTimestampBehavior' => array(
                'class' => 'application.behaviors.EcTimestampBehavior',
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
	        // это поведение позволяет изменять набор связей модели в зависимости от того какие данные в ней находятся
	        'CustomRelationsBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationsBehavior',
	        ),
	        // группы условий для поиска по данным моделей, которые ссылаются
	        // на эту запись по составному ключу objecttype/objectid
	        'CustomRelationTargetBehavior' => array(
	            'class' => 'application.behaviors.CustomRelationTargetBehavior',
	            // @todo
	            /*'customRelations' => array(
	                'videos' => array(
                        'model'     => 'Video',
                        'typeField' => 'objecttype',
                        'idField'   => 'objectid',
                    ),
    	        ),
	            'searchRelation' => 'videos',*/
	        ),
	        // настройки для модели и методы для поиска по этим настройкам
	        'ConfigurableRecordBehavior' => array(
	            'class'      => 'application.behaviors.ConfigurableRecordBehavior',
	            // настройка "баннер" создается автоматически вместе с каждым проектом
	            'autoCreate' => array('banner'),
	        ),
	    );
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, typeid, description', 'required'),
			array('isfree, virtual', 'numerical', 'integerOnly' => true),
			array('name, email', 'length', 'max' => 255),
			array('email', 'email'),
			array('email', 'unique'),
			array('status, type', 'length', 'max' => 50),
			array('description, shortdescription, customerdescription', 'length', 'max' => 4095),
			array('photogalleryid, galleryid, timestart, timeend, timecreated, timemodified, 
			    leaderid, supportid, customerid, orderid, memberscount, rating, notimestart, 
			    notimeend, typeid', 'length', 'max' => 11,
            ),
		    // делаем обязательными дату начала и окончания проекта, 
		    // только если не установлены галочки "без даты начала"
		    array('timestart', 'ext.YiiConditionalValidator',
		        'if' => array(
		            array('notimestart', 'compare', 'compareValue' => '0'),
		        ),
		        'then' => array(
		            array('timestart', 'required'),
		        ),
		    ),
		    // или "без даты окончания"
		    array('timeend', 'ext.YiiConditionalValidator',
		        'if' => array(
		            array('notimeend', 'compare', 'compareValue' => '0'),
		        ),
		        'then' => array(
		            array('timeend', 'required'),
		        ),
		    ),
		    // проверка даты начала через фильтр
		    array('timestart', 'ext.YiiConditionalValidator',
                'if' => array(
                    array(
                        'timestart', 'date',
                        'allowEmpty' => false,
                        'format'     => Yii::app()->params['yiiDateFormat'],
                    ),
                ),
                'then' => array(
                    array(
                        'timestart', 'filter',
                        'filter' => array('EcDateTimeParser', 'parse'),
                    ),
                ),
            ),
		    // проверка даты окончания через фильтр
		    array('timeend', 'ext.YiiConditionalValidator',
                'if' => array(
                    array(
                        'timeend', 'date',
                        'allowEmpty' => false,
                        'format'     => Yii::app()->params['yiiDateFormat'],
                    ),
                ),
                'then' => array(
                    array(
                        'timeend', 'filter',
                        'filter' => array('EcDateTimeParser', 'parse'),
                    ),
                ),
            ),
			// The following rule is used by search().
			array('id, name, typeid, description, galleryid, timestart, timeend, timecreated, timemodified, 
			    leaderid, customerid, orderid, isfree, virtual, memberscount, status, rating', 
			    'safe', 'on' => 'search',
	        ),
		);
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    // условия поиска по датам создания и изменения 
	    $timestampScopes = $this->asa('EcTimestampBehavior')->getDefaultTimestampScopes();
	    $visibleStatuses = array(swProject::ACTIVE, swProject::SUSPENDED, swProject::FINISHED);
	    // условия поиска для проекта
	    $modelScopes = array(
	        // лучшие по рейтингу
	        'bestRated' => array(
	            'order' => $this->getTableAlias(true).'.`rating` DESC'
	        ),
	        // худшие по рейтингу
	        'worstRated' => array(
	            'order' => $this->getTableAlias(true).'.`rating` ASC'
	        ),
	        // видимые участникам
	        'visible' => array(
	            'scopes' => array(
    	            'withStatus' => array($visibleStatuses),
    	        ),
	        ),
            // скрываем из общего списка те проекты которые не хотим показывать в общем доступе
            'exceptCloaked'  => array(
                'scopes' => array(
                    'exceptId' => array($this->getCloakedIds()),
                ),
            ),
            
	    );
	    return CMap::mergeArray($timestampScopes, $modelScopes);
	}
	
	/**
	 * Именованная группа условий поиска - выбрать проекты по типу (используя сокращенное название типа)
	 * 
	 * @param  string|array $type - тип проекта или массив таких типов 
	 * @param  string $operation  - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return Project
	 */
	public function withType($type, $operation='AND')
	{
	    if ( ! $type )
	    {// условие не используется
            return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'withValue' => array($type),
	            ),
	        ),
	    );
	    $criteria->together = true;
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	    
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска - выбрать проекты по типу (используя id типа)
	 * 
	 * @param  int|array $typeId - id типа проекта в таблице {{easy_list_items}} или массив таких id 
	 * @param  string $operation - как присоединить это условие к остальным? (AND/OR/AND NOT/OR NOT)
	 * @return Project
	 */
	public function withTypeId($typeId, $operation='AND')
	{
	    if ( ! $typeId )
	    {// условие не используется
            return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`typeid`', $typeId);
	    
	    $this->getDbCriteria()->mergeWith($criteria, $operation);
	    
	    return $this;
	}
	
	/**
	 * Именованная группа условий: получить все проекты указанного руководителя
	 * 
	 * @param int $userId - id руководителя проекта в таблице Users (или массив таких id)
	 *                      Если указан 0 - то будут найдены все проекты без руководителя
	 * @return Project
	 */
	public function forLeader($userId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`leaderid`', $userId);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
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
			'typeid' => ProjectsModule::t('type'),
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
			'notimeend' => 'Дата окончания неизвестна (для длительных проектов)',
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
	 * Если специальное описание заказчика/участника отсутствует - подставляется то которое есть
	 * 
	 * @param  string - режим просмотра сайта: для участника или для заказчика
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
	    if ( $userMode === 'user' )
	    {
	        return $userDescription;
	    }elseif ( $userMode === 'customer' )
	    {
	        return $cutomerDescription;
	    }else
	    {
	        return $userDescription;
	    }
	}
	
	/**
	 * Получить тип проекта для отображения пользователю
	 * 
	 * @param  string $field - как получить тип проекта (поле объекта EasyListItem)
	 *                         name  - название для пользователя
	 *                         value - короткое служебное название
	 *                         id    - id элемента в списке типов
	 * @return string
	 */
	public function getType($field='name')
	{
	    if ( $this->typeItem AND isset($this->typeItem->$field) )
	    {
	        return $this->typeItem->$field;
	    }
	    return '';
	}
	
	/**
	 * Задать тип проекта (сеттер)
	 * 
	 * @param  string|int|EasyListItem $type - id или короткое название типа проекта
	 * @return void
	 */
	public function setType($type)
	{
	    if ( ! $type )
	    {// сбросить тип проекта нельзя - только изменить
	        return;
	    }
	    $typesListId = $this->getProjectTypesConfig()->valueid;
	    
	    if ( is_object($type) AND ( get_class($type) === 'EasyListItem' ) )
	    {// передан элемент списка
	        if ( $type->easylistid === $typesListId )
	        {// этот элемент точно является типом проекта
	            $this->typeid = $type->id;
	            return;
	        }
	    }
	    if ( intval($type) )
	    {// передан id элемента списка
	        if ( $type = EasyListItem::model()->forList($typesListId)->findByPk($type) )
	        {
	            $this->typeid = $type->id;
	            return;
	        }
	    }
	    if ( is_string($type) )
	    {// передано короткое название типа
	        if ( $type = EasyListItem::model()->forList($typesListId)->withValue($type)->find() )
	        {
	            $this->typeid = $type->id;
	            return;
	        }
	    }
	    // попытка задать неправильный тип проекта
	    $msg = 'Trying to set incorrect project type: '.CVarDumper::dumpAsString($type);
	    Yii::log($msg, CLogger::LEVEL_ERROR, 'modules.projects');
	}
	
	/**
	 * Получить тип проекта для отображения пользователю (alias) 
	 * 
	 * @param  string $type
	 * @return string
	 * 
	 * @todo убрать использование $type
	 */
	public function getTypeLabel($type=null)
	{
	    if ( $type )
	    {
	        $items = $this->getProjectTypesConfig()->selectedListItems;
	        $types = CHtml::listData($items, 'value', 'name');
	        if ( isset($types[$type]) )
	        {
	            return $types[$type];
	        }
	    }else
	    {
	        return $this->getType();
	    }
	}
	
	/**
	 * Получить список возможных типов проекта
	 *
	 * @return array
	 */
	public function getTypesList()
	{
	    $items = $this->getProjectTypesConfig()->selectedListItems;
	    return CHtml::listData($items, 'id', 'name');
	}
	
	/**
	 * Получить системную настройку, отвечающую за возможные типы проекта
	 * 
	 * @return Config
	 */
	public function getProjectTypesConfig()
	{
	    if ( $this->_typesListConfig )
	    {
	        return $this->_typesListConfig;
	    }
	    if ( $this->_typesListConfig = Config::model()->withName('projectTypesListId')->systemOnly()->find() )
	    {
	        return $this->_typesListConfig;
	    }
	    throw new CException('Не удалось найти системную настройку со списком типов проекта');
	}
	
	/**
	 * @return SWNode
	 */
	public function getStatusNode()
	{
	    return $this->swGetStatus();
	}
	
	/**
	 * Разослать приглашения всем подходящим участникам в базе
	 * 
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
	 * Получить все доступные для участника вакансии в проекте
	 * 
	 * @param int $questionaryId     - id анкеты для которой ищутся подходящие вакансии
	 *                                 (если не указан - берется id текущего пользователя)
	 * @param bool $withApplications - добавить ли в конец списка роли,  
	 *                                 на которые пользователь уже подал заявки, 
	 *                                 которые либо еще не рассмотрели либо уже утвердили
	 * @return array
	 * 
	 * @todo переписать, используя списки
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
	 * Получить ссылку на картинку с аватаром проекта
	 * 
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
	 * 
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
	    $num      = 0;
	    if ( ! $photos = $this->photoGalleryBehavior->getGalleryPhotos() )
	    {
	        return array();
	    }
	    foreach ( $photos as $photo )
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
     * @return bool
     */
    public function hasBanner()
    {
        if ( ! $this->getBannerUrl() )
        {
            return false;
        }
        return true;
    }
    
    /**
     * 
     * @return string
     */
    public function getBannerUrl()
    {
        if ( ! $this->hasConfig('banner') )
        {
            return null;
        }
        return $this->getConfig('banner');
    }
    
    /**
     * 
     * @return Config|null
     */
    public function getBannerObject()
    {
        if ( ! $this->hasConfig('banner') )
        {
            return null;
        }
        return $this->getConfigValueObject('banner');
    }
    
    /**
     * @return bool - true для проектов которые мы не хотим показывать в общем доступе
     */
    public function isCloaked()
    {
        if ( in_array((int)$this->id, $this->getCloakedIds()) )
        {
            return true;
        }
        return false;
    }
    
    /**
     * @return array
     */
    public function getCloakedIds()
    {
        return self::cloakedIds();
    }
	
	/**
	 * Действия, выполняемые при запуске проекта
	 * 
	 * @param  Project $model
	 * @param  string $srcStatus
	 * @param  string $destStatus
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
	 * @param  Project $model
	 * @param  string $srcStatus
	 * @param  string $destStatus
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
	 * 
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
	 * 
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
	 * 
	 * @return boolean
	 */
	protected function canFinish()
	{
	    return true;
	}
	
	//// устаревшие методы (временно сохранены для совместимости) ////
	
	/**
	 *
	 * @param  string $attribute
	 * @param  array $attribute
	 * @return int
	 *
	 * @deprecated использовать фильтр в rules()
	 */
	/*public function parseDateInput($attribute, $params)
	{
	    if ( ! $this->hasErrors() )
	    {
	        if ( $date = CDateTimeParser::parse($this->$attribute, Yii::app()->params['inputDateFormat']) )
	        {
	            $this->$attribute = $date;
	        }
	    }
	}*/
	
	/**
	 * Получить все вакансии для всех активных событий проекта
	 * (для админа, используется при просмотре проекта)
	 *
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
	 * Перевести объект из одного статуса в другой, выполнив все необходимые действия
	 *
	 * @param  string $newStatus
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
	 * Получить тип проекта для отображения пользователю
	 *
	 * @param string $type
	 * @return string
	 *
	 * @deprecated заменить на getTypeLabel при рефакторинге
	 */
	public function getTypeText($type=null)
	{
	    return $this->getTypeLabel($type);
	}
	
	/**
	 * Получить список статусов, в которые может перейти проект
	 *
	 * @return array
	 *
	 * @deprecated после внедрения simpleWorkflow больше не используется - удалить при рефакторинге
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
	 *
	 * @param string $status
	 *
	 * @deprecated устаревшая функция - удалить при рефакторинге, убрать все использования
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
	 *
	 * @return array
	 *
	 * @deprecated всегда использовать множественное число для функций получения списков
	 */
	public function getTypeList()
	{
	    return $this->getTypesList();
	}
}