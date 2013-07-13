<?php
/**
 * Класс для работы с одним музыкальным инструментом (тип + уровень владения)
 */
class QInstrument extends QActivity
{
    /**
     * (non-PHPdoc)
     * @see QActivity::init()
     */
    public function init()
    {
        parent::init();
        Yii::import('questionary.extensions.behaviors.QSaveSkillLevelBehavior');
    }
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "instruments",
            'condition' => "`instruments`.`type`='instrument'",
        );
    }
    /**
     * @see parent::rules()
     * @return array
     */
    public function rules()
    {
        // наследуем общие проверки от базового класса
        $rules = parent::rules();

        // создаем новые правила проверки для полей "музыкальный инструмент" и "уровень"
        $customRules = array(
            // поле "инструмент"
            array('instrument', 'length', 'max'=>255 ),
            array('instrument', 'required'),
            
            array('name', 'length', 'max'=>255),
            array('name', 'filter', 'filter'=>'trim'),
            
            // поле "уровень владения (професиогнал/непрофессионал)"
            array('level', 'in', 'range'=> array('amateur', 'professional'), 'allowEmpty' => false),
            array('level', 'required'),
        );
        // совмещаем старые проверки с новыми
        return CMap::mergeArray($rules, $customRules);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'instrument'       => QuestionaryModule::t('instrument_label'),
            'custominstrument' => QuestionaryModule::t('custominstrument_label'),
            'level'            => QuestionaryModule::t('level'),
        );
    }

    /**
     * @see parent::beforeSave()
     * @return bool
     */
    protected function beforeSave()
    {
        if ( $this->isNewRecord )
        {
            $this->type      = 'instrument';
            if ( ! isset($this->value) OR ! $this->value )
            {
                $this->value = null;
            }
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
        $newBehaviors = array('QSaveSkillLevelBehavior',
            array(
                'class' => 'questionary.extensions.behaviors.QSaveSkillLevelBehavior'
            ),
        );
        
        return CMap::mergeArray($parentBehaviors, $newBehaviors);
    }

    /**
     * Сеттер для поля "музыкальный инструмент" - используется, если нужно сохранить стандартное значение из списка
     * @param string $instrument - значение из выпадающего списка (короткое название латинскими буквами)
     */
    public function setinstrument($instrument)
    {
        if ( ! trim($instrument) OR $instrument == 'custom' )
        {// Указан собственный инструмент
            $this->value = 'custom';
            return;
        }
        if ( QActivityType::model()->isStandardComplexValue('instrument', $instrument) )
        {// инструмент выбран из списка
            $this->value = $instrument;
            return;
        }
        
        $this->value = 'custom';
    }

    /**
     * Геттер для поля "музыкальный инструмент" - используется для получения значения типа танца
     * (неважно, стандартного или добавленного пользователем)
     * @return string
     */
    public function getInstrument()
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
     * Получить название инструмента
     * (non-PHPdoc)
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
            $variants = QActivityType::model()->activityVariants('instrument');
            return $variants[$this->value];
        }
    }
    
    /**
     * Сохранить название инструмента
     * @param string $name
     */
    public function setName($name)
    {
        if ( QActivityType::model()->isStandardComplexValue('instrument', $this->value) )
        {// инструмент выбран из выпадающего списка - не вносим его в базу
            $this->uservalue = null;
            return;
        }
    
        // если инструмент введен вручную - то попробуем сначала найти его у нас по названию
        $condition = ' name = "instrument" AND translation = :name ';
        $params    = array(':name' => $name );
        if ( $instrument = QActivityType::model()->find($condition, $params) )
        {
            $this->value = $instrument->value;
            return;
        }
    
        // Введет тот инструмент, которого нет у нас в списке
        $this->value     = 'custom';
        $this->uservalue = strip_tags($name);
    }

    /**
     * Сохранить введенный пользователем инструмент
     * @param string $customtype - введенное пользователем значение
     */
    public function setcustominstrument($customtype)
    {
        $this->value     = 'custom';
        $this->uservalue = $customtype;
    }

    /**
     * Данные для создания формы одного стандартного музыкального инструмента которым владеет пользователь
     * при помощи расширения multiModelForm
     * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
     *
     * @return array
     */
    public function formConfig()
    {
        return array(
            'elements'=>array(
                // музыкальный инструмент
                'instrument'=>array(
                    'type'    => 'ext.combobox.EJuiComboBox',
                    'data'    => QActivityType::model()->activityVariants('instrument'),
                    'textFieldName' => 'name',
                    'textFieldAttribute' => 'name',
                    'assoc'   => true,
                    'visible' => true,
                ),
                // уровень владения
                'level'=>array(
                    'type'    =>'dropdownlist',
                    'items'   => $this->levelList(),
                    'prompt'  => Yii::t('coreMessages','choose'),
                    'visible' => true, // multimodel form hack
                ),
            ));
    }
}
