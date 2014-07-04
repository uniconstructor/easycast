<?php

/**
 * This is the model class for table "{{member_instances}}".
 *
 * The followings are the available columns in table '{{member_instances}}':
 * @property integer $id
 * @property string $objecttype
 * @property string $objectid
 * @property string $memberid
 * @property string $comment
 * @property string $sourcetype
 * @property string $sourceid
 * @property string $timecreated
 * @property string $timemodified
 * @property string $status
 * @property string $linktype
 */
class MemberInstance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{member_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('objecttype', 'required'),
			array('objecttype, sourcetype, status, linktype', 'length', 'max' => 50),
			array('objectid, memberid, sourceid, timecreated, timemodified', 'length', 'max' => 11),
			array('comment', 'length', 'max' => 4095),
			// The following rule is used by search().
			array('id, objecttype, objectid, memberid, comment, sourcetype, sourceid, timecreated, 
			    timemodified, status', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // заявка участника на роль
		    'member' => array(self::BELONGS_TO, 'ProjectMember', 'memberid'),
		    // раздел заявки к которому прикреплена заявка 
		    'sectionInstance' =>  array(self::BELONGS_TO, 'CatalogSectionInstance', 'objectid'),
		);
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
			'objecttype' => 'Objecttype',
			'objectid' => 'Раздел',
			'memberid' => 'Memberid',
			'comment' => 'Комментарий',
			'sourcetype' => 'Sourcetype',
			'sourceid' => 'Sourceid',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'status' => 'Status',
			'linktype' => 'Добавить в раздел?',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectid',$this->objectid,true);
		$criteria->compare('memberid',$this->memberid,true);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('sourcetype',$this->sourcetype,true);
		$criteria->compare('sourceid',$this->sourceid,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MemberInstance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {
	        $criteria =  new CDbCriteria();
	        $criteria->compare('objecttype', $this->objecttype);
	        $criteria->compare('objectid', $this->objectid);
	        $criteria->compare('memberid', $this->memberid);
	        if ( $this->exists($criteria) )
	        {// одну заявку нельзя 2 раза поместить в одну и ту же категорию
	            return false;
	        }
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    return array(
	        // последние созданные записи
	        'lastCreated' => array(
    	        'order' => $this->getTableAlias(true).'.`timecreated` DESC, '.
	               $this->getTableAlias(true).'.`id` ASC',
    	    ),
	        // последние измененные записи
	        'lastModified' => array(
    	        'order' => $this->getTableAlias(true).'.`timemodified` DESC, '.
	               $this->getTableAlias(true).'.`id` ASC',
    	    ),
	    );
	}
	
	/**
	 * Именованая группа условий: получить все ссылки на заявку для определенного объекта
	 * (например все заявки внутри вкладки роли)
	 * @param string $objectType
	 * @param int    $objectId
	 * @return StatusHistory
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->compare($this->getTableAlias(true).'.`objectid`', $objectId);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все ссылки на заявку для нескольких объектов одного типа
	 * (например если нужно получить все заявки, которые находятся в нескольких вкладках роли)
	 * Примечание: здесь можно было бы применить несколько вызовов forObject и слить критерии через OR
	 *             но IN по id будет работать быстрее, потому что не нужно будет каждый раз проверять
	 *             objecttype для каждой связи
	 * @param string $objectType
	 * @param array  $objectIds - массив id объектов
	 * @return StatusHistory
	 */
	public function forObjects($objectType, $objectIds)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`objecttype`', $objectType);
	    $criteria->addInCondition($this->getTableAlias(true).'.`objectid`', $objectIds);
	
	    $this->getDbCriteria()->mergeWith($criteria);
	
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска - найти все записи принадлежащие разделу заявок
	 * @param int $instanceId  - id раздела заявок внутри роли (CatalogSectionInstance)
	 * @param string $linkType - 
	 * @return MemberInstance
	 */
	public function forSectionInstance($instanceId, $linkType='<>nolink')
	{
	    $criteria = new CDbCriteria();
	    if ( $linkType )
	    {
	        $criteria->compare('linktype', $linkType);
	    }
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this->forObject('section_instance', $instanceId);
	}
	
	/**
	 * Именованная группа условий поиска - найти все записи принадлежащие нескольким разделам заявок
	 * @param array $instanceIds - массив id разделов заявок внутри роли (CatalogSectionInstance)
	 * @return MemberInstance
	 */
	public function forSectionInstances($instanceIds, $linkType='<>nolink')
	{
	    $criteria = new CDbCriteria();
	    if ( $linkType )
	    {
	        $criteria->compare('linktype', $linkType);
	    }
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this->forObjects('section_instance', $instanceIds);
	}
	
	/**
	 * Именованная группа условий поиска - найти все записи с определенным типом связи с объектом
	 * @param array $linkTypes
	 * @return MemberInstance
	 * 
	 * @todo переписать с использованием тегов (TagInstance): сделать тип связи не отдельным полем
	 *       а id тега, чтобы можно было задать любой набор маркеров для заявок 
	 *       (сейчас только "лучшие/средние/худшие")
	 */
	public function withLinkTypes($linkTypes)
	{
	    if ( ! $linkTypes )
	    {
	        return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->addInCondition('linktype', $linkTypes);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Именованная группа условий поиска - исключить из выборки все записи с определенным типом связи
	 * @param array $linkTypes
	 * @return MemberInstance
	 * 
	 * @todo переписать с использованием тегов (TagInstance): сделать тип связи не отдельным полем
	 *       а id тега, чтобы можно было задать любой набор маркеров для заявок 
	 *       (сейчас только "лучшие/средние/худшие")
	 */
	public function withoutLinkTypes($linkTypes)
	{
	    if ( ! $linkTypes )
	    {
	        return $this;
	    }
	    $criteria = new CDbCriteria();
	    $criteria->addNotInCondition('linktype', $linkTypes);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Именованная группа условий: найти ссылки на заявки с определенным статусом заявки
	 * @param array $statuses
	 * @return MemberInstance
	 */
	public function withMemberStatus($statuses)
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'member' => array(
    	        'scopes' => array(
    	            'withStatus' => array($statuses),
    	        ),
    	    ),
	    );
	    $criteria->together = true;
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getLinkTypeOptions()
	{
	    return array(
            'nolink'  => 'Нет',
            'nograde' => 'Да [без пометки]',
            'good'    => 'Да [лучшее]',
            'normal'  => 'Да [среднее]',
            'sad'     => 'Да [хушее]',
	    );
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getLinkTypeOption()
	{
	    $options = $this->getLinkTypeOptions();
	    return $options[$this->linktype];
	}
}