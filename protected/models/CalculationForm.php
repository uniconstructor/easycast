<?php

/**
 * Класс формы расчета стоимости заказа
 */
class CalculationForm extends CFormModel
{
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
            array('projectname, projecttype, eventtime, userinfo, daysnum, name, email', 'required'),
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
            'userinfo'    => 'Кого хотите пригласить?',
            'daysnum'     => 'Сколько съемочных дней планируется?',
            'comment'     => 'Дополнительная информация',
            // контакты заказчика
            'name'        => 'Имя',
            'email'       => 'email',
            'phone'       => 'Телефон',
        );
    }
}