<?php

/**
 * This is the model class for table "{{wizard_step_instances}}".
 *
 * The followings are the available columns in table '{{wizard_step_instances}}':
 * @property integer $id
 * @property string $wizardstepid
 * @property string $objecttype
 * @property string $objectid
 * @property string $timecreated
 * @property string $sortorder
 * 
 * Relations:
 * @property WizardStep $step
 */
class WizardStepInstance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{wizard_step_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('objecttype', 'required'),
			array('wizardstepid, objectid, timecreated, sortorder', 'length', 'max' => 11),
			array('objecttype', 'length', 'max' => 50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, wizardstepid, objecttype, objectid, timecreated, sortorder', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'step' => array(self::BELONGS_TO, 'WizardStep', 'wizardstepid'),
		);
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class'           => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	        ),
	    );
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {// автоматически назначаем порядок сортировки для новых записей, добавляя их в конец списка
	        // @todo вынести в behavior
	        $lastItemCount = $this->forObject($this->objecttype, $this->objectid)->count();
	        $lastItemCount++;
	        $this->sortorder = $lastItemCount;
	    }
	    return parent::beforeSave();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'wizardstepid' => 'Wizardstepid',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
			'timecreated' => 'Timecreated',
			'sortorder' => 'Sortorder',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('wizardstepid',$this->wizardstepid,true);
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectid',$this->objectid,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('sortorder',$this->sortorder,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WizardStepInstance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Получить название вкладки
	 * @return string
	 */
	public function getName()
	{
	    return $this->step->name;
	}
	
	/**
	 * Получить название вкладки
	 * @return string
	 */
	public function getHeader()
	{
	    return $this->step->header;
	}
	
	/**
	 * Получить список полей, присутствующих в этом разделе (массив записей)
	 * 
	 * @return void
	 */
	public function getFields()
	{
	    $result = array();
	    // поля анкеты
	    $userFields  = QFieldInstance::model()->forObject('wizardstepinstance', $this->id)->findAll();
	    foreach ( $userFields as $userField )
	    {
	        $result[] = $userField;
	    }
	    // поля заявки
	    $extraFields = ExtraFieldInstance::model()->forObject('wizardstepinstance', $this->id)->findAll();
	    foreach ( $extraFields as $extraField )
	    {
	        $result[] = $extraField;
	    }
	    return $result;
	}
	
	/**
	 * Получить список полей, присутствующих в этом разделе (для отображения)
	 * @return string
	 */
	public function getFieldList()
	{
	    $result = array();
	    // поля анкеты
	    $userFields  = QFieldInstance::model()->forObject('wizardstepinstance', $this->id)->findAll();
	    foreach ( $userFields as $userField )
	    {
	        $result[] = $userField->name;
	    }
	    // поля заявки
	    $extraFields = ExtraFieldInstance::model()->forObject('wizardstepinstance', $this->id)->findAll();
	    foreach ( $extraFields as $extraField )
	    {
	        $result[] = $extraField->name;
	    }
	    return implode('<br> ', $result);
	}
	
	/**
	 * Именованая группа условий: получить все записи, связанные с определенным объектом
	 * @param string $objectType
	 * @param int $objectId
	 * @return WizardStepInstance
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', $objectType);
	    $criteria->compare('objectid', $objectId);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все записи, связанные с несколькими объектами одного типа
	 * @param string $objectType
	 * @param array $objectIds
	 * @return WizardStepInstance
	 */
	public function forObjects($objectType, $objectIds)
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare('objecttype', $objectType);
	    $criteria->addInCondition('objectid', $objectIds);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все записи, прикрепленные к роли 
	 * @param int $id
	 * @return WizardStepInstance
	 */
	public function forVacancy($id)
	{
	    return $this->forObject('vacancy', $id);
	}
}
