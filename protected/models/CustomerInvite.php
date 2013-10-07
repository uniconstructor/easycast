 <?php

/**
 * Модель для работы с приглашениями для заказчиков
 * Таблица "{{customer_invites}}".
 *
 * The followings are the available columns in table '{{customer_invites}}':
 * @property integer $id
 * @property string $objecttype
 * @property string $objectid
 * @property string $key
 * @property string $key2
 * @property string $email
 * @property string $name
 * @property string $managerid
 * @property string $timecreated
 * @property string $timeused
 * @property string $comment
 * @property string $userid
 * 
 * Relations:
 * @property User $manager
 * @property User $customer
 * @property Project $project
 * @property ProjectEvent $event
 * @property EventVacancy $vacancy
 */
class CustomerInvite extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerInvite the static model class
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
        return '{{customer_invites}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('objecttype, objectid, managerid, timecreated, timeused, userid', 'length', 'max'=>11),
            array('key, key2', 'length', 'max'=>40),
            array('email, name', 'length', 'max'=>255),
            array('email', 'email'),
            array('comment', 'length', 'max'=>4095),
            
            array('id, objecttype, objectid, key, key2, email, name, managerid, timecreated, timeused, userid', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // админ, который выслал приглашение
            'manager' => array(self::BELONGS_TO, 'User', 'managerid'),
            // заказчик
            'customer' => array(self::BELONGS_TO, 'User', 'userid'),
            // проект (отбор людей на весь проект)
            'project' => array(self::BELONGS_TO, 'Project', 'objectid', 'condition'=>"t.objecttype = 'project'"),
            // мероприятие (отбор людей только на мероприятие)
            'event'   => array(self::BELONGS_TO, 'ProjectEvent', 'objectid', 'condition'=>"t.objecttype = 'event'"),
            // роль (разрешен отбор людей только на роль)
            'vacancy' => array(self::BELONGS_TO, 'EventVacancy', 'objectid', 'condition'=>"t.objecttype = 'vacancy'"),
            // @todo онлайн-кастинг (отбор людей при проведении онлайн-кастинга)
            //'casting' => array(self::BELONGS_TO, '???', 'objectid', 'condition'=>"t.objecttype = 'casting'"),
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see CModel::behaviors()
     */
    public function behaviors()
    {
        return array(
            // автоматическое заполнение дат создания
            'CTimestampBehavior' => array(
                'class'           => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'timecreated',
            ),
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::beforeSave()
     */
    protected function beforeSave()
    {
        if ( $this->isNewRecord )
        {// создаем ключи одноразовых ссылок при создании записи
            if ( ! $this->key )
            {
                $this->key = $this->generateKey();
            }
            if ( ! $this->key2 )
            {
                $this->key2 = $this->generateKey();
            }
        }
        return parent::beforeSave();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'objecttype' => 'Objecttype',
            'objectid' => 'Objectid',
            'key' => 'Key',
            'key2' => 'Key2',
            'email' => 'Email',
            'name' => 'Имя заказчика',
            'managerid' => 'Managerid',
            'timecreated' => 'Timecreated',
            'timeused' => 'Timeused',
            'comment' => 'Комментарий',
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
        $criteria->compare('objecttype',$this->objecttype,true);
        $criteria->compare('objectid',$this->objectid,true);
        $criteria->compare('key',$this->key,true);
        $criteria->compare('key2',$this->key2,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('managerid',$this->managerid,true);
        $criteria->compare('timecreated',$this->timecreated,true);
        $criteria->compare('timeused',$this->timeused,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    /**
     * Создать ключ для одноразовой ссылки
     * 
     * @return string - 40-значный sha1-ключ
     */
    public function generateKey()
    {
        return sha1(time().microtime().Yii::app()->params['hashSalt'].rand(1, 9001));
    }
    
    /**
     * Пометить одноразовую ссылку использованной
     * @return bool
     */
    public function markUsed()
    {
        if ( $this->isNewRecord )
        {
            throw new CException('Ссылка еще не сохранена');
        }
        
        $this->timeused = time();
        return $this->save();
    }
} 