<?php

/**
 * Форма создания роли для онлайн-кастинга
 */
class OnlineCastingRoleForm extends CFormModel
{
    public $name;
    public $description;
    public $eventid;
    
    /**
     * @see CFormModel::init()
     */
    public function init()
    {
        Yii::import('projects.models.*');
        parent::init();
    }
    
    /**
     * @see CModel::rules()
     */
    public function rules()
    {
        return array(
            array('name, description', 'filter', 'filter' => 'trim'),
            // все поля формы обязательные
            array('name, description', 'required'),
        );
    }
    
    /**
     * @see CModel::attributeLabels()
     */
    public function attributeLabels()
    {
        return array(
            'name'        => 'Роль',
            'description' => 'Задача',
        );
    }
}