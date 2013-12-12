<?php

/**
 * Класс формы расчета стоимости заказа
 * @todo добавить фамилию заказчика
 */
class CalculationForm extends CFormModel
{
    public $projectname; 
    public $projecttype; 
    public $eventtime; 
    public $plandate; 
    public $categories; 
    public $daysnum; 
    public $comment; 
    public $name; 
    public $email; 
    public $phone; 
    
    /**
     * @see CFormModel::init()
     */
    public function init()
    {
        Yii::import('ext.LPNValidator.LPNValidator');
    }
    
    /**
     * @see CModel::rules()
     */
    public function rules()
    {
        return array(
            array('projectname, name, email, phone', 'filter', 'filter' => 'trim'),
            // Сохраняем номер телефона в правильном формате (10 цифр)
            array('phone', 'LPNValidator', 'defaultCountry' => 'RU'),
            array('email', 'email'),
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
            'categories'  => 'Кого хотите пригласить?',
            //'userinfo'    => 'Опишите подробнее кого вы хотите видеть',
            'daysnum'     => 'Сколько съемочных дней планируется?',
            'comment'     => 'Дополнительная информация',
            // контакты заказчика
            'name'        => 'Имя',
            'email'       => 'email',
            'phone'       => 'Телефон',
        );
    }
}