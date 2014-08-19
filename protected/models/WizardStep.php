<?php

/**
 * This is the model class for table "{{wizard_steps}}".
 *
 * The followings are the available columns in table '{{wizard_steps}}':
 * @property integer $id
 * @property string $name
 * @property string $header
 * @property string $description
 * @property string $prevlabel
 * @property string $nextlabel
 * @property string $timecreated
 * @property string $timemodified
 */
class WizardStep extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{wizard_steps}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name, prevlabel, nextlabel', 'length', 'max'=>128),
			array('header', 'length', 'max'=>255),
			array('description', 'length', 'max'=>4095),
			array('timecreated, timemodified', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, header, description, prevlabel, nextlabel, timecreated, timemodified', 'safe', 'on'=>'search'),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'instances' => array(self::HAS_MANY, 'WizardStepInstance', 'wizardstepid'),
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
                'updateAttribute' => 'timemodified',
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
			'name' => 'Название',
			'header' => 'Заголовок',
			'description' => 'Описание',
			'prevlabel' => 'Prevlabel',
			'nextlabel' => 'Nextlabel',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('header',$this->header,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('prevlabel',$this->prevlabel,true);
		$criteria->compare('nextlabel',$this->nextlabel,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WizardStep the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Именованная группа условий: получить все записи связанные с ролью
	 * @param int $id
	 * @return WizardStep
	 */
	public function forVacancy($id)
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forVacancy' => array($id),
	            ),
	        ),
	    );
	    $criteria->together = true;
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все записи, связанные с определенным объектом
	 * @param string $objectType
	 * @param string $objectId
	 * @return WizardStep
	 */
	public function forObject($objectType, $objectId)
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forObject' => array($objectType, $objectId),
	            ),
	        ),
	    );
	    $criteria->together = true;
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
	
	/**
	 * Именованая группа условий: получить все записи, связанные с несколькими объектами одного типа
	 * @param string $objectType
	 * @param string $objectIds
	 * @return WizardStep
	 */
	public function forObjects($objectType, $objectIds)
	{
	    $criteria = new CDbCriteria();
	    $criteria->with = array(
	        'instances' => array(
	            'select'   => false,
	            'joinType' => 'INNER JOIN',
	            'scopes'   => array(
	                'forObjects' => array($objectType, $objectIds),
	            ),
	        ),
	    );
	    $criteria->together = true;
	
	    $this->getDbCriteria()->mergeWith($criteria);
	    return $this;
	}
}
