<?php

/**
 * This is the model class for table "{{q_theatre_instances}}".
 *
 * The followings are the available columns in table '{{q_theatre_instances}}':
 * @property integer $id
 * @property string $questionaryid
 * @property string $theatreid
 * @property string $timestart
 * @property string $timeend
 * @property string $director
 * @property string $timecreated
 * @property string $timemodified
 */
class QTheatreInstance extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return QTheatreInstance the static model class
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
		return '{{q_theatre_instances}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
		    array('startyear, stopyear, name', 'required'),
			array('questionaryid, theatreid, timestart, timeend, timecreated, timemodified', 'length', 'max' => 11),
			array('director, name', 'length', 'max' => 255),
		    array('director, name', 'filter', 'filter' => 'trim'),
		    
		    // если указан свой вариант - он не должен быть пустым
		    array('theatreid', 'ext.YiiConditionalValidator',
		        'if' => array(
		            array('name', 'compare', 'compareValue'=>""),
		        ),
		        'then' => array(
		            array('theatreid', 'required'),
		        ),
		    ),
		    
		    array('startyear, stopyear', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, questionaryid, theatreid, timestart, timeend, director, timecreated, timemodified', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		    'theatre'     => array(self::BELONGS_TO, 'QTheatre', 'theatreid'),
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    Yii::import('questionary.extensions.behaviors.QSaveYearBehavior');
	    return array(
	        'QSaveYearBehavior' => array(
               'class' => 'questionary.extensions.behaviors.QSaveYearBehavior',
	        ),
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
		    'director'   => QuestionaryModule::t('theatre_director'),
		    'name'       => QuestionaryModule::t('theatre'),
		    'theatreid'  => QuestionaryModule::t('theatre'),
		    'startyear'  => QuestionaryModule::t('theatre_startyear'),
		    'stopyear'   => QuestionaryModule::t('theatre_stopyear'),
		    'workperiod' => QuestionaryModule::t('theatre_workperiod'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('questionaryid',$this->questionaryid,true);
		$criteria->compare('theatreid',$this->theatreid,true);
		$criteria->compare('timestart',$this->timestart,true);
		$criteria->compare('timeend',$this->timeend,true);
		$criteria->compare('director',$this->director,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Получить название театра
	 * @return mixed
	 */
	public function getname()
	{
	    if ( isset($this->theatre->name) AND $this->theatre->name )
	    {
	        return $this->theatre->name;
	    }
	    return '';
	}
	
	/**
	 * Установить театр введенный пользователем
	 * @param $name - название театра
	 */
	public function setname($name)
	{
	    if ( $this->theatreid )
	    {// театр выбран из выпадающего списка - нам передан id
	        return;
	    }
	    
	    if ( ! trim($name) )
	    {// название пустое - не сохраняем его
	        return;
	    }
	
	    if ( $id = QTheatre::model()->theatreExists($name) )
	    {// нашли театр по названию - запишем его id
	        $this->theatreid = $id;
	        return;
	    }
	
	    // театр не выбран из списка и не найден по названию - добавим его в наш справочник
	    $this->theatreid = QTheatre::model()->addUserTheatre($name);
	}
	
	/**
	 * Установить год начала
	 * @param int $year
	 */
	public function setStartyear($year)
	{
	    $year = intval($year);
	    if ( $year )
	    {
	        $this->timestart = mktime(12, 0, 0, 1, 1, $year);
	    }
	}
	
	/**
	 * Получить поле "год"
	 */
	public function getStartyear()
	{
	    if ( $this->timestart )
	    {
	        return date('Y', (int)$this->timestart);
	    }
	    return 0;
	}
	
	/**
	 * Установить год начала
	 * @param int $year
	 */
	public function setStopyear($year)
	{
	    $year = intval($year);
	    if ( $year )
	    {
	        $this->timeend = mktime(12, 0, 0, 1, 1, $year);
	    }
	}
	
	/**
	 * Получить поле "год"
	 */
	public function getStopyear()
	{
	    if ( $this->timeend )
	    {
	        return date('Y', (int)$this->timeend);
	    }
	    return 0;
	}
	
	/**
	 * Получить список театров для выпадающего меню (id => "Название театра")
	 *
	 * @return array
	 */
	public function getTheatreList()
	{
	    $result = array();
	
	    $criteria = new CDbCriteria();
	    $criteria->order = 'name';
	
	    if ( ! $theatres = QTheatre::model()->findAll($criteria) )
	    {
	        return array();
	    }
	
	    foreach ( $theatres as $theatre )
	    {
	        $result[$theatre->id] = $theatre->name;
	    }
	
	    return $result;
	}
	
	/**
	 * Получить период работы в театре
	 * @return string|NULL
	 */
	public function getWorkperiod()
	{
	    if ( $this->startyear AND $this->stopyear )
	    {
	        if ( $this->startyear == $this->stopyear )
	        {
	            return $this->stopyear;
	        }
	        return $this->startyear.'-'.$this->stopyear;
	    }
	    if ( $this->startyear )
	    {
	        return $this->startyear;
	    }
	    if ( $this->stopyear )
	    {
	        return $this->stopyear;
	    }
	    
	    return null;
	}
	
	/**
	 * Данные для создания формы одного театра при помощи расширения multiModelForm
	 * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
	 * @return array
	 * 
	 * @deprecated использовалось для multimodelform. Удалить при рефакторинге
	 */
	public function formConfig()
	{
	    return array(
	        'elements'=>array(
	            // театр (выбор из списка + возможность вводить свой)
	            'theatreid'=>array(
	                'type'    => 'ext.combobox.EJuiComboBox',
	                'data'    => $this->getTheatreList(),
	                'textFieldName' => 'name',
	                'textFieldAttribute' => 'name',
	                'assoc'   => true,
	                'visible' => true,
	            ),
	            // год начала
	            'startyear'=>array(
	                'type'    =>'dropdownlist',
	                'items'   => $this->yearList(1950, date('Y', time())),
	                'visible' => true,
	            ),
	            // год окончания
	            'stopyear'=>array(
	                'type'    =>'dropdownlist',
	                'items'   => $this->yearList(1950, date('Y', time())),
	                'visible' => true,
	            ),
	            // художественный руководитель
	            'director'=>array(
	                'type'      => 'text',
	                'maxlength' => 255,
	                'visible'   => true,
	            ),
	        )
	    );
	}
}