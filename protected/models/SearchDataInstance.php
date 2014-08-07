<?php

/**
 * Связи условий поиска с другими объектами в базе
 *
 * Таблица '{{search_data_instances}}':
 * @property integer $id
 * @property string $searchdataid
 * @property string $objecttype
 * @property string $objectid
 * @property string $targettype - тип объекта по которому происходит поиск 
 *                                (само условие не хранит информацию о том какая модель нужна для поиска)
 * @property string $timecreated
 */
class SearchDataInstance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{search_data_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('searchdataid, objectid, timecreated', 'length', 'max'=>11),
			array('objecttype, targettype', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, searchdataid, objecttype, objectid, targettype, timecreated', 'safe', 'on'=>'search'),
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
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'searchdataid' => 'Searchdataid',
			'objecttype' => 'Objecttype',
			'objectid' => 'Objectid',
			'targettype' => 'targettype',
			'timecreated' => 'Timecreated',
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
		$criteria->compare('searchdataid',$this->searchdataid,true);
		$criteria->compare('objecttype',$this->objecttype,true);
		$criteria->compare('objectid',$this->objectid,true);
		$criteria->compare('targettype',$this->targettype,true);
		$criteria->compare('timecreated',$this->timecreated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SearchDataInstance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
