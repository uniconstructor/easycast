<?php

/**
 * This is the model class for table "{{q_edit_history}}".
 *
 * The followings are the available columns in table '{{q_edit_history}}':
 * @property integer $id
 * @property string $questionaryid
 * @property string $field
 * @property string $type
 * @property string $oldvalue
 * @property string $newvalue
 * @property string $timecreated
 * @property string $editortype
 * @property string $editorid
 */
class QEditHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{q_edit_history}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('field', 'required'),
			array('questionaryid, timecreated, editorid', 'length', 'max' => 11),
			array('field, editortype', 'length', 'max' => 20),
			array('type', 'length', 'max' => 12),
			array('oldvalue, newvalue', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, questionaryid, field, type, oldvalue, newvalue, timecreated, editortype, editorid', 
			    'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    
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
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'questionaryid' => 'Questionaryid',
			'field' => 'Field',
			'type' => 'Type',
			'oldvalue' => 'Oldvalue',
			'newvalue' => 'Newvalue',
			'timecreated' => 'Timecreated',
			'editortype' => 'Editortype',
			'editorid' => 'Editorid',
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
		$criteria->compare('questionaryid',$this->questionaryid,true);
		$criteria->compare('field',$this->field,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('oldvalue',$this->oldvalue,true);
		$criteria->compare('newvalue',$this->newvalue,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('editortype',$this->editortype,true);
		$criteria->compare('editorid',$this->editorid,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return QEditHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
