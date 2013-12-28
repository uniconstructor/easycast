<?php

/**
 * Класс формы расчета стоимости заказа
 */
class CalculationForm extends CFormModel
{
    public $projectname; 
    public $projecttype; 
    public $eventtime; 
    public $plandate;
    public $duration;
    public $categories; 
    public $daysnum; 
    public $comment; 
    public $name; 
    public $lastname; 
    public $email; 
    public $phone; 
    
    /**
     * @see CFormModel::init()
     */
    public function init()
    {
        Yii::import('ext.LPNValidator.LPNValidator');
        Yii::import('projects.models.*');
    }
    
    /**
     * @see CModel::rules()
     */
    public function rules()
    {
        return array(
            array('projectname, name, lastname, email, phone, duration, daysnum', 'filter', 'filter' => 'trim'),
            array('duration, daysnum', 'numerical', 'integerOnly' => true),
            // Сохраняем номер телефона в правильном формате (10 цифр)
            array('phone', 'LPNValidator', 'defaultCountry' => 'RU', 'allowEmpty' => true),
            array('email', 'email'),
            array('plandate, categories, comment', 'safe'),
            array('projectname, projecttype, eventtime, daysnum, name, email', 'required'),
        );
    }
    
    /**
     * @see CModel::attributeLabels()
     */
    public function attributeLabels()
    {
        return array(
            // информация о проекте
            'projectname' => 'Название проекта',
            'projecttype' => 'Тип проекта',
            'eventtime'   => 'Это дневная или ночная съемка?',
            'plandate'    => 'Когда планируется съемка?',
            'duration'    => 'Длительность смены (в часах)',
            'categories'  => 'Кого хотите пригласить?',
            //'userinfo'    => 'Опишите подробнее кого вы хотите видеть',
            'daysnum'     => 'Сколько съемочных дней планируется?',
            'comment'     => 'Дополнительная информация',
            // контакты заказчика
            'name'        => 'Имя',
            'lastname'    => 'Фамилия',
            'email'       => 'email',
            'phone'       => 'Телефон',
        );
    }
    
    /**
     * Сохранить запрос на расчет стоимости
     * Добавляет в Мегаплан новую задачу со всей информацией о заказе
     * @return boolean
     */
    public function save()
    {
        // создаем новый заказ на расчет стоимости
        $order = new FastOrder;
        $order->type       = FastOrder::TYPE_CALCULATION;
        $order->name       = $this->name;
        $order->customerid = 0;
        $order->email      = $this->email;
        $order->phone      = $this->phone;
        $order->comment    = $this->comment;
        // сохраняем данные формы заказа
        $order->orderdata  = serialize($this->attributes);
        if ( ! $order->save() )
        {
            throw new CException('Не удалось создать заказ при сохранении формы расчета стоимости');
        }
        
        return true;
    }
}