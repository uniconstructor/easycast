<?php

/**
 * Класс для работы с одним экземпляром фильма
 * 
 * @property int $id
 * @property int $questionaryid
 * @property int $filmid
 * @property string $name
 * @property string $role
 * @property string $director
 * @property int $date
 * @property int $timecreated
 * @property int $timemodified
 * 
 * Relations:
 * @property QFilm $film
 * @property Questionary $questionary
 */
class QFilmInstance extends CActiveRecord
{
    //public $year; 
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
	        //'with'  => 'film',
	        'order' => '`date` DESC',
	    );
	}

	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
			array('questionaryid, filmid, timecreated, timemodified, year', 'length', 'max' => 11),
            // роль
			array('role', 'length', 'max' => 255),
		    array('role', 'filter', 'filter' => 'trim'),
		    array('role', 'required'),
            // название фильма
            array('name', 'length', 'max' => 255),
		    array('name', 'filter', 'filter' => 'trim'),
            array('name', 'required'),
            // год выхода
            array('date', 'numerical', 'integerOnly' => true),
		    array('date', 'filter', 'filter' => 'trim'),
		    //array('date', 'required'),
            // режиссер
            array('director', 'length', 'max' => 255),
		    array('director', 'filter', 'filter' => 'trim'),

			array('id, questionaryid, filmid, name, role, date, director, timecreated, timemodified', 'safe', 'on'=>'search'),
		);
	}
	
    /**
     * @see CActiveRecord::relations()
     */
	public function relations()
	{
		return array(
		    // анкета к которой привязана ссылка на фильм
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		    // сам фильм
		    'film'        => array(self::BELONGS_TO, 'QFilm', 'filmid'),
		);
	}
    
	/**
	 * @see CModel::behaviors()
	 */
	public function behaviors()
	{
		return array(
            'CAdvancedArBehavior' => array('class' => 'ext.CAdvancedArBehavior'),
            'QSaveYearBehavior'   => array(
                'class' => 'questionary.extensions.behaviors.QSaveYearBehavior',
                'yearfield' => 'date',
            ),
		    // автоматическое заполнение дат создания и изменения
		    'CTimestampBehavior'  => array(
		        'class' => 'zii.behaviors.CTimestampBehavior',
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
			'questionaryid' => Yii::t('app', 'Questionaryid'),
			'filmid' => QuestionaryModule::t('film_name_label'),
			'name' => QuestionaryModule::t('film_name_label'),
			'role' => QuestionaryModule::t('film_role_label'),
            'year' => QuestionaryModule::t('film_year_label'),
            'date' => QuestionaryModule::t('film_year_label'),
            'director' => QuestionaryModule::t('film_director_label'),
			'timecreated' => Yii::t('app', 'Timecreated'),
			'timemodified' => Yii::t('app', 'Timemodified'),
		);
	}

	/**
	 * @return CActiveDataProvider
	 * @return void
	 */
	public function search()
	{
		$criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('questionaryid',$this->questionaryid,true);
		$criteria->compare('filmid',$this->filmid,true);
		$criteria->compare('role',$this->role,true);
		$criteria->compare('timecreated',$this->timecreated,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
		));
	}

    /**
     * @see parent::beforeSave
     */
    protected function beforeSave()
    {
        return parent::beforeSave();
    }

    /**
     * @see parent::afterSave
     */
    protected function afterSave()
    {
        parent::afterSave();
    }
    
    /**
     * 
     * @param unknown $name
     * @return void
     */
    public function setName($name)
    {
        if ( $this->film AND $this->film->name == $name )
        {// Название фильма не менялось
            return;
        }
        
        if ( ! trim($name) )
        {// название пустое - не сохраняем его
            return;
        }
        
        // фильм не выбран из списка - попробуем отыскать его по имени
        if ( $id = QFilm::model()->filmExists($name) )
        {// нашли фильм по названию - просто запишем его id
            $this->filmid = $id;
        }else
        {// фильм не выбран из списка и не найден по названию - добавим его в наш справочник
            $this->filmid = QFilm::model()->addUserFilm($name, $this->date, $this->director);
        }
        
        // обрезаем любые кавычки в названии
        $name = ECPurifier::trimQuotes($name);
        $this->name = $name;
    }

    public function setFilmid($id)
    {
        if ( QFilm::model()->exists('id = :id', array(':id' => $id)) )
        {
            $this->filmid = $id;
        }else
        {
            // @todo обработать ошибку
        }
    }
    
    /**
     * Данные для создания формы одного фильма при помощи расширения multiModelForm
     * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
     * @return array
     * 
     * @deprecated нужно было для виджета multimodelform. Больше не используется.
     * @todo удалить при рефакторинге
     */
    public function formConfig()
    {
        return array(
            'elements' => array(
                // название фильма
                'name' => array(
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
                'role' => array(
                    'type'      => 'text',
                    'maxlength' => 255,
                    'visible'   => true,
                ),
                // год выхода
                'year' => array(
                    'type'    =>'dropdownlist',
                    'items'   => $this->yearList(),
                    'visible' => true,
                ),
                // режиссер
                'director' => array(
                    'type'      => 'text',
                    'maxlength' => 255,
                    'visible'   => true,
                ),
            ));
    }
}
