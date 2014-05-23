<?php

// родительский класс сложных значений
Yii::import('questionary.models.QActivity');

/**
 * Класс для работы с одним типом танца (стиль + уровень владения)
 * Все используемые классы поведений подключаются в родительском init(),
 * так что не забудь вызвать parent::init при переопределении 
 */
class QDanceType extends QActivity
{
    /**
     * Это условие выборки присутствует во всех классах сложных значений, наследуемых от QActivity
     * Поскольку все классы моделей-наследников QActivity хранят данные в одной таблице, то
     * это условие нужно для того чтобы, например, при всех операциях моделью "Тип танца" 
     * быть уверенным в том что не будут задет соседние значения другого типа: оно автоматически
     * применяется перед каждым SQL-запросом к модели.
     * Подробнее см. справку по defaultScope
     * 
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "dancetypes",
            'condition' => "`dancetypes`.`type` = 'dancetype'",
        );
    }
    
    /**
     * @see parent::rules()
     * @return array
     */
    public function rules()
    {
        // берем все общие проверки из базового класса
        $rules = parent::rules();

        // создаем новые правила проверки для полей "тип танца" и "уровень владения"
        $customRules = array(
            // поле "тип танца" ()
            array('dancetype', 'length', 'max' => 255),
            array('dancetype', 'filter', 'filter' => 'trim'),
            // поле "тип танца" (если введен свой вариант)
            array('name', 'length', 'max' => 255),
            array('name', 'filter', 'filter' => 'trim'),
            
            // если указан свой вариант - он не должен быть пустым
            array('dancetype', 'ext.YiiConditionalValidator',
                'if'   => array(
                    array('name', 'compare', 'compareValue' => ""),
                ),
                'then' => array(
                    array('dancetype', 'required'),
                ),
            ),
            
            // поле "уровень владения (професиогнал/непрофессионал)"
            array('level', 'in', 'range' => array('amateur', 'professional'), 'allowEmpty' => false),
            array('level', 'required'),
        );
        // совмещаем старые правила проверки с новыми
        return CMap::mergeArray($rules, $customRules);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'dancetype'       => QuestionaryModule::t('dancetype_label'),
            'customdancetype' => QuestionaryModule::t('customdancetype_label'),
            'level'           => QuestionaryModule::t('level'),
        );
    }

    /**
     * @see parent::beforeSave()
     * @return bool
     */
    protected function beforeSave()
    {
        if ( $this->isNewRecord )
        {// установка значений по усолчанию
            $this->type = 'dancetype';
            if ( ! isset($this->value) OR ! $this->value )
            {
                $this->value = null;
            }
            // @todo после миграции, которая навела порядок с использованием null во всех таблицах анкеты
            // неизвестно нужно ли теперь устанавливать null здесь. Проверить и удалить если больше не нужно
            $this->timestart = null;
            $this->timeend   = null;
        }
        
        return parent::beforeSave();
    }

    /**
     * @see parent::behaviors()
     */
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        $newBehaviors    = array(
            'QSaveSkillLevelBehavior' => array(
                'class' => 'questionary.extensions.behaviors.QSaveSkillLevelBehavior',
            ),
        );
        // добавляем behavior для работы с полем "уровень владения" и добавляем его к родительским
        return CMap::mergeArray($parentBehaviors, $newBehaviors);
    }

    /**
     * Сеттер для поля "тип танца" - используется, если нужно сохранить стандартный тип танца
     * @param string $dancetype - предустановленный тип танца (короткое название латинскими буквами)
     * @return null
     */
    public function setDanceType($dancetype)
    {
        if ( ! trim($dancetype) OR $dancetype == 'custom' )
        {// Указан собственный тип танца
            $this->value = 'custom';
            return;
        }
        if ( QActivityType::model()->isStandardComplexValue('dancetype', $dancetype) )
        {// Тип танца выбран из списка
            $this->value = $dancetype;
            return;
        }
        
        $this->value = 'custom';
    }

    /**
     * Геттер для поля "тип танца" - используется для получения значения типа танца
     * (не важно, стандартного или добавленного пользователем)
     * 
     * @return string
     */
    public function getDanceType()
    {
        if ( ! $this->value OR $this->value == 'custom' )
        {
            return CHtml::encode($this->uservalue);
        }else
        {
            return $this->value;
        }
    }
    
    /**
     * Получить название танца (отображаемое пользователю)
     * @see QActivity::getName()
     */
    public function getName()
    {
        if ( ! $this->value )
        {
            return '';
        }
        if ( $this->value == 'custom'  )
        {
            return $this->uservalue;
        }else
        {
            $variants = QActivityType::model()->activityVariants('dancetype');
            return $variants[$this->value];
        }
    }
    
    /**
     * Сохранить название стиля танца
     * @param string $name
     * @return null
     */
    public function setName($name)
    {
        if ( QActivityType::model()->isStandardComplexValue('dancetype', $this->value) )
        {// тип танца выбран из выпадающего списка - не вносим его в базу
            $this->uservalue = null;
            return;
        }
        // если стиль танца введен вручную - то попробуем сначала найти его у нас по названию
        $condition = ' name = "dancetype" AND translation = :dancename ';
        $params    = array(':dancename' => $name );
        if ( $danceType = QActivityType::model()->find($condition, $params) )
        {
            $this->value = $danceType->value;
            return;
        }
        // Введен тот стиль танца, которого нет у нас в списке
        $this->value     = 'custom';
        $this->uservalue = strip_tags($name);
    }

    /**
     * Данные для создания формы одного стандартного типа танца которым владеет пользователь
     * при помощи расширения multiModelForm
     * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
     *
     * @return array
     * @deprecated не используется, было нужно для multimodelform, удалить при рефакторинге
     */
    public function formConfig()
    {
        return array(
            'elements' => array(
                // тип танца
                'dancetype' => array(
                    'type'    => 'ext.combobox.EJuiComboBox',
                    'data'    => QActivityType::model()->activityVariants('dancetype'),
                    'textFieldName'      => 'name',
                    'textFieldAttribute' => 'name',
                    'assoc'   => true,
                    'visible' => true,
                ),
                // уровень владения
                'level' => array(
                    'type'    => 'dropdownlist',
                    'items'   => $this->levelList(),
                    'prompt'  => Yii::t('coreMessages','choose'),
                    'visible' => true,
                ),
            ));
    }
}
