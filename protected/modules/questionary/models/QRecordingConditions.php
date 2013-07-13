<?php

/**
 * Класс для работы с условиями участия актера в съемках
 *
 * The followings are the available columns in table '{{q_recording_conditions}}':
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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('wantsbusinesstrips, hasforeignpassport, isnightrecording, istoplessrecording, isfreerecording', 'numerical', 'integerOnly'=>true),
			array('questionaryid, salary', 'length', 'max'=>11),
			array('custom', 'length', 'max'=>255),
		    array('passportexpires', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, questionaryid, salary, wantsbusinesstrips, hasforeignpassport, passportexpires, isnightrecording, istoplessrecording, isfreerecording, custom', 'safe', 'on'=>'search'),
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
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		);
	}

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
		$criteria->compare('questionaryid',$this->questionaryid,true);
		$criteria->compare('salary',$this->salary,true);
		$criteria->compare('wantsbusinesstrips',$this->wantsbusinesstrips);
		$criteria->compare('hasforeignpassport',$this->hasforeignpassport);
		$criteria->compare('passportexpires',$this->passportexpires,true);
		$criteria->compare('isnightrecording',$this->isnightrecording);
		$criteria->compare('istoplessrecording',$this->istoplessrecording);
		$criteria->compare('isfreerecording',$this->isfreerecording);
		$criteria->compare('custom',$this->custom,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Получить срок истечения загранпаспорта
	 */
	public function getPassportexpires($value)
	{
	    if ( $this->scenario == 'view' )
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