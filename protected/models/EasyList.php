<?php

/**
 * Списки для группировки объектов системы.
 * Один список может быть прикреплен одновременно к нескольким объектам
 * Списки могут содержать в себе модели разных типов, для этого каждую модель нужно прикрепить
 * к EasyListItem, и уже список EasyListItem можно перебирать как оычные связанные со списком записи
 * 
 * Списки могут быть:
 * - статическими (например снимок одобренных заявок на роль в определенный период)
 *   такие списки не пополняюстя и не очищаются со временем: данные в них всегда находятся в таком же
 *   состоянии как и в момент создания списка
 * - динамическими или дополняемыми: например список подходящих актеров на роль
 *   Если набор на роль идет несколько дней и на сайте регистрируются подходящие на
 *   роль актеры - то динамический список список будет пополнятся в зависимости от критериев поиска,
 *   которые прикреплены к нему
 *   
 * Каждый список может содержать только уникальные элементы 
 * (в такие списки нельзя добавить один и тот же объект два раза)
 * или же, наоборот, не требовать уникальности 
 * (один элемент можно добавлять в список много раз)
 *
 * Таблица '{{easy_lists}}':
 * 
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $allowupdate
 * @property string $updatemethod
 * @property string $timecreated
 * @property string $timemodified
 * @property string $timeupdated
 * @property string $updateperiod
 * 
 * @todo @property string $unique - должны ли элементы в списке быть уникальными
 */
class EasyList extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{easy_lists}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name', 'required'),
			array('allowupdate', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('description', 'length', 'max'=>4095),
			array('updatemethod', 'length', 'max'=>10),
			array('timecreated, timemodified, timeupdated, updateperiod', 'length', 'max'=>11), //unique
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, description, allowupdate, updatemethod, timecreated, timemodified, timeupdated, updateperiod, unique', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'instances' => array(self::HAS_MANY, 'EasyListInstance', 'easylistid'),
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
			'name' => Yii::t('coreMessages', 'title'),
			'description' => Yii::t('coreMessages', 'description'),
			'allowupdate' => 'Allowupdate',
			'updatemethod' => 'Updatemethod',
			'timecreated' => 'Timecreated',
			'timemodified' => 'Timemodified',
			'timeupdated' => 'Timeupdated',
			'updateperiod' => 'Updateperiod',
			//'unique' => 'Требовать уникальность элементов?',
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('allowupdate',$this->allowupdate);
		$criteria->compare('updatemethod',$this->updatemethod,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('timeupdated',$this->timeupdated,true);
		$criteria->compare('updateperiod',$this->updateperiod,true);
		//$criteria->compare('unique',$this->unique,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EasyList the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
