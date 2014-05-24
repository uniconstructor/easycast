<?php

/**
 * Модель для работы с проектами телеведущего
 *
 * Таблица '{{q_tvshow_instances}}':
 * @property integer $id
 * @property string $questionaryid
 * @property string $channelname
 * @property string $projectname
 * @property string $timestart
 * @property string $timeend
 * @property string $timecreated
 * 
 * @todo прописать ConditionalValidator для полей stopyear и currently
 * @todo добавить очистку для полей "канал" и "телепроект"
 */
class QTvshowInstance extends CActiveRecord
{
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('questionary.extensions.behaviors.QSaveYearBehavior');
        parent::init();
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return QTvshowInstance the static model class
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
		return '{{q_tvshow_instances}}';
	}
	
	/**
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
	        'order' => '`timeend` DESC',
	    );
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('questionaryid, timestart, timeend, timecreated, timemodified, startyear, stopyear', 
			    'length', 'max' => 11),
			array('channelname, projectname', 'length', 'max' => 255),
		    array('channelname, projectname, startyear, stopyear', 'filter', 'filter' => 'trim'),
			array('channelname, projectname, startyear', 'required'),
		    array('startyear, stopyear, currently', 'numerical', 'integerOnly' => true),
			
			// The following rule is used by search().
			array('id, questionaryid, channelname, projectname, timestart, timeend, 
			    timecreated, timemodified, currently', 'safe', 'on' => 'search'),
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
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'questionaryid' => QuestionaryModule::t('questionary'),
			'channelname' => QuestionaryModule::t('tvshowmen_channelname'),
			'projectname' => QuestionaryModule::t('tvshowmen_projectname'),
			'startyear' => QuestionaryModule::t('tvshowmen_startyear'),
			'stopyear' => QuestionaryModule::t('tvshowmen_stopyear'),
			'timecreated' => QuestionaryModule::t('timecreated'),
			'timemodified' => QuestionaryModule::t('timemodified'),
			'currently' => QuestionaryModule::t('this_is_current_workplace'),
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
		$criteria->compare('channelname',$this->channelname,true);
		$criteria->compare('projectname',$this->projectname,true);
		$criteria->compare('timestart',$this->timestart,true);
		$criteria->compare('timeend',$this->timeend,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('currently',$this->currently,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
	
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    return array(
	        'QSaveYearBehavior' => array(
	            'class' => 'questionary.extensions.behaviors.QSaveYearBehavior'
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
	    return null;
	}
	
	/**
	 * Установить год начала
	 * @param int $year
	 * return_type
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
	    return null;
	}
	
	/**
	 * Получить период работы ведущим на телепроекте
	 * @return string
	 */
	public function getPeriod()
	{
        $startYear = $this->getStartyear();
        $stopYear = $this->getStopyear();
	    if ( $startYear AND $stopYear )
	    {
	        return $startYear.'-'.$stopYear;
	    }elseif ( $startYear )
	    {
	        return $startYear;
	    }elseif ( $stopYear )
	    {
	        return $stopYear;
	    }
	    
	    return '';
	}
	
	/**
	 * @todo заменить использованием rules()
	 */
	public function setProjectname($name)
	{
	    $this->projectname = strip_tags($name);
	}
	
	/**
	 * @param string $channel - название канала
	 * 
	 * @todo заменить использованием rules()
	 */
	public function setChannelname($channel)
	{
	    $this->channelname = strip_tags($channel);
	}
	
	
	/**
	 * Данные для создания формы одного ВУЗа при помощи расширения multiModelForm
	 * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
	 * @return array
	 * @deprecated использовалось для multimodelform. Удалить при рефакторинге.
	 */
	public function formConfig()
	{
	    return array(
	        'elements'=>array(
	            // телеканал
	            'channelname'=>array(
	                'type'      => 'text',
	                'maxlength' => 255,
	                'visible'   => true,
	            ),
	            // название проекта
	            'projectname'=>array(
	                'type'      => 'text',
	                'maxlength' => 255,
	                'visible'   => true,
	            ),
	            // год начала проекта
	            'startyear'=>array(
	                'type'    =>'dropdownlist',
    	            'items'   => $this->yearList(),
    	            'visible' => true,
    	        ),
    	        // год окончания
    	        'stopyear'=>array(
	                'type'    =>'dropdownlist',
    	            'items'   => $this->yearList(),
    	            'visible' => true,
    	        ),
	        ));
	}
}