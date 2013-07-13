<?php

/**
 * Модель для хранения данных о фильме
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
	 * (non-PHPdoc)
	 * @see CActiveRecord::tableName()
	 */
	public function tableName()
	{
		return '{{q_films}}';
	}

	public function rules()
	{
		return array(
			array('externalid, date, pictureid', 'length', 'max'=>11),
			array('name, director', 'length', 'max'=>128),
			array('id, externalid, name, date, director, pictureid', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function behaviors()
	{
		return array('CAdvancedArBehavior',
				array('class' => 'ext.CAdvancedArBehavior')
				);
	}

	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'ID'),
			'externalid' => Yii::t('app', 'Externalid'),
			'name' => Yii::t('app', 'Name'),
			'date' => Yii::t('app', 'Date'),
			'director' => Yii::t('app', 'Director'),
			'pictureid' => Yii::t('app', 'Pictureid'),
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('externalid',$this->externalid,true);

		$criteria->compare('name',$this->name,true);

		$criteria->compare('date',$this->date,true);

		$criteria->compare('director',$this->director,true);

		$criteria->compare('pictureid',$this->pictureid,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
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
        {// нет ВУЗа с таким названием
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
        $film->pictureid  = 0;

        $film->save();

        return $film->id;
    }
}
