<?php

/**
 * Класс для работы с одним экземпляром фильма
 */
class QFilmInstance extends CActiveRecord
{
    public $_year     = '';
    public $_director = '';

    /**
     * 
     * @param system $className
     * @return QFilmInstance
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
		return '{{q_film_instances}}';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
	        'with' => 'film',
	        'order' => 'film.date DESC');
	}

	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
			array('questionaryid, filmid, timecreated', 'length', 'max'=>11),
            // роль
			array('role', 'length', 'max'=>128),
		    array('role', 'filter', 'filter'=>'trim'),
            // название фильма
            array('name', 'length', 'max'=>255),
		    array('name', 'filter', 'filter'=>'trim'),
            array('name', 'required'),
            // год выхода
            array('year', 'numerical', 'integerOnly'=>true),
		    array('year', 'filter', 'filter'=>'trim'),
		    //array('year', 'required'),
            // режиссер
            array('director', 'length', 'max'=>255),
		    array('director', 'filter', 'filter'=>'trim'),

			array('id, questionaryid, filmid, role, timecreated', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		    'film'        => array(self::BELONGS_TO, 'QFilm', 'filmid'),
		);
	}

	public function behaviors()
	{
		return array(
            'CAdvancedArBehavior',
                     array('class' => 'ext.CAdvancedArBehavior'),
            'QSaveYearBehavior',
                     array('class' => 'application.modules.questionary.extensions.behaviors.QSaveYearBehavior'),
				);
	}

	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'ID'),
			'questionaryid' => Yii::t('app', 'Questionaryid'),
			'filmid' => QuestionaryModule::t('film_name_label'),
			'name' => QuestionaryModule::t('film_name_label'),
			'role' => QuestionaryModule::t('film_role_label'),
            'year' => QuestionaryModule::t('film_year_label'),
            'director' => QuestionaryModule::t('film_director_label'),
			'timecreated' => Yii::t('app', 'Timecreated'),
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('questionaryid',$this->questionaryid,true);

		$criteria->compare('filmid',$this->filmid,true);

		$criteria->compare('role',$this->role,true);

		$criteria->compare('timecreated',$this->timecreated,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @see parent::beforeSave
     */
    protected function beforeSave()
    {
        if ( $this->isNewRecord )
        {
            $this->timecreated = time();
        }

        if ( ! isset($this->role) OR ! $this->role )
        {
            $this->role = null;
        }

        return parent::beforeSave();
    }

    /**
     * @see parent::afterSave
     */
    protected function afterSave()
    {
        if ( $this->isNewRecord )
        {
            $film = $this->film;
            if ( $this->_director )
            {
                $film->director = $this->_director;
            }
            if ( $this->_year )
            {
                $film->date     = $this->_year;
            }

            $film->save();
        }

        parent::afterSave();
    }

    public function setdirector($director)
    {
        if ( $director )
        {
            $this->_director = $director;
        }
    }

    public function getdirector()
    {
        if ( isset($this->film->director) AND $this->film->director )
        {
            return $this->film->director;
        }
        return '';
    }

    public function setname($name)
    {
        /*if ( $this->filmid AND $this->film->name == $name )
        {// фильм выбран из списка - не добавляем его в наш справочник
            return;
        }*/
        
        if ( ! trim($name) )
        {// название пустое - не сохраняем его
            return;
        }
        
        // фильм не выбран из списка - попробуем отыскать его по имени
        /*if ( $id = QFilm::model()->filmExists($name) )
        {// нашли фильм по названию - просто запишем его id
            $this->filmid = $id;
            return;
        }*/

        // фильм не выбран из списка и не найден по названию - добавим его в наш справочник
        $this->filmid = QFilm::model()->addUserFilm($name, $this->_year, $this->_director);
    }

    public function getname()
    {
        if ( isset($this->film->name) AND $this->film->name )
        {
            return $this->film->name;
        }
        return '';
    }

    public function setyear($year)
    {
        if ( $year )
        {
            $this->_year = mktime(12, 0, 0, 1, 1, $year);
        }
    }

    public function getyear()
    {
        if ( isset($this->film->date) AND $this->film->date )
        {
            return date('Y', (int)$this->film->date);
        }

        return '';
    }

    public function setfilmid($id)
    {
        if ( QFilm::model()->exists('id = :id', array(':id' => $id)) )
        {
            $this->filmid = $id;
        }
    }

    /**
     * Данные для создания формы одного фильма при помощи расширения multiModelForm
     * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
     * @return array
     */
    public function formConfig()
    {
        return array(
            'elements'=>array(
                // название фильма
                'name'=>array(
                    'type'      => 'text',
                    'maxlength' => 255,
                    'visible'   => true,
                ),
                // id фильма (для ранее добавленных)
                /*'filmid'=>array(
                    'type'      => 'hidden',
                    'value'     => $this->universityid,
                    'visible'   => true,
                ),*/
                // роль
                'role'=>array(
                    'type'      => 'text',
                    'maxlength' => 255,
                    'visible'   => true,
                ),
                // год выхода
                'year'=>array(
                    'type'    =>'dropdownlist',
                    'items'   => $this->yearList(),
                    'visible' => true,
                ),
                // режиссер
                'director'=>array(
                    'type'      => 'text',
                    'maxlength' => 255,
                    'visible'   => true,
                ),
            ));
    }
}
