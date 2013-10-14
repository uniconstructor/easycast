<?php

/**
 * Модель связи отчета с другими объектами.
 *
 * Таблица '{{report_links}}':
 * @property integer $id
 * @property string $reportid
 * @property string $linktype
 * @property string $linkid
 * 
 * Relations:
 * @property Report $report
 */
class ReportLink extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ReportLink the static model class
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
		return '{{report_links}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('reportid, linkid', 'length', 'max'=>11),
			array('linktype', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, reportid, linktype, linkid', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'report' => array(self::BELONGS_TO, 'Report', 'reportid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'reportid' => 'Reportid',
			'linktype' => 'Linktype',
			'linkid' => 'Linkid',
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
		$criteria->compare('reportid',$this->reportid,true);
		$criteria->compare('linktype',$this->linktype,true);
		$criteria->compare('linkid',$this->linkid,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}