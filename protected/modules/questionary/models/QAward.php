<?php

// Выбор страны из списка
Yii::import('ext.CountryCitySelectorRu.*');

/**
 * Класс для работы с одним званием, призом или наградой, которую раньше получил пользователь
 */
class QAward extends CActiveRecord
{
    /**
     * @var string - поле таблицы куда записывается значение поля "год"
     * @deprecated
     */
    public $yearfield = 'date';

    /**
     * @param system $className
     * @return QAward
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
		return '{{q_awards}}';
	}

	/**
	 * (non-PHPdoc)
	 * @see CModel::rules()
	 */
	public function rules()
	{
		return array(
			array('questionaryid, countryid, date, timecreated, timemodified', 'length', 'max'=>11),
            // название награды
			array('name', 'length', 'max'=>128),
		    array('name', 'filter', 'filter'=>'trim'),
            array('name', 'required'),
            // Номинация
			array('nomination', 'length', 'max'=>255),
		    array('nomination', 'filter', 'filter'=>'trim'),
            // год
            array('year', 'numerical', 'integerOnly'=>true),

			array('id, questionaryid, name, nomination, countryid, date, timecreated, timemodified', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::relations()
	 */
	public function relations()
	{
		return array(
		    'questionary' => array(self::BELONGS_TO, 'Questionary', 'questionaryid'),
		    'country'     => array(self::BELONGS_TO, 'CSGeoCountry', 'countryid'),
		);
	}

    /**
     * @see parent::behaviors()
     */
	public function behaviors()
	{
        return array(
            'CAdvancedArBehavior' => array('class' => 'ext.CAdvancedArBehavior'),
            'QSaveYearBehavior'   => array(
                'class'     => 'questionary.extensions.behaviors.QSaveYearBehavior',
                'yearfield' => 'date',
            ),
        );
	}

	/**
	 * (non-PHPdoc)
	 * @see CModel::attributeLabels()
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'ID'),
			'questionaryid' => Yii::t('app', 'Questionaryid'),
			'name' => QuestionaryModule::t('award_name_label'),
			'nomination' => QuestionaryModule::t('award_nomination_label'),
			'countryid' => QuestionaryModule::t('award_country_label'),
			'date' => QuestionaryModule::t('year_label'),
			'year' => QuestionaryModule::t('year_label'),
			'timecreated' => Yii::t('app', 'Timecreated'),
			'timemodified' => Yii::t('app', 'Timemodified'),
		);
	}

	/**
	 * 
	 * @return CActiveDataProvider
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('questionaryid',$this->questionaryid,true);

		$criteria->compare('name',$this->name,true);

		$criteria->compare('nomination',$this->nomination,true);

		$criteria->compare('countryid',$this->countryid,true);

		$criteria->compare('date',$this->date,true);

		$criteria->compare('timecreated',$this->timecreated,true);

		$criteria->compare('timemodified',$this->timemodified,true);

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

        if ( ! isset($this->nomination) OR ! $this->nomination )
        {
            $this->nomination = null;
        }

        return parent::beforeSave();
    }

    /**
     * Данные для создания формы одного фильма при помощи расширения multiModelForm
     * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
     * @return array
     */
    public function formConfig()
    {
        // создаем объект виджета для выбора страны и города
        $countryConfig = array();
        $countryConfig['country']['default'] = 'RU';
        $countryConfig['country']['topKeys'] = array('RU','UA','BY');
        $countrySelector = new CountryCitySelectorRu($countryConfig);

        // получаем список всех стран
        $countries = $countrySelector->getCountries();
        $countries = $countrySelector->getDropDownOptions($countries);
        $countries = $countrySelector->setTopKeys(array_keys($countrySelector->getTopKeys('country')), $countries);
        $countries = CMap::mergeArray(array('' => Yii::t('coreMessages', 'choose')), $countries);

        return array(
            'elements'=>array(
                // название награды
                'name'=>array(
                    'type'      => 'text',
                    'maxlength' => 255,
                    'visible'   => true,
                ),
                // номинация
                'nomination'=>array(
                    'type'      => 'text',
                    'maxlength' => 255,
                    'visible'   => true,
                ),
                // страна
                'countryid'=>array(
                    'type'    => 'dropdownlist',
                    'items'   => $countries,
                    'visible' => true,
                ),
                // год
                'year'=>array(
                    'type'    =>'dropdownlist',
                    'items'   => $this->yearList(),
                    'visible' => true,
                ),
            ));
    }
}
