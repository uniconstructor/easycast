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
 * @property string $expire
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
 * @todo добавить поле duration - период времени на который выдается приглашение
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
     *               у нас какое-то действие или доступ, а мы отказали ему.
     */
    const STATUS_REJECTED    = 'rejected';
    /**
     * @var string - статус приглашения: истек срок действия
     */
    const STATUS_EXPIRED     = 'expired';
    
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
            array('objectid, managerid, timecreated, timeused, timefinished, userid, expire', 'length', 'max' => 11),
            array('key, key2', 'length', 'max' => 40),
            array('objecttype, status', 'length', 'max' => 50),
            array('email, name', 'length', 'max' => 255),
            array('email', 'required'),
            array('email', 'email'),
            array('comment, feedback', 'length', 'max' => 4095),
            
            array('id, objecttype, objectid, key, key2, email, name, managerid, timecreated, timeused,
                 userid, timefinished, feedback, status, expire', 'safe', 'on' => 'search'),
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
     * @see CModel::behaviors()
     */
    public function behaviors()
    {
        return array(
            // автоматическое заполнение дат создания
            'CTimestampBehavior' => array(
                'class'           => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'timecreated',
                //'updateAttribute' => null,
            ),
        );
    }
    
    /**
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
        
        if ( $this->isNewRecord )
        {// отправляем оповещение заказчику (только если запись только что создана)
            $this->sendNotification();
        }
        
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
            'expire' => 'Когда истекает',
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
            'criteria'   => $criteria,
            'pagination' => false,
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
     * Проверить ключи доступа из приглашения заказчика
     * 
     * @param CustomerInvite $invite - приглашение заказчика
     * @param string $key  - первый ключ безопасности (приходит из GET)
     * @param string $key2 - второй ключ безопасности (приходит из GET)
     * @return bool
     */
    public function checkKeys($id, $key, $key2)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('id', $id);
        $criteria->compare('key', $key);
        $criteria->compare('key2', $key2);
        
        return $this->exists($criteria);
    }
    
    /**
     * Пометить приглашение использованным: это значит что заказчик только что перешел по ссылке
     * с приглашением. В этот момент: 
     * - статус приглашения меняется на "используется в данный момент" (active)
     * - запоминается время использования приглашения
     * 
     * Если приглашением поспользовался админ - то никаких действий не происходит: это нужно для того
     * чтобы администраторы могли просмотреть приглашение перед отправкой заказчику 
     * 
     * @return bool
     */
    public function markUsed()
    {
        if ( $this->isNewRecord )
        {
            throw new CException('Приглашение еще не сохранено - нельзя пометить его использованным');
        }
        if ( Yii::app()->user->checkAccess('Admin') )
        {// админ просто проверяет приглашение перед отправкой - ничего не нужно делать
            return true;
        }
        if ( $this->status === self::STATUS_ACTIVE )
        {// статус уже изменен
            return true;
        }
        // запоминаем время использования приглашения
        $this->timeused = time();
        return $this->setStatus(self::STATUS_ACTIVE);
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
    
    /**
     * Сохранить данные приглашения
     * @param array $data
     * @return null
     */
    public function saveData($data)
    {
        $this->data = serialize($data);
        return $this->save();
    }
    
    /**
     * Получить данные заказа в структурированном виде
     * @return null|array - зависит от версии заказа
     */
    public function loadData()
    {
        if ( ! $this->data )
        {
            return null;
        }
        return unserialize($this->data);
    }
}