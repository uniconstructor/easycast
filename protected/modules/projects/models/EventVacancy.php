<?php

/**
 * Роли (вакансии) для мероприятия
 *
 * Таблица '{{event_vacancies}}':
 * @property integer $id
 * @property string $eventid
 * @property string $name
 * @property string $description
 * @property string $scopeid - id условия поиска, по которому будут искаться участники на вакансию.
 *                             Вакансия всегда содержит только один ScopeCondition внутри SearchScope
 *                             и его тип всегда 'serialized'.
 *                             Это сделано для того чтобы было легче обновлять условия поиска людей на вакансию
 * @property string $limit - требуемое количество человек, утвержденных на эту роль
 * @property string $timecreated
 * @property string $timemodified
 * @property string $status
 * @property string $searchdata
 * @property string $autoconfirm
 * @property string $salary
 * @property string $timestart
 * @property string $timeend
 * 
 * Relations:
 * @property SearchScope $scope
 * @property CatalogFilter[] $searchFilters - список используемых фильтров поиска
 * @property ProjectEvent $event
 * @property ExtraField[] $extraFields - список дополнительных полей при подаче заявки
 * @property QUserField[] $userFields - список обязательных полей анкеты при подаче заявки
 * @property Category[] $extraFieldCategories - используемые категории доп. полей
 * @property Category[] $sectionCategories - используемые категории разделов отбора заявок
 * @property catalogSectionInstance[] $catalogSectionInstances - 
 * @property catalogSection[] $catalogSections - 
 * 
 * @todo все прямые обращения к статусам заменить на константы
 * @todo запретить редактирование поисковых условий если вакансия - не черновик
 * @todo всегда автоматически добавлять фильтр по оплате, если в вакансии указана оплата.
 *       Не давать удалять этот фильтр, пока не будет удалена сумма оплаты
 * @todo запретить редактирование списка поисковых фильтров если вакансия - не черновик
 * @todo миграция, заменяющая все старые критерии поиска: заменить sections на iconlist
 * @todo сделать количество человек необязательным полем для кастингов
 */
class EventVacancy extends CActiveRecord
{
    /**
     * @var string - статус вакансии: черновик. Вакансия еще только создана, не отображается участникам.
     *               Условия подбора людей на вакансию можно задавать и менять только в этом статусе.
     */
    const STATUS_DRAFT    = 'draft';
    /**
     * @var string - статус вакансии: опубликована. Вакансия полностью настроена и описана, заданы критерии
     *               подбора людей. На нее можно подавать заявки. При переходе в этот статус всем подходящим
     *               участникам проекта рассылаются приглашения.
     *               Критерии подбора людей менять нельзя.
     */
    const STATUS_ACTIVE   = 'active';
    /**
     * @var string - статус вакансии: закрыта. Или набрано необходимое количество людей, или мероприятие
     *               уже началось и подавать заявки и вписывать людей больше нельзя - мероприятие
     *               начинается с тем количеством человек, которое удалось набрать
     *               При переходе в этот статус отменяются все неподтвержденные заявки.
     *               Критерии подбора людей менять нельзя.
     */
    const STATUS_FINISHED = 'finished';
    
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        // подключаем модели, которые понадобятся
        Yii::import('ext.ESearchScopes.models.*');
        Yii::import('application.modules.catalog.models.*');
        Yii::import('application.modules.catalog.CatalogModule');
        Yii::import('application.modules.questionary.models.QFieldInstance');
        Yii::import('application.modules.questionary.models.QUserField');
        
        parent::init();
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EventVacancy the static model class
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
		return '{{event_vacancies}}';
	}
	
	/**
	 * Получить список фильтров поиска, которые предлагаются по умолчанию при создании каждой анкеты 
	 * 
	 * @return array
	 * 
	 * @todo вынести в настройку
	 */
	protected function getDefaultFilters()
	{
	    return array(
	        'gender',
	        'age',
	        'playage',
	        'actoruniversities',
	        'physiquetype',
	        'looktype',
	        'nativecountryid',
	        'height',
	        'weight',
	        'body',
	        'haircolor',
	        'hairlength',
	        'eyecolor',
	        'wearsize',
	        'shoessize',
	        'titsize',
	        'tatoo',
	        'dancer',
	        'sporttype',
	        'extremaltype',
	        'voicetype',
	        'voicetimbre',
	        'musicuniversities',
	        'instrument',
	        'language',
	        'striptease',
	        'driver',
	        'name',
	        'sections', // @todo удалить
	        'salary',
	        'system',
	        'email',
	        'iconlist',
	        'status',
	    );
	}
	
	/**
	 * Получить список полей, без которыз нельзя подать ни одну заявку при регистрации
	 *
	 * @return QUserField[]
	 *
	 * @todo вынести в настройку
	 */
	protected function getDefaultRegistrationFields()
	{
	    $names = array(
	        'email',
	        'firstname',
	        'lastname',
	        'gender',
	        'birthdate',
	        'mobilephone',
	        'photo',
	        'policyagreed',
	    );
	    $criteria = new CDbCriteria();
	    $criteria->addInCondition('name', $names);
	    $criteria->index = 'name';
	    
	    return QUserField::model()->findAll($criteria);
	}
	
	/**
	 * @see CActiveRecord::beforeDelete()
	 */
	protected function beforeDelete()
	{
	    // При вакансии удаляем всех соискателей и участников этой вакансии
	    $members = ProjectMember::model()->findAll('vacancyid = '.$this->id);
	    foreach ( $members as $member )
	    {
	        $member->delete();
	    }
	    // удаляем ссылки фильтров поиска на эту роль
	    foreach ( $this->filterinstances as $instance )
	    {
	        $instance->delete();
	    }
	    // удаляем условия отбора людей на эту вакансию
	    if ( $this->scope )
	    {
	        $this->scope->delete();
	    }
	    // @todo удаляем все связи обязательных и дополнительных полей с этой ролью (но не значения доп. полей)
        /*foreach ( $this->userFields as $field )
        {
            QFieldInstance::model()->forField($field->id)->attachedTo('vacancy', $this->id)->deleteAll();
        }*/
	    
	    return parent::beforeDelete();
	}
	
	/**
	 * @see parent::behaviors
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        ),
	        // работа с сохраненными критериями поиска
	        'ESearchScopeBehavior' => array(
                'class' => 'ext.ESearchScopes.behaviors.ESearchScopeBehavior',
	            'idAttribute' => 'scopeid',
            ),
	    );
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 * 
	 * если понадобится - можно добавить возможность создавать вакансии вообще без фильтров
	 * (но непонятно пока зачем это может быть надо)
	 */
	protected function beforeSave()
	{
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::afterSave()
	 */
	protected function afterSave()
	{
	    if ( $this->isNewRecord )
	    {// для каждой роли при создании задается обязательный минимум заполняемых при регистрации полей
	        // который потом можно изменить
	        $this->attachRequiredFields();
	    }
	    if ( ! $this->filterinstances )
	    {// к вакансии не прикреплен ни один фильтр - прикрепляем все которые по умолчанию
	        $this->attachDefaultFilters();
	    }
	    if ( ! $this->isNewRecord AND ! $this->scopeid )
	    {// условия для выборки анкет для вакансии еще не созданы - создадим условия по умолчанию
	        $this->initVacancyScope();
	    }
	    parent::afterSave();
	}
	
	/**
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
	        'order' => '`timecreated` DESC',
	    );
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, limit', 'required'),
			array('salary, autoconfirm, eventid, scopeid, timecreated, timemodified, timestart, timeend', 'length', 'max' => 11),
			array('name', 'length', 'max' => 255),
			array('description', 'length', 'max' => 4095),
			array('limit', 'length', 'max' => 6),
			array('status', 'length', 'max' => 9),
		    // @todo придумать более безопасный фильтр для условий поиска людей на вакансию 
		    array('searchdata', 'safe'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // мероприятие на которое создана вакансия
		    'event' => array(self::BELONGS_TO, 'ProjectEvent', 'eventid'),
		    // критерий поиска, по которому выбираются подходящие на вакансию участники 
		    'scope' => array(self::BELONGS_TO, 'SearchScope', 'scopeid'),
		    
		    
		    // дополнительные поля, необходимые для подачи заявки на эту роль
		    'extraFields' => array(self::MANY_MANY, 'ExtraField', "{{extra_field_instances}}(objectid, fieldid)",
		        'condition' => "`objecttype` = 'vacancy'",
		    ),
		    // обязательные поля анкеты, необходимые для подачи заявки на эту роль
		    'userFields' => array(self::MANY_MANY, 'QUserField', "{{q_field_instances}}(objectid, fieldid)",
		        'condition' => "`objecttype` = 'vacancy'",
		    ),
		    // доступные фильтры поиска для этой вакансии
		    'searchFilters' => array(self::MANY_MANY, 'CatalogFilter', "{{catalog_filter_instances}}(linkid, filterid)",
		        'condition' => "`linktype` = 'vacancy'",
		    ),
		    // разделы для отбора заявок
		    'catalogSections' => array(self::MANY_MANY, 'CatalogSection', "{{catalog_section_instances}}(objectid, sectionid)",
		        'condition' => "`objecttype` = 'vacancy'",
		    ),
		    // разделы для отбора заявок
		    'catalogSectionInstances' => array(self::HAS_MANY, 'CatalogSectionInstance', "objectid",
		        'index'     => 'id',
		        'condition' => "`objecttype` = 'vacancy'",
		    ),
		    
		    
		    // Группы дополнительных полей, используемых в этой роли
		    // @todo искоренить антипаттерн "magic numbers": убрать из условий конкретный parentid
		    //       есть два возможных решения:
		    //       - включить условие по scopes и убрать parentid
		    //       - вынести parentid, содержащие списки разделов и наборы полей в настройку
		    'extraFieldCategories' => array(self::MANY_MANY, 'Category', "{{category_instances}}(objectid, categoryid)", 
                'condition' => "`objecttype` = 'vacancy' AND `parentid` = 5",
		    //    'scopes'    => array('withType' => array('extrafields')),
		    ),
		    // Разделы вкладок для заявок участников, используемых в этой роли
		    // @todo искоренить антипаттерн "magic numbers": убрать из условий конкретный parentid
		    //       есть два возможных решения:
		    //       - включить условие по scopes и убрать parentid
		    //       - вынести parentid, содержащие списки разделов и наборы полей в настройку
		    'sectionCategories' => array(self::MANY_MANY, 'Category', "{{category_instances}}(objectid, categoryid)",
                'condition' => "`objecttype` = 'vacancy'  AND `parentid` = 4",
            //    'scopes'    => array('withType' => array('sections')),
		    ),
		    
		    
		    // Заявки на участие
		    // @todo переписать через именованные группы условий
		    'requests' => array(self::HAS_MANY, 'MemberRequest', 'vacancyid'),
		    // одобренные заявки на вакансию
		    // @todo переписать через именованные группы условий
		    // @deprecated - переписано: используется функция members(), связь оставлена для совместимости
		    //               со старым кодом, удалить ее при рефакторинге
		    'members' => array(self::HAS_MANY, 'ProjectMember', 'vacancyid',
		        'condition' => "`members`.`status` = 'active' OR `members`.`status` = 'finished'",
		    ),
		    // отклоненные заявки на вакансию
		    // @deprecated - переписано: используется функция members(), связь оставлена для совместимости
		    //               со старым кодом, удалить ее при рефакторинге
		    // @todo переписать через именованные группы условий, удалить при рефакторинге
		    'rejectedmembers' => array(self::HAS_MANY, 'ProjectMember', 'vacancyid',
		        'condition' => "`status` = 'rejected'",
		    ),
	        // ссылки на доступные фильтры поиска для этой вакансии
		    // @deprecated - использовалось пока я не умел писать связи типа "мост"
		    // @todo удалить при рефакторинге, вместо нее использовать связь searchFilters
		    'filterinstances' => array(self::HAS_MANY, 'CatalogFilterInstance', 'linkid',
		        'condition' => "`linktype` = 'vacancy'",
		    ),
		    
		    
		    // Статистика
		    // Количество поданых заявок
		    // @todo переписать через именованные группы условий
		    'requestsCount' => array(self::STAT, 'MemberRequest', 'vacancyid'),
		    // Количество подтвержденных заявок
		    // @todo переписать через именованные группы условий
		    'membersCount' => array(self::STAT, 'ProjectMember', 'vacancyid',
		        'condition' => "`status` = 'active' OR `status` = 'finished'",
		    ),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'eventid' => ProjectsModule::t('еvent'),
			'name' => ProjectsModule::t('name'),
			'description' => ProjectsModule::t('description'),
			'scopeid' => ProjectsModule::t('vacancy_scopeid'),
			'limit' => ProjectsModule::t('vacancy_limit'),
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'status' => ProjectsModule::t('status'),
			'autoconfirm' => 'Автоматически одобрять все заявки',
			'salary' => 'Размер оплаты за съемочный день',
			'timestart' => 'Время начала',
			'timeend' => 'Время окончания',
		);
	}
	
	/**
	 * Подсчитать количество участников, которые подходят по условиям вакансии (потенциальных соискателей)
	 * 
	 * @param bool $excludeApproved - исключить из выборки тех, кто уже утвержден на эту вакансию
	 * @return int
	 * 
	 * @todo исключить из выборки тех, кто уже утвержден на эту вакансию
	 */
	public function countPotentialApplicants($excludeApproved=false)
	{
	    $criteria = $this->getSearchCriteria();
	    return Questionary::model()->count($criteria);
	}
	
	/**
	 * Разослать приглашения всем подходящим пользователям
	 * @return bool
	 * 
	 * @todo придумать что делать со списком вакансий в приглашении
	 */
	public function sendInvites()
	{
	    // получаем условия выборки для этой вакансии
	    $criteria = $this->getSearchCriteria();
	    // @todo заменить использование псевдонима "t" на имя/alias таблицы
	    $criteria->select = '`t`.`id`';
	    
	    $users = Questionary::model()->findAll($criteria);
	    foreach ( $users as $user )
	    {// перебираем всех подходящих пользователей
	        if ( $this->isInvited($user->id, $this->event->id) )
	        {// участник уже приглашен на мероприятие - идем дальше
	            continue;
	        }
	        // Участник еще не приглашен - высылаем приглашение
	        $invite = new EventInvite;
	        $invite->questionaryid = $user->id;
	        $invite->eventid       = $this->event->id;
	        $invite->save();
	    }
	    
	    return true;
	}
	
	/**
	 * Проверить, приглашен ли пользователь на мероприятие
	 * @param int $questionaryId - id анкеты участника в таблице questionary
	 * @param int $eventId - id события, на которое мы собираемся пригласить участника
	 * @return boolean
	 * 
	 * @todo перенести в класс ProjectEvent
	 */
	protected function isInvited($questionaryId, $eventId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('eventid', $eventId);
	    $criteria->compare('questionaryid', $questionaryId);
	    if ( EventInvite::model()->exists($criteria) )
	    {// приглашение на это мкроприятие уже отправлено
	        return true;
	    }
	    if ( $this->hasApplication($questionaryId) )
	    {// записан на роль админом когда она была еще черновиком
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Может ли пользователь претендовать на эту вакансию?
	 * @param int $questionaryId - id анкеты пользователя в таблице questionary
	 * @param bool $ignoreApplication - считать вакансию доступной, даже если на нее уже подана заявка
	 * @return bool
	 * 
	 * @todo добавить сравнение по критерию
	 */
	public function isAvailableForUser($questionaryId=null, $ignoreApplication=false)
	{
	    if ( ! $questionaryId )
	    {// id анкеты не указан - попробуем взять текущий
	        $questionaryId = $this->getCurrentUserQuestionaryId();
	    }
	    
	    if ( $this->hasApplication($questionaryId) AND ! $ignoreApplication )
	    {// участник уже подал заявку на эту вакансию
	        return false;
	    }
	    
	    if ( ! $this->userMatchVacancyConditions($questionaryId) )
	    {// участник не подходит под указанные в вакансии критерии
	        return false;
	    }
	    
	    return true;
	}
	
	/**
	 * Определить, посылал ли уже участник заявку на эту вакансию
	 * @param int $questionaryId - id анкеты пользователя в таблице questionary
	 * @param array $statuses - статусы заявки: можно выборочно учитывать
	 *                           - только ожидающие (draft), 
	 *                           - подтвержденные (active), 
	 *                           - отклоненные (rejected) 
	 *                           - или завершенные (finished) заявки
	 *                           А также любые комбинации этих статусов
	 * @return boolean
	 */
	public function hasApplication($questionaryId=null, $statuses=null)
	{
	    if ( ! $questionaryId )
	    {// id анкеты не указан - попробуем взять текущий
	        if ( ! $questionaryId = Yii::app()->getModule('questionary')->getCurrentQuestionaryId() )
	        {
	            return false;
	        }
	    }
	    $criteria = new CDbCriteria();
	    $criteria->compare('memberid', $questionaryId);
	    $criteria->compare('vacancyid', $this->id);
	    
	    if ( is_array($statuses) AND ! empty($statuses) )
	    {
	        $criteria->addInCondition('status', $statuses);
	    }
	    
	    return ProjectMember::model()->exists($criteria);
	}
	
	/**
	 * Определить, является ли переданный пользователь утвержденным кандидатом на эту роль
	 * @param int $questionaryId - id анкеты участника
	 * @param array $statuses
	 * @return bool
	 */
	public function hasMember($questionaryId=null, $statuses=array())
	{
	    if ( ! $questionaryId )
	    {// id анкеты не указан - попробуем взять текущий
	        if ( ! $questionaryId = Yii::app()->getModule('questionary')->getCurrentQuestionaryId() )
	        {
	            return false;
	        }
	    }
	    if ( ! $statuses )
	    {
	        $statuses = array(
	            ProjectMember::STATUS_ACTIVE,
	            ProjectMember::STATUS_FINISHED,
	            ProjectMember::STATUS_SUCCEED,
            );
	    }
	    return $this->hasApplication($questionaryId, $statuses);
	}
	
	/**
	 * Получить пользователя, который подал заявку, но пока еще не был распределен 
	 * ни в одну из категорий внутри роли, 
	 * @todo и которого в данный момент не редактируют другие пользователи
	 * 
	 * @param array $statuses
	 * @return ProjectMember|null
	 */
	public function getUnallocatedMember($statuses=array())
	{
	    if ( $this->status != self::STATUS_ACTIVE )
	    {// на роль или еще не начат или уже закончен отбор
	        return null;
	    }
	    if ( empty($statuses) )
	    {// если статус заявок не указан - возьмем все кроме отклоненных
	        $statuses = array(
	            ProjectMember::STATUS_DRAFT,
	            ProjectMember::STATUS_PENDING,
	            ProjectMember::STATUS_ACTIVE,
	        );
	    }
	    
	    // получаем всех участников роли
	    $members = ProjectMember::model()->forVacancy($this->id)->withStatus($statuses)->notLocked()->
	       findAll(array('order' => "`t`.`timecreated` DESC"));
	    // получаем все разделы заявок
	    $csids = array_keys($this->catalogSectionInstances);
	    foreach ( $members as $member )
	    {/* @var $member ProjectMember */
	        if ( $member->forSectionInstances($csids)->findByPk($member->id) )
	        {// заявка есть хотя бы в одном разделе - она уже обработана
                continue;
	        }else
	        {// заявка не записана ни в один раздел
	            return $member;
	        }
	    }
	    return null;
	}
	
	/**
	 * 
	 * @param unknown $statuses
	 * @return void
	 * 
	 * @todo переписать, убрать дублирование кода
	 */
	public function countUnallocatedMembers($statuses=array())
	{
	    if ( $this->status != self::STATUS_ACTIVE )
	    {// на роль или еще не начат или уже закончен отбор
	       return null;
	    }
	    if ( empty($statuses) )
	    {// если статус заявок не указан - возьмем все кроме отклоненных
    	    $statuses = array(
    	        ProjectMember::STATUS_DRAFT,
    	        ProjectMember::STATUS_PENDING,
    	        ProjectMember::STATUS_ACTIVE,
    	    );
	    }
	    $count = 0;

	    
	    // получаем всех участников роли
	    $members = ProjectMember::model()->forVacancy($this->id)->withStatus($statuses)->notLocked()->
	       findAll(array('select' => "`t`.`id`"));
	    // получаем все разделы заявок
	    $csids = array_keys($this->catalogSectionInstances);
	    
	    foreach ( $members as $member )
	    {/* @var $member ProjectMember */
    	    if ( $member->forSectionInstances($csids)->findByPk($member->id) )
    	    {// заявка есть хотя бы в одном разделе - она уже обработана
    	       continue;
    	    }else
    	    {// заявка не записана ни в один раздел
    	       $count++;
    	    }
	    }
	    
	    return $count;
	}
	
	/**
	 * Определить, нужно ли пользователю указать доп. данные прежде чем подать заявку на эту роль
	 * @param Questionary $questionary
	 * @return void
	 */
	public function needMoreDataFromUser(Questionary $questionary)
	{
	    if ( ! $questionary->id )
	    {
	        return true;
	    }
	    foreach ( $this->userFields as $userField )
	    {/* @var $userField QUserField */
    	    if ( $userField->isRequiredFor('vacancy', $this->id) AND $userField->isEmptyIn($questionary) )
    	    {// как минимум одно поле анкеты требует заполнения
    	        return true;
    	    }
	    }
	    foreach ( $this->extraFields as $extraField )
	    {/* @var $extraField ExtraField */
    	    if ( $extraField->isRequiredFor('vacancy', $this->id) AND 
                 $extraField->isEmptyForVacancy($this, $questionary) )
    	    {// как минимум одно дополнительное поле требует заполнения
    	       return true;
    	    }
	    }
	    return false;
	}
	
	/**
	 * Определить, подходит ли участник по условиям, указанным в вакансии
	 * 
	 * @param int $questionaryId - id анкеты пользователя 
	 * @return bool
	 * 
	 * @todo проверить вариант с compare
	 */
	protected function userMatchVacancyConditions($questionaryId)
	{
	    if ( ! $questionary = Questionary::model()->findByPk($questionaryId) )
	    {// нет анкеты с таким id
	        // @todo записать ошибку в лог
	        return false;
	    }
	    if ( ! $questionary->isAdmitted() )
	    {// быстро отсекаем тех, у кого не подтверждена анкета
	        return false;
	    }
	    
	    // Получаем полные условия соответствия вакансии
	    //$criteria = $this->scope->getCombinedCriteria();
	    
	    $criteria = $this->getSearchCriteria();
	    // сужаем их до единственного человека
	    $criteria->addCondition('`t`.`id` = '.$questionary->id);
	    // и в итоге просто проверяем существование такой записи
	    return Questionary::model()->exists($criteria);
	}
	
	/**
	 * Получить список статусов, в которые может перейти объект
	 * @todo добавить статус "suspended"
	 * @return array
	 */
	public function getAllowedStatuses()
	{
	    switch ( $this->status )
	    {
	        case 'draft':
	            if ( $this->event->status == 'active' )
	            {
	                return array('active');
	            }
            break;
	        case 'active':
                return array('finished');
            break;
            case 'finished':
                if ( in_array($this->event->status, array('active', 'draft')) )
                {
                    return array('active');
                }
            break;
	    }
	
	    return array();
	}
	
	/**
	 * Получить статус объекта для отображения пользователю
	 * @param string $status
	 */
	public function getStatustext($status=null)
	{
	    if ( ! $status )
	    {
	        $status = $this->status;
	    }
	    return ProjectsModule::t('vacancy_status_'.$status);
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
	    
	    if ( $newStatus == self::STATUS_ACTIVE )
	    {// если вакансия активируется - оповестим всех подходящих участников
	        // о том, что она открыта
	        $this->sendInvites();
	    }
	
	    return true;
	}
	
	/**
	 * Определить id анкеты текущего пользователя (если он участник)
	 * 
	 * @return int
	 * 
	 * @deprecated функция перенесена в модуль Questionary
	 *             правило вызова: Yii::app()->getModule('questionary')->getCurrentQuestionaryId();
	 */
	protected function getCurrentUserQuestionaryId()
	{
	    if ( Yii::app()->getModule('user')->user() )
	    {
	        return Yii::app()->getModule('user')->user()->questionary->id;
	    }else
	    {// что-то не так с учетной записью
	        // @todo записать ошибку в лог
	        return 0;
	    }
	}
	
	/**
	 * Создать стандартную заготовку условия выбора участников на вакансию.
	 * Используется для новых, только что созданных вакансий.
	 * Условие создается не полностью пустым - изначально в него добавляется правило 
	 * "искать только анкеты в активном статусе" и несколько других условий, в зависимости от 
	 * данных с которыми создана роль
	 * Создает объект SearchScope с серавлизованным критерием выборки на борту и JSON-массив для формы поиска людей
	 * на вакансию
	 * 
	 * @throws CDbException
	 * @return int - id группы условий (SearchScope) которая содержит один пустой критерий выборки анкет
	 * 
	 * @todo предусмотреть возможность отключать изначальное содержание CDbCriteria
	 * @todo если понадобится - сделать настройку "добавлять/не добавлять префикс 't' к полю status"
	 * @todo переименовать в initObjectScope для того чтобы позже можно было создать общий интерфейс для 
	 *       всех объектов использующих плагин SearchScope 
	 */
	protected function initVacancyScope($saveData=false)
	{
	    // создаем группу для условий поиска
	    $scope = new SearchScope;
	    $scope->name      = $this->name;
	    $scope->modelid   = SearchScope::QMODEL_ID;
	    $scope->type      = 'vacancy';
	    $scope->save();
	    
	    // получаем те условия поиска, которые можем сразу же создать из вакансии
	    $searchData = $this->сreateStartSearchData();
	    $criteria   = $this->createSearchCriteria($searchData);
	    
	    // создаем само условие
	    $condition = new ScopeCondition();
	    $condition->scopeid = $scope->id;
	    $condition->type    = 'serialized';
	    $condition->value   = serialize($criteria);
	    $condition->combine = 'and';
	    $condition->save();
	    
	    // сохраняем ссылку на поисковый критерий и данные формы поиска
	    $this->scopeid    = $scope->id;
	    $this->searchdata = serialize($searchData);
	    
	    if ( $saveData )
	    {// нужно сохранить модель после инициализации поисковых критериев
	        return (bool)$this->save();
	    }else
	    {// модель сохранять не нужно - это произойдет само автоматически
	        return true;
	    }
	}
	
	/**
	 * Создать базовый критерий выборки анкет для вакансии (то условие, поверх
	 * которого будут накладываться все остальные критерии поиска)
	 * 
	 * @return CDbCriteria
	 * @deprecated использовать метод сборки критериев поиска из CatalogModule
	 */
	protected function createStartCriteria()
	{
	    $criteria = new CDbCriteria();
	    
	    // (по умолчанию - берем только анкеты в активном статусе)
	    // $criteria->compare('status', 'active');
	    // @todo в качестве alias таблицы проставить не "t" а alias в модели Questionary
	    $criteria->addCondition("`t`.`status` NOT IN ('delayed', 'draft', 'unconfirmed')");
	    // сортируем анкеты по рейтингу (сначала лучшие)
	    $criteria->order = '`t`.`rating` DESC';
	    
	    return $criteria;
	}
	
	/**
	 * Автоматически создать данные для фильтров поиска анкет при создании вакансии
	 * Эта функция немного облегчает процесс добавления вакансий, сразу же устанавливая такие параметры как цена
	 * 
	 * @return array
	 * 
	 * @todo учитывать здесь пожелания актеров: хотят/не хотят сняться в рекламе/сериале/кино/эпизодической роли
	 *       когда в условиях съемок и вакансии появятся эти поля и когда мы создадим критерии поиска по ним
	 * @todo более точно округлять сумму, в зависимости от размера оплаты 
	 */
	protected function сreateStartSearchData()
	{
	    $prefix     = CatalogModule::SEARCH_FIELDS_PREFIX;
	    $filters    = $this->getDefaultFilters();
	    $searchData = array();
	    
	    if ( in_array('salary', $filters) AND $this->salary )
	    {// если в вакансии указана сумма оплаты - установим ее сразу же как поисковый критерий 
	        // (таким образом сразу же отсекаем всех кто дороже)
	        
	        // округляем сумму оплаты до 250 в меньшую сторону
	        $tail      = $this->salary % 250;
	        $maxSalary = $this->salary - $tail;
	        $searchData[$prefix.'salary'] = array('maxsalary' => $maxSalary);
	    }
	    
	    return $searchData;
	}
	
	/**
	 * Создать условие поиска (CDbCriteria) по данным из фильтров, прикрепленных к вакансии (роли)
	 * По этому условию определяется, подходит участник на вакансию или нет
	 * Условие никогда не создается полностью пустым - изначально в него всегда добавляется правило 
	 * "искать только анкеты в активном статусе" и другие критерии, в зависимости от данных создаваемой роли
	 * 
	 * @param array $data - данные из поисковых фильтров (формы поиска)
	 * @return CDbCriteria
	 * 
	 * @todo предусмотреть возможность отключать изначальное содержание CDbCriteria
	 * @todo если понадобится - сделать настройку "добавлять/не добавлять префикс 't' к полю status"
	 */
	protected function createSearchCriteria($data)
	{
	    // указываем путь к классу, который занимается сборкой поискового запроса из отдельных частей
	    $pathToAssembler = 'catalog.extensions.search.handlers.QSearchCriteriaAssembler';
	    // создаем основу для критерия выборки
	    $startCriteria = $this->createStartCriteria();
	    
	    // Указываем параметры для сборки поискового запроса по анкетам
	    $config = array(
	        'class'           => $pathToAssembler,
	        'data'            => $data,
	        'startCriteria'   => $startCriteria,
	    );
	    if ( $this->isNewRecord )
	    {// вакансия создается, она пока еще не сохранена в БД, поэтому
	        // фильтры к ней еще не добавлены, и сохранять критерий тоже некуда - зададим все руками
	        $config['filters']  = $this->getDefaultFilters();
	        $config['saveData'] = false;
	    }else
	    {// вакансия редактируется - обновляем критерий выборки
	        $config['filters'] = $this->searchFilters;
	        $config['saveTo']  = 'db';
	    }
	    
	    // создаем компонет-сборщик запроса. Он соберет CDbCriteria из отдельных данных формы поиска 
	    /* @var $assembler QSearchCriteriaAssembler */
	    $assembler = Yii::createComponent($config);
	    $assembler->init();
	    if ( ! $finalCriteria = $assembler->getCriteria() )
	    {// ни один фильтр поиска не был использован - возвращаем исходные условия
	        return $startCriteria;
	    }
	    return $finalCriteria;
	}
	
	/**
	 * Получить условия выборки подходящих анкет для этой вакансии
	 * @return CDbCriteria
	 */
	public function getSearchCriteria()
	{
	    if ( $this->isNewRecord )
	    {// условие еще не создано
	        return false;
	    }
	    return $this->createSearchCriteria($this->getSearchData());
	}
	
	/**
	 * Это функция-заглушка, которая присоединяет к каждой созданной вакансии стандартный набор
	 * поисковых фильтров
	 * 
	 * @return null
	 * 
	 * @todo позже заменить на интерфейс, позволяющий выбирать фильтры вручную при создании
	 */
	protected function attachDefaultFilters()
	{
	    $num = 1;
	    $filters = $this->getDefaultFilters();
	    
	    foreach ( $filters as $filter )
	    {
	        $this->bindFilter($filter, $num);
	        $num = $num + 1;
	    }
	}
	
	/**
	 * Добавить обязательные поля для подачи заявки сразу же после создания роли
	 * @return void
	 */
	protected function attachRequiredFields()
	{
	    $fields = $this->getDefaultRegistrationFields();
	    foreach ( $fields as $field )
	    {
	        $field->bindWith('vacancy', $this->id);
	    }
	}
	
	/**
	 * Добавить фильтр к форме отбора людей в вакансии
	 * 
	 * @param string $shortName - короткое название фильтра
	 * @param int $num - порядковый номер фильтра в форме
	 * 
	 * @throws CDbException
	 * 
	 * @todo перенести в класс CatalogFilter
	 */
	protected function bindFilter($shortName, $num)
	{
	    if ( ! $filter = CatalogFilter::model()->find('`shortname` = :shortname', array(':shortname' => $shortName)) )
	    {// находим нужный фильтр по названию
	        throw new CDbException($shortName.' not found');
	    }
	    
	    // создаем и сохраняем связку фильтра и вакансии
	    $filterInstance = new CatalogFilterInstance;
	    $filterInstance->linktype  = 'vacancy';
	    $filterInstance->linkid    = $this->id;
	    $filterInstance->filterid  = $filter->id;
	    $filterInstance->visible   = 1;
	    $filterInstance->order     = $num;
	    $filterInstance->save();
	}
	
	// Функции для работы с данными фильтров поиска
	
	/**
	 * Обновить данные о критерии выборки людей, которые подходят под эту вакансию
	 * @param array|null $newData - новые условия подбора людей на вакансию
	 * @return bool
	 *
	 * @todo обработать ситуацию, когда набор условий есть, но содержит более одного критерия
	 *       или критерий неправильного типа
	 */
	public function setSearchData($newData)
	{
	    $newDataSerialized = serialize($newData);
	    if ( $this->searchdata == $newDataSerialized )
	    {// если условия выборки не изменились - ничего не надо делать
	        return true;
	    }
	     
	    if ( ! $this->scope )
	    {// условие для этой вакансии еще не создано - исправим это
	        $this->initVacancyScope();
	    }
	     
	    // сохраняем новые данные из формы поиска в вакансию
	    $this->searchdata = $newDataSerialized;
	    $this->save();
	    
	    if ( ! $this->scope )
	    {
	        return true;
	    }
	    // обновим составленный критерий поиска (ScopeCondition)
	    // (для вакансии он всегда только один в наборе (SearchScope) и всегда является сериализованным массивом)
	    $conditions = $this->scope->scopeConditions;
	    $condition  = current($conditions);
	    // заново получаем из данных формы поиска критерии для выборки участников
	    $criteria  = $this->createSearchCriteria($newData);
	    // сериализуем и сохраняем новые критерии выборки
	    $condition->value = serialize($criteria);
	     
	    return $condition->save();
	}
	
	/**
	 * Получить данные для одного поискового фильтра
	 * ! Важно: все функции, предоставляющие данные для формы поиска должны иметь функцию getFilterSearchData
	 *          Это необходимо для совместимости
	 * 
	 * @param string $namePrefix - имя ячейки в массиве данных из формы поиска, 
	 *                             в которой лежит сохраненное значение фильтра 
	 * @return array
	 */
	public function getFilterSearchData($namePrefix)
	{
	    $searchData = $this->getSearchData();
	    if ( ! isset($searchData[$namePrefix]) )
	    {
	        return array();
	    }
	    return $searchData[$namePrefix];
	}
	
	/**
	 * Получить все сохраненные данные из формы поиска людей для вакансии
	 * @return null
	 */
	public function getSearchData()
	{
	    return unserialize($this->searchdata);
	}
	
	/**
	 * Удалить поисковые данные одного фильтра из формы поиска людей для вакансии
	 * 
	 * @param string $namePrefix - имя ячейки в массиве данных из формы поиска, 
	 *                             в которой лежит сохраненное значение фильтра 
	 * @return null
	 */
	public function clearFilterSearchData($namePrefix)
	{
	    $searchData = $this->getSearchData();
	    
	    if ( isset($searchData[$namePrefix]) )
	    {// если нужные данные есть - удаляем их и сразу же пересохраняем все оставшиеся данные, 
	        unset($searchData[$namePrefix]);
	        // а также пересчитываем и сохраняем CDbCriteria
	        $this->setSearchData($searchData);
	    }
	}
	
	/**
	 * Удалить все поисковые данные всех фильтров из формы поиска людей для вакансии
	 * 
	 * @return null
	 */
	public function clearSearchData()
	{
	    $this->setSearchData(array());
	}
}