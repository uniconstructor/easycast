<?php

/**
 * Форма создания онлайн-кастинга
 * При сохранении создает проект и событие
 * 
 * @todo настройка: сколько помнить незаконченный кастинг
 */
class OnlineCastingForm extends CFormModel
{
    public $projectname;
    public $projecttype;
    public $plandate;
    public $projectdescription;
    public $eventdescription;
    public $name;
    public $lastname;
    public $email;
    public $phone;
    
    // дополнительные поля для того чтобы работал виджет Editable
    // @see http://yii-booster.clevertech.biz/widgets/editable/view/field.html
    //public $isNewRecord = false;
    //public $primaryKey  = 'id';
    //public $id;
    //public $tableSchema = array('columns' => array());
    
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
        Yii::import('ext.LPNValidator.LPNValidator');
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
            array('projectname, name, lastname, email, phone', 'filter', 'filter' => 'trim'),
            // Сохраняем номер телефона в правильном формате (10 цифр)
            array('phone', 'LPNValidator', 'defaultCountry' => 'RU', 'allowEmpty' => true),
            array('email', 'email'),
            array('plandate', 'safe'), //comment
            array('projectname, projecttype, projectdescription, eventdescription, name, email', 'required'),
        );
    }
    
    /**
     * @see CModel::attributeLabels()
     */
    public function attributeLabels()
    {
        return array(
            // информация о проекте и мероприятии
            'projectname' => 'Название проекта',
            'projecttype' => 'Тип проекта',
            'plandate'    => 'Когда планируется съемка?',
            'projectdescription' => 'Расскажите о проекте для которого планируется кастинг',
            'eventdescription'   => 'Расскажите о съемках. Что будет происходить, каковы задачи?',
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
        self::setCastingInfo($this);
        
        return true;
    }
    
    /**
     * Создать описание онлайн-кастинга для отправки задачи в Мегаплан
     * @return string
     */
    protected function createDescription()
    {
        $result = 'Новый запрос онлайн-кастинга<br>';
        
        return $result;
    }
    
    /// Функции для работы с сессией
    
    /**
     * Сохранить в сессию данные формы онлайн-кастинга
     * @var OnlineCastingForm $info - форма создания онлайн-кастинга вместе со всей внесенной информацией
     * @return void
     */
    public static function setCastingInfo($info)
    {
        self::initCastingInfo();
        $data = Yii::app()->session->itemAt('onlineCasting');
        $data['info'] = $info;
        
        Yii::app()->session->add('onlineCasting', $data);
    }
    
    /**
     * Получить из сессии данные формы создания онлайн-кастинга
     * @return OnlineCastingForm|null - форма создания онлайн-кастинга вместе со всей внесенной информацией
     */
    public static function getCastingInfo()
    {
        self::initCastingInfo();
        $data = Yii::app()->session->itemAt('onlineCasting');
        return $data['info'];
    }
    
    /**
     * Сохранить в сессию данные формы создания роли
     * @var OnlineCastingForm $info - форма создания роли вместе со всей внесенной информацией
     * @return void
     */
    public static function setRoleInfo($info)
    {
        self::initCastingInfo();
        $data = Yii::app()->session->itemAt('onlineCasting');
        $data['role'] = $info;
        
        Yii::app()->session->add('onlineCasting', $data);
    }
    
    /**
     * Получить из сессии данные формы создания роли
     * @return OnlineCastingRoleForm|null - форма создания роли вместе со всей внесенной информацией
     */
    public static function getRoleInfo()
    {
        self::initCastingInfo();
        $data = Yii::app()->session->itemAt('onlineCasting');
        return $data['role'];
    }
    
    /**
     * Подготовить сессию для работы с онлайн-качтингом
     * @return void
     */
    protected static function initCastingInfo()
    {
        if ( ! Yii::app()->session->contains('onlineCasting') )
        {
            Yii::app()->session->add('onlineCasting', array(
                'info' => new OnlineCastingForm(),
                'role' => new OnlineCastingRoleForm(),
                )
            );
        }
        
        // долго помним данные кастинга на случай если пользователь
        // не завершил создание, но потом решил вернуться 
        Yii::app()->session->setTimeout(3600 * 24 * 14);
    }
}