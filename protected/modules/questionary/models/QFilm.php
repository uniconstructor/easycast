<?php

/**
 * Модель для хранения данных о фильме
 * 
 * @property int $id
 * @property int $externalid
 * @property string $name
 * @property string $director
 * @property int $timecreated
 * @property int $timemodified
 */
class QFilm extends CActiveRecord
{
    /**
     * 
     * @param system $className
     * @return QFilm
     */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @see CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return '{{q_films}}';
	}

	/**
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
			array('externalid, date, timecreated, timemodified', 'length', 'max' => 11),
			array('name, director', 'length', 'max' => 255),
			array('id, externalid, name, date, director, timecreated, timemodified', 'safe', 'on' => 'search'),
		);
	}
    
	/**
	 * @see CActiveRecord::relations()
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
		        'updateAttribute' => 'timemodified',
		    ),
		);
	}

	/**
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'ID'),
			'externalid' => Yii::t('app', 'Externalid'),
			'name' => Yii::t('app', 'Name'),
			'date' => Yii::t('app', 'Date'),
			'director' => Yii::t('app', 'Director'),
		);
	}
    
	/**
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('externalid',$this->externalid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('director',$this->director,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}

    /**
     * Проверить, есть ли в нашей базе фильи с таким названием
     * @param string $name - название фильма
     *
     * @return bool|int - id найденного фильма (если он существует) или false
     */
    public function filmExists($name)
    {
        if ( ! $films = $this->findAll('name = :name', array(':name' => $name)) )
        {// нет фильма с таким названием
            return false;
        }

        $film = current($films);
        return $film->id;
    }

    /**
     * Добавить к нашему списку фильмов новый (введенный пользователем)
     * @param string $name - название фильма
     * @param string $year - год выхода (unixtime)
     * @param string $director - режиссер
     *
     * @return int - id новой записи
     */
    public function addUserFilm($name, $year=null, $director=null)
    {
        $film = new QFilm;
        $film->name       = $name;
        $film->externalid = 0;
        $film->date       = $year;
        $film->director   = $director;

        $film->save();

        return $film->id;
    }
}
