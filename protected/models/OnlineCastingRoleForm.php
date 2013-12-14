<?php

/**
 * Форма создания роли для онлайн-кастинга
 */
class OnlineCastingRoleForm extends CFormModel
{
    public $name;
    public $description;
    public $salary;
    
    // дополнительные поля для того чтобы работал виджет Editable
    // @see http://yii-booster.clevertech.biz/widgets/editable/view/field.html
    //public $isNewRecord = false;
    //public $primaryKey  = 'id';
    //public $id;
    //public $tableSchema;
    
    /**
     * функция для имитации поведения activeRecord, чтобы работал виджет editable
     * @see http://yii-booster.clevertech.biz/widgets/editable/view/field.html
     * @see CModel::isAttributeSafe()
     */
    public function isAttributeSafe($attribute)
    {
        return true;
    }
    
    /**
     * @see CFormModel::init()
     */
    public function init()
    {
        Yii::import('projects.models.*');
        //$this->tableSchema = new stdClass();
        //$this->tableSchema->columns = array();
        
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
            'salary'      => 'Предполагаемый размер оплаты',
        );
    }
    
    /**
     * Сохранить в сессию данные роли
     * @return void
     */
    public function save()
    {
        OnlineCastingForm::setRoleInfo($this);
        
        return true;
    }
}