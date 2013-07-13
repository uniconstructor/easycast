<?php

/**
 * Модель для работы с проектами телеведущего
 *
 * The followings are the available columns in table '{{q_tvshow_instances}}':
 * @property integer $id
 * @property string $questionaryid
 * @property string $channelname
 * @property string $projectname
 * @property string $timestart
 * @property string $timeend
 * @property string $timecreated
 */
class QTvshowInstance extends CActiveRecord
{
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
	 * (non-PHPdoc)
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
	        'order' => 'timeend DESC');
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('questionaryid, timestart, timeend, timecreated, startyear, stopyear', 'length', 'max'=>11),
			array('channelname, projectname', 'length', 'max'=>255),
		    array('channelname, projectname, startyear, stopyear', 'filter', 'filter'=>'trim'),
			array('channelname, projectname, startyear, stopyear', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, questionaryid, channelname, projectname, timestart, timeend, timecreated', 'safe', 'on'=>'search'),
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
		$criteria->compare('channelname',$this->channelname,true);
		$criteria->compare('projectname',$this->projectname,true);
		$criteria->compare('timestart',$this->timestart,true);
		$criteria->compare('timeend',$this->timeend,true);
		$criteria->compare('timecreated',$this->timecreated,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
	    Yii::import('application.modules.questionary.extensions.behaviors.QSaveYearBehavior');
	    return array(
	        'CAdvancedArBehavior',
	        array('class' => 'ext.CAdvancedArBehavior'),
	        'QSaveYearBehavior',
	        array('class' => 'application.modules.questionary.extensions.behaviors.QSaveYearBehavior'),
	    );
	}
	
	/**
	 * Установить год начала
	 * @param int $year
	 * return_type
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
	    return 0;
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
	 * 
	 */
	public function setProjectname($name)
	{
	    $this->projectname = strip_tags($name);
	}
	
	/**
	 * 
	 */
	public function setChannelname($channel)
	{
	    $this->channelname = strip_tags($channel);
	}
	
	
	/**
	 * Данные для создания формы одного ВУЗа при помощи расширения multiModelForm
	 * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
	 * @return array
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