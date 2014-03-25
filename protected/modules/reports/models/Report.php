<?php

/**
 * Модель для работы с отчетами
 *
 * Таблица '{{reports}}':
 * @property integer $id
 * @property string $authorid
 * @property string $name
 * @property string $type
 * @property string $timecreated
 * @property string $timemodified
 * @property string $plantime
 * @property string $data
 * @property string $status
 * @property string $comment
 * @property string $key
 * 
 * @property string $reportData
 * 
 * @todo языковые строки
 * @todo добавить какую-то общую проверку для сериализуемого массива data 
 * @todo сделать создание ссылок по событиям
 */
class Report extends CActiveRecord
{
    /**
     * @var string - статус отчета: черновик - отчет в этом статусе представляет собой заготовку без данных. 
     *               Это промежуточный статус, он присваивается всем отчетам вне зависимости от типа, 
     *               сразу же после создания
     */
    const STATUS_DRAFT    = 'draft';
    /**
     * @var string - статус отчета: запланирован. Этот статус нужен для отчетов, создающихся по расписанию:
     *               они находятся в нем до того момента когда придет время собирать данные.
     */
    const STATUS_PLAN     = 'plan';
    /**
     * @var string - статус отчета: завершен. Отчет сформирован, данные собраны, дальнейшее изменение данных не 
     *               планируется, поэтому отчеты в этос статусе редактировать нельзя.
     */
    const STATUS_FINISHED = 'finished';
    /**
     * @var string - статус отчета: удален. Этот статус используется для работы "мягкого удаления",
     *               чтобы не терять данные
     */
    const STATUS_DELETED  = 'deleted';
    
    /**
     * @var array - данные отчета
     */
    protected $_data;
    
    /**
     * @see CActiveRecord::init()
     */
    public function init()
    {
        Yii::import('ext.ESearchScopes.behaviors.*');
        Yii::import('ext.ESearchScopes.models.*');
        Yii::import('ext.ESearchScopes.*');
        
        parent::init();
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Report the static model class
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
		return '{{reports}}';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::defaultScope()
	 */
	public function defaultScope()
	{
	    return array(
	        'order' => "`timecreated` DESC"
	    );
	}
	
	/**
	 * Именованная группа условий: извлечь только отчеты определенного типа
	 */
	public function byType($type='')
	{
	    return array(
	        'condition' => "`type` = :type",
	        'params'    => array(':type' => $type),
	    );
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('authorid, timecreated, timemodified, plantime', 'length', 'max'=>11),
			array('name', 'length', 'max'=>255),
			array('key', 'length', 'max'=>127),
			array('type', 'length', 'max'=>20),
			array('status', 'length', 'max'=>50),
		    array('comment', 'length', 'max'=>4095),
			array('data', 'safe'),
			// The following rule is used by search().
			array('id, authorid, name, type, timecreated, timemodified, plantime, data, status', 'safe', 'on'=>'search'),
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
	            'class' => 'zii.behaviors.CTimestampBehavior',
	            'createAttribute' => 'timecreated',
	            'updateAttribute' => 'timemodified',
	        ),
	    );
	}
	
	/**
	 * @see CActiveRecord::beforeSave()
	 */
	public function beforeSave()
	{
	    if ( $this->isNewRecord )
	    {
	        if ( ! $this->status )
	        {// по умолчанию все отчеты создаются в статусе "черновик", если не указано иное
	            $this->status   = self::STATUS_DRAFT;
	        }
	        $this->authorid = Yii::app()->user->id;
	        $this->key      = sha1(microtime().Yii::app()->params['hashSalt']);
	    }
	    if ( ! empty($this->_data) )
	    {
	        $this->data = serialize($this->_data);
	    }
	    return parent::beforeSave();
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		    'links' => array(self::HAS_MANY, 'ReportLink', 'reportid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'authorid' => 'Автор',
			'name' => 'Название',
			'type' => 'Тип',
			'timecreated' => 'Время создания',
			'timemodified' => 'Timemodified',
			'plantime' => 'Планируемое время сбора',
			'data' => 'Данные отчета',
			'status' => 'Статус',
			'comment' => 'Комментарий',
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
		$criteria->compare('authorid',$this->authorid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('timecreated',$this->timecreated,true);
		$criteria->compare('timemodified',$this->timemodified,true);
		$criteria->compare('plantime',$this->plantime,true);
		$criteria->compare('status',$this->status,true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
	
    /**
     * Получить данные отчета 
     * @return array
     */
    public function getData()
    {
        if ( empty($this->_data) )
        {
            $this->_data = unserialize($this->data);
        }
        return $this->_data;
    }
    
    /**
     * Получить данные отчета (эта функция нужна для того чтобы нормально работал геттер поля data.
     * Если обращатся к отчету $report->data - то геттер "getData" не срабатывает)
     * @return array
     */
    public function getReportData()
    {
        return $this->getData();
    }
    
    /**
     * Сохранить данные отчета
     * @param array $value
     * @return null
     */
    public function setData($value)
    {
        $this->_data = $value;
    }
    
    /**
     * Собрать все данные для отчета в один массив.
     * Эта функция должна быть переопределена в дочерних классах
     * @param array $options - дополнительные параметры для сбора отчета (свои для каждого типа отчета)
     * @return array
     */
    public function collectData($options)
    {
        return array();
    }
    
    /**
     * Собрать все данные для отчета и сохранить их в базу
     * @return null
     */
    public function createReport($options)
    {
        if ( $this->status == self::STATUS_FINISHED )
        {// отчет уже собран, действия не требуются
            return;
        }
        // собираем данные для отчета
        $data = $this->collectData($options);
        // сохраняем собранные данные и помечаем отчет собранным
        $this->setData($data);
        $this->status = self::STATUS_FINISHED;
        $this->timemodified = time();
        // сохраняем отчет в базу
        $this->save();
        // указываем, по каким объектам собран отчет
        $this->createLinks();
    }
    
    /**
     * Сохранить информацию о том, по каким объектам собран отчет
     * Набор связей свой для каждого отчета, поэтому эта функция переопределяется в каждом отчете
     * @return null
     */
    protected function createLinks()
    {
        return;
    }
}