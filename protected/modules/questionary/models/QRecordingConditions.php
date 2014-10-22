<?php

/**
 * Класс для работы с условиями участия актера в съемках
 *
 * Таблица '{{q_recording_conditions}}':
 * @property integer $id
 * @property string $questionaryid
 * @property string $salary
 * @property integer $wantsbusinesstrips
 * @property integer $hasforeignpassport
 * @property string $passportexpires
 * @property integer $isnightrecording
 * @property integer $istoplessrecording
 * @property integer $isfreerecording
 * @property string $custom
 */
class QRecordingConditions extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return QRecordingConditions the static model class
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
		return '{{q_recording_conditions}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('wantsbusinesstrips, hasforeignpassport, isnightrecording, istoplessrecording, isfreerecording', 
			    'numerical', 'integerOnly' => true),
			array('questionaryid, salary', 'length', 'max' => 11),
			array('custom', 'length', 'max' => 255),
		    array('passportexpires', 'safe'),
			// The following rule is used by search().
			array('id, questionaryid, salary, wantsbusinesstrips, hasforeignpassport, 
			    passportexpires, isnightrecording, istoplessrecording, isfreerecording, custom', 
			    'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		);
	}
	
	/**
	 * @see parent::behaviors()
	 */
	/*public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'CTimestampBehavior' => array(
	            'class'           => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        ),
	    );
	}*/

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'questionaryid' => QuestionaryModule::t('questionary'),
			'salary' => QuestionaryModule::t('salary_label'),
			'wantsbusinesstrips' => QuestionaryModule::t('wantsbusinesstrips_label'),
			'hasforeignpassport' => QuestionaryModule::t('hasforeignpassport_label'),
			'passportexpires' => QuestionaryModule::t('passportexpires_label'),
			'isnightrecording' => QuestionaryModule::t('isnightrecording_label'),
			'istoplessrecording' => QuestionaryModule::t('istoplessrecording_label'),
			'isfreerecording' => QuestionaryModule::t('isfreerecording_label'),
			'custom' => QuestionaryModule::t('сustom_conditions'),
		    'timecreated' => Yii::t('coreMessages', 'timecreated'),
		    'timemodified' => Yii::t('coreMessages', 'timemodified'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('questionaryid',$this->questionaryid,true);
		$criteria->compare('salary',$this->salary,true);
		$criteria->compare('wantsbusinesstrips',$this->wantsbusinesstrips);
		$criteria->compare('hasforeignpassport',$this->hasforeignpassport);
		$criteria->compare('passportexpires',$this->passportexpires,true);
		$criteria->compare('isnightrecording',$this->isnightrecording);
		$criteria->compare('istoplessrecording',$this->istoplessrecording);
		$criteria->compare('isfreerecording',$this->isfreerecording);
		$criteria->compare('custom',$this->custom,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
	
	/**
	 * 
	 * @param  int $salary
	 * @return QRecordingConditions
	 */
	public function withSalaryLessThen($salary, $operation='AND')
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`salary`', '<='.$salary);
	    
	    $this->getDbCriteria()->mergeWith($criteria);
	    
	    return $this;
	}
	
	/**
	 *
	 * @param  int $salary
	 * @return QRecordingConditions
	 */
	public function withSalaryMoreThen($salary, $operation='AND')
	{
	    $criteria = new CDbCriteria();
	    $criteria->compare($this->getTableAlias(true).'.`salary`', '>='.$salary);
	     
	    $this->getDbCriteria()->mergeWith($criteria);
	     
	    return $this;
	}
	
	/**
	 * Получить срок истечения загранпаспорта
	 */
	public function getPassportexpires($value)
	{
	    if ( $this->scenario === 'view' )
	    {
	        return date('d.m.Y', $this->passportexpires);
	    }
	    return $this->passportexpires;
	}
	
	/**
	 * Установить срок истечения загранпаспорта
	 */
	public function setPassportexpires($value)
	{
	    $this->passportexpires = ActiveDateSelect::make_unixtime($value);
	}
	
	/**
	 * Получить размер участия в съемках
	 * @return string
	 */
	public function getSalary()
	{
	    if ( $this->scenario == 'view' )
	    {
	        return $this->salary.' p.';
	    }
	    return $this->salary;
	}
}