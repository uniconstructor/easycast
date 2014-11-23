<?php

/**
 * Условия поиска - сохраненные критерии выборки, которые могут быть использованы
 * и для составления запроса (CDbCriteria) и для заполнения формы фильтров поиска
 *
 * Таблица '{{search_data}}':
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property string $description
 * @property string $timecreated
 * @property string $timemodified
 * 
 * Relations:
 * @property SearchFilter[] $searchFilters - фильтры поиска используемые в этом условии
 */
class SearchData extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{search_data}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'length', 'max' => 255),
			array('value, description', 'length', 'max' => 4095),
			array('timecreated, timemodified', 'length', 'max' => 11),
			
			// The following rule is used by search().
			array('id, name, value, description, timecreated, timemodified', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    // фильтры поиска используемые в этом условии
		    'searchFilters' => array(self::HAS_MANY, 'SearchFilter', 'searchdataid'),
		);
	}
	
	/**
	 * @see CActiveRecord::scopes()
	 */
	public function scopes()
	{
	    // стандартные условия поиска по датам создания и изменения
	    $timestampScopes = $this->asa('EcTimestampBehavior')->getDefaultTimestampScopes();
	    // собственные условия поиска для модели
	    $modelScopes = array(
	        
	    );
	    return CMap::mergeArray($timestampScopes, $modelScopes);
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        // автоматическое заполнение дат создания и изменения
	        'EcTimestampBehavior' => array(
	            'class' => 'application.behaviors.EcTimestampBehavior',
	            'updateAttribute' => null,
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
			'name' => 'Name',
			'value' => 'Value',
			'description' => 'Description',
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
		$criteria->compare('value',$this->value,true);
		$criteria->compare('description',$this->description,true);
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
	 * @return SearchData the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
