 <?php

/**
 * Модель для работы с приглашениями для заказчиков
 * Таблица "{{customer_invites}}"
 *
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
 * @property string $timefinished
 * @property string $feedback
 * @property string $status
 * 
 * Relations:
 * @property User $manager
 * @property User $customer
 * @property Project $project
 * @property ProjectEvent $event
 * @property EventVacancy $vacancy
 * 
 * @todo добавить поле timesent - время отправки приглашения 
 *       (оно может быть создано заранее, а отправлено позднее)
 * @todo добавить поле action - действие, на которое дается разрешение/приглашение
 *       Продумать этот пункт - возможно стоит сделать тут третью нормальную форму, и разрешить
 *       в одном приглашении давать доступ сразу к нескольким действиям, но пока не известно
 *       есть ли в этом необходимость
 */
class CustomerInvite extends CActiveRecord
{
    /**
     * @var string - статус приглашения: черновик. Оно только что было создано, и пока не отправлено
     *               ни заказчику ни нам для утверждения.
     *               Обычно используется для только что созданных онлайн-кастингов, в которые еще не
     *               внесена вся необходимая информация.
     */
    const STATUS_DRAFT       = 'draft';
    /**
     * @var string - статус приглашения: не подтверждено нами. Для приглашений, требующих нашего одобрения
     *               перед отправкой (например активация онлайн-кастинга).
     */
    const STATUS_UNCONFIRMED = 'unconfirmed';
    /**
     * @var string - статус приглашения: отправлено заказчику, но еще не использовано.
     */
    const STATUS_PENDING     = 'pending';
    /**
     * @var string - статус приглашения: используется в данный момент (например идет отбор актеров)
     */
    const STATUS_ACTIVE      = 'active';
    /**
     * @var string - статус приглашения: работа успешно завершена (например отбор актеров закончен).
     */
    const STATUS_FINISHED    = 'finished';
    /**
     * @var string - статус приглашения: отклонено нами. Используется в тех случаях, когда заказчик запросил
     *               у нас какоето-действие или доступ, а мы отказали ему.
     */
    const STATUS_REJECTED    = 'rejected';
    
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
            array('objecttype, objectid, managerid, timecreated, timeused, timefinished, userid', 'length', 'max'=>11),
            array('key, key2', 'length', 'max'=>40),
            array('status', 'length', 'max'=>50),
            array('email, name', 'length', 'max'=>255),
            array('email, name', 'required'),
            array('email', 'email'),
            array('comment, feedback', 'length', 'max'=>4095),
            
            array('id, objecttype, objectid, key, key2, email, name, managerid, timecreated, timeused,
                 userid, timefinished, feedback, status', 'safe', 'on'=>'search'),
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
            'project' => array(self::BELONGS_TO, 'Project', 'objectid'),
            // мероприятие (отбор людей только на мероприятие)
            'event'   => array(self::BELONGS_TO, 'ProjectEvent', 'objectid'),
            // роль (разрешен отбор людей только на роль)
            'vacancy' => array(self::BELONGS_TO, 'EventVacancy', 'objectid'),
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
                'updateAttribute' => null,
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
        {
            // создаем ключи одноразовой ссылки
            $this->key  = $this->generateKey();
            $this->key2 = $this->generateKey();
            // запоминаем кто отправил приглашение
            $this->managerid = Yii::app()->user->id;
        }
        
        return parent::beforeSave();
    }
    
    /**
     * Удаляет запись из базы если возникла ошибка
     * @see CActiveRecord::afterSave()
     */
    protected function afterSave()
    {
        try
        {
            $this->creatreCustomer();
        }catch( CException $e )
        {// не удалось создать заказчика - удаляем запись 
            $this->delete();
            throw new CException('Ошибка при создании приглашения: не удалось создать заказчика.
                Сообщение не было отправлено - попробуйте еще раз.');
            return;
        }
        // отправляем оповещение заказчику
        // @todo продумать, нужно ли каждый раз при создании нового приглашения сразу же отправлять сообщение
        $this->sendNotification();
        
        parent::afterSave();
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
            'name' => 'Имя заказчика (без фамилии, для обращения)',
            'managerid' => 'Managerid',
            'timecreated' => 'Timecreated',
            'timeused' => 'Использовано',
            'comment' => 'Комментарий',
            'userid' => 'Заказчик',
            'timefinished' => 'Время завершения',
            'feedback' => 'Отзыв заказчика после использования',
            'status' => 'Статус',
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
            throw new CException('Ссылка еще не сохранена - нельзя пометить ее использованной');
        }
        $this->timeused = time();
        
        return $this->save(false);
    }
    
    /**
     * Создать заказчика, если он еще не зарегистрирован.
     * Проверяет, есть ли уже заказчик с таким email. Если заказчика нет - создает ему учетную запись.
     * Вызывается из afterSave(). Данные для создания берутся из текущего объекта.
     * @return bool
     * 
     * @throws CException
     * 
     * @todo функция-заглушка: позже дописать функционал
     */
    protected function creatreCustomer()
    {
        return true;
    }
    
    /**
     * Отправить письмо с приглашением и доступом заказчику
     * @return bool
     * 
     * @todo функция-заглушка: позже дописать функционал
     */
    public function sendNotification()
    {
        // @todo проверяем было ли уже отправлено письмо
        
        $params = array(
            'customerInvite' => $this,
            'manager'        => $this->manager,
        );
        // отправляем письмо (сразу же, без очереди)
        $subject = Yii::app()->getModule('mailComposer')->getSubject('customerInvite', $params);
        $message = Yii::app()->getModule('mailComposer')->getMessage('customerInvite', $params);
        Yii::app()->getModule('user')->sendMail($this->email, $subject, $message, true);
        return true;
    }
    
    /**
     * Изменить статус объекта
     * @param string $newStatus
     * @return bool
     * 
     * @todo проверить правильность и допустимость статуса
     */
    public function setStatus($newStatus)
    {
        $this->status = $newStatus;
        return $this->save(false);
    }
}