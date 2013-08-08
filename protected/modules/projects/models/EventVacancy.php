<?php

/**
 * This is the model class for table "{{event_vacancies}}".
 *
 * The followings are the available columns in table '{{event_vacancies}}':
 * @property integer $id
 * @property string $eventid
 * @property string $name
 * @property string $description
 * @property string $scopeid - id условия поиска, по которому будут искаться участники на вакансию.
 *                             Вакансия всегда содержит только один ScopeCondition внутри SearchScope
 *                             и его тип всегда 'serialized'.
 *                             Это сделано для того чтобы было легче обновлять условия поиска людей на вакансию
 * @property string $limit - количество человек в вакансии
 * @property string $timecreated
 * @property string $timemodified
 * @property string $status
 * @property string $searchdata
 * @property string $autoconfirm
 * @property string $salary
 * 
 * @property SearchScope $scope
 * @property array $filterinstances
 * 
 * @todo все прямые обращения к статусам заменить на константы
 * @todo запретить редактирование поисковых условий если вакансия - не черновик
 */
class EventVacancy extends CActiveRecord
{
    /**
     * @var string - статус вакансии: черновик. Вакансия еще только создана, не отображается участникам.
     *               Условия подбора людей на вакансию можно задавать и менять только в этом статусе.
     */
    const STATUS_DRAFT     = 'draft';
    /**
     * @var string - статус вакансии: опубликована. Вакансия полностью настроена и описана, заданы критерии
     *               подбора людей. На нее можно подавать заявки. При переходе в этот статус всем подходящим
     *               участникам проекта рассылаются приглашения.
     *               Критерии подбора людей менять нельзя.
     */
    const STATUS_ACTIVE    = 'active';
    /**
     * @var string - статус вакансии: закрыта. Или набрано необходимое количество людей, или мероприятие
     *               уже началось и подавать заявки и вписывать людей больше нельзя - мероприятие
     *               начинается с тем количеством человек, которое удалось набрать
     *               При переходе в этот статус отменяются все неподтвержденные заявки.
     *               Критерии подбора людей менять нельзя.
     */
    const STATUS_FINISHED  = 'finished';
    
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::init()
     */
    public function init()
    {
        // подключаем модели, которые понадобятся
        Yii::import('ext.ESearchScopes.models.*');
        Yii::import('application.modules.catalog.models.*');
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
	        'sections',
	    );
	    
	}
	
	/**
	 * (non-PHPdoc)
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
	}
	
	/**
	 * @see parent::behaviors
	 */
	public function behaviors()
	{
	    $searchConfig = array(
                'class' => 'ext.ESearchScopes.behaviors.ESearchScopeBehavior',
	            'idAttribute' => 'scopeid',
	            
            );
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        ),
	        'ESearchScopeBehavior' => $searchConfig,
	    );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::beforeSave()
	 * 
	 * если понадобится - можно добавить возможность создавать вакансии вообще без фильтров
	 * (но непонятно пока зачем это может быть надо)
	 */
	protected function beforeSave()
	{
	    if ( $this->isNewRecord OR ! $this->scopeid )
	    {// если группа для условий выборки анкет для вакансии еще не создана - 
	        // создадим пустую заготовку для нее
	        $this->initVacancyScope();
	    }
	    
	    return parent::beforeSave();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::afterSave()
	 */
	protected function afterSave()
	{
	    if ( ! $this->filterinstances )
	    {// к вакансии не прикреплен ни один фильтр - прикрепляем все которые по умолчанию
	        $this->attachDefaultFilters();
	    }
	    
	    parent::afterSave();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
	        'order' => 'timecreated DESC');
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, limit', 'required'),
			array('salary, autoconfirm, eventid, scopeid, timecreated, timemodified', 'length', 'max'=>11),
			array('name, description', 'length', 'max'=>255),
			array('limit', 'length', 'max'=>6),
			array('status', 'length', 'max'=>9),
		    // @todo придумать более безопасный фильтр для условий поиска людей на вакансию 
		    array('searchdata', 'safe'),
		    
			// The following rule is used by search().
			array('id, eventid, name, description, scopeid, limit, timecreated, timemodified, status', 'safe', 'on'=>'search'),
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
		    // Заявки на участие
		    'requests' => array(self::HAS_MANY, 'MemberRequest', 'vacancyid'),
		    // одобренные заявки на вакансию
		    'members' => array(self::HAS_MANY, 'ProjectMember', 'vacancyid', 
		        'condition' => "`members`.`status`='active' OR `members`.`status` ='finished'"),
		    // отклоненные заявки на вакансию
		    'rejectedmembers' => array(self::HAS_MANY, 'ProjectMember', 'vacancyid', 
		        'condition' => "status='rejected'"),
		    // доступные фильтры поиска для этой вакансии
		    'filterinstances' => array(self::HAS_MANY, 'CatalogFilterInstance', 'linkid', 
		        'condition' => "`linktype` = 'vacancy'"),
		    
		    // Статистика
		    // Количество поданых заявок
		    'requestsCount' => array(self::STAT, 'MemberRequest', 'vacancyid'),
		    // Количество подтвержденных заявок
		    'membersCount' => array(self::STAT, 'ProjectMember', 'vacancyid', 
		        'condition' => "status='active' OR status ='finished'"),
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
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 * 
	 * @todo удалить, не используется
	 */
	/*public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('eventid',$this->eventid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('scopeid',$this->scopeid,true);
		$criteria->compare('limit',$this->limit,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}*/
	
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
	    $condition = '`eventid` = :eventid AND `questionaryid` = :userid';
	    $params = array(':eventid' => $eventId, ':userid' => $questionaryId);
	    return EventInvite::model()->exists($condition, $params);
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
	    
	    if ( ! $ignoreApplication AND $this->hasApplication($questionaryId) )
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
	 * @param array $statuses - статусы заявки. Можно выборочно учитывать только ожидающие (draft), 
	 *                           подтвержденные (active), отклоненные (rejected) или завершенные (finished) заявки
	 *                           А также любые комбинации этих статусов
	 * @return boolean
	 */
	public function hasApplication($questionaryId=null, $statuses=null)
	{
	    if ( ! $questionaryId )
	    {// id анкеты не указан - попробуем взять текущий
	        $questionaryId = $this->getCurrentUserQuestionaryId();
	    }
	    $criteria = new CDbCriteria();
	    $criteria->addCondition('`memberid` = :memberid');
	    $criteria->addCondition('`vacancyid` = :vacancyid');
	    $criteria->params = array(':memberid' => $questionaryId, ':vacancyid' => $this->id);
	    
	    if ( is_array($statuses) AND ! empty($statuses) )
	    {
	        $criteria->addInCondition('status', $statuses);
	    }
	    
	    return ProjectMember::model()->exists($criteria);
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
	    if ( $questionary->status != 'active' )
	    {// быстро отсекаем тех, у кого не подтверждена анкета
	        return false;
	    }
	    
	    // Получаем полные условия соответствия вакансии
	    $criteria = $this->scope->getCombinedCriteria();
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
	 * @todo перенести эту функцию в другую библиотеку
	 */
	protected function getCurrentUserQuestionaryId()
	{
	    if ( Yii::app()->getModule('user')->user() )
	    {
	        $questionaryId = Yii::app()->getModule('user')->user()->questionary->id;
	    }else
	    {// что-то не так с учетной записью
	        // @todo записать ошибку в лог
	        return 0;
	    }
	}
	
	/**
	 * Создать пустое условие выбора участников на вакансию.
	 * Используется для новых, только что созданных вакансий.
	 * Условие создается не полностью пустым - изначально в него добавляется правило 
	 * "искать только анкеты в активном статусе"
	 * 
	 * @throws CDbException
	 * @return int - id группы условий (SearchScope) которая содержит один пустой критерий выборки анкет
	 * 
	 * @todo предусмотреть возможность отключать изначальное содержание CDbCriteria
	 * @todo если понадобится - сделать настройку "добавлять/не добавлять префикс 't' к полю status"
	 */
	protected function initVacancyScope($saveData=false)
	{
	    // создаем группу для условий поиска
	    $scope = new SearchScope;
	    $scope->name      = $this->name;
	    // @todo убрать запись shortname если оно так и не пригодится
	    //$scope->shortname = 'vacancy'.time();
	    $scope->modelid   = SearchScope::QMODEL_ID;
	    $scope->type      = 'vacancy';
	    $scope->save();
	    
	    // создаем основу для критерия выборки
	    $criteria = $this->createStartCriteria();
	    
	    // создаем само условие
	    $condition = new ScopeCondition();
	    $condition->scopeid = $scope->id;
	    $condition->type    = 'serialized';
	    $condition->value   = serialize($criteria);
	    $condition->combine = 'and';
	    $condition->save();
	    
	    $this->scopeid = $scope->id;
	    
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
	 * 
	 * @todo проверить вариант с compare
	 */
	protected function createStartCriteria()
	{
	    // (по умолчанию - берем только анкеты в активном статусе)
	    $criteria = new CDbCriteria();
	    //$criteria->addCondition("`t`.`status` = '".self::STATUS_ACTIVE."'");
	    $criteria->compare('status', self::STATUS_ACTIVE);
	    
	    return $criteria;
	}
	
	/**
	 * Создать условие поиска (CDbCriteria) по данным из фильтров, прикрепленных к вакансии
	 * По этому условию определяется, подходит участник на вакансию или нет
	 * Условие никогда не создается полностью пустым - изначально в него всегда добавляется правило 
	 * "искать только анкеты в активном статусе"
	 * 
	 * @param array $data - данные из поисковых фильтров (формы поиска)
	 * @return CDbCriteria
	 * 
	 * @todo предусмотреть возможность отключать изначальное содержание CDbCriteria
	 * @todo если понадобится - сделать настройку "добавлять/не добавлять префикс 't' к полю status"
	 */
	protected function createSearchCriteria($data)
	{
	    if ( ! $this->id )
	    {// на всякий случай проверим, что создаем условие поиска для уже существующей вакансии
	        throw new CDbException('Cannot create new SearchCriteria for non-saved vacancy');
	    }
	    // указываем путь к классу, который занимается сборкой поискового запроса из отдельных частей
	    $pathToAssembler = 'application.modules.catalog.extensions.search.handlers.QSearchCriteriaAssembler';
	    // создаем основу для критерия выборки
	    $startCriteria = $this->createStartCriteria();
	    
	    // Указываем параметры для сборки запроса
	    $config = array(
	        'class'   => $pathToAssembler,
	        'data'    => $data,
	        'startCriteria'   => $startCriteria,
	        'filterInstances' => $this->filterinstances,
	        'saveTo' => 'db',
	    );
	    
	    // создаем компонет-сборщик запроса. Он соберет CDbCriteria из отдельных данных формы поиска 
	    $assembler = Yii::createComponent($config);
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
	    if ( $this->isNewRecord OR ! $this->scope )
	    {// условие еще не создано
	        return false;
	    }
	    
	    return $this->scope->getCombinedCriteria();
	}
	
	/**
	 * Это функция-заглушка, которая присоединяет к каждой созданной вакансии стандартный набор
	 * поисковых фильтров
	 * 
	 * @return null
	 * 
	 * @todo позже наменить на интерфейс, позволяющий выбирать фильтры вручную при создании
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
	 * Добавить фильтр к форме отбора людей в вакансии
	 * 
	 * @param string $shortName - короткое название фильтра
	 * @param int $num - порядковый номер фильтра в форме
	 * 
	 * @throws CDbException
	 * @return null
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
	 * 
	 * @return null
	 */
	protected function getSearchData()
	{
	    return unserialize($this->searchdata);
	}
	
	/**
	 * Удалить поисковые даннык одного фильтра из формы поиска людей для вакансии
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