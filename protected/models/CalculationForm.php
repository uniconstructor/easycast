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
     * Добавляет в Мегаплан новую задачу со всей информацией
     * @return boolean
     */
    public function save()
    {
        /*$description = $this->createDescription();
        // создаем данные для задачи
        $task = array();
        $task['Model[Name]']        = 'Новый запрос расчета стоимости '.date('Y-m-d H:i');
        $task['Model[Responsible]'] = '1000004';
        $task['Model[Statement]']   = $description;
        
        // создаем задачу в Мегаплане
        $result = Yii::app()->megaplan->createTask($task);*/
        
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
    
    /**
     * Создать из данных формы текстовое описание для запроса расчета стоимости
     * @return string
     */
    protected function createDescription()
    {
        $result = '';
        $type = Project::model()->getTypetext($this->projecttype);
        
        $result .= "С сайта поступил новый запрос на расчет стоимости. \n\n<br><br>";
        $result .= "Имя заказчика: {$this->name}<br>\n";
        $result .= "Фамилия: {$this->lastname}<br>\n";
        $result .= "email: {$this->email}<br>\n";
        $result .= "Телефон: {$this->phone}<br><br>\n";
        
        $result .= "Проект: {$this->projectname}<br>\n";
        $result .= "Тип проекта: {$type}<br>\n";
        $result .= "Планируемая дата: {$this->plandate}<br>\n";
        $result .= "Кто требуется: {$this->categories}<br>\n";
        $result .= "Количество съемочных дней: {$this->daysnum}<br>\n";
        $result .= "Комментарий: {$this->comment}<br>\n";
        
        return $result;
    }
}