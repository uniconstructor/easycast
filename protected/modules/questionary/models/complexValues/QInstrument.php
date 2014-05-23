<?php
/**
 * Класс для работы с одним музыкальным инструментом (тип + уровень владения)
 * @todo вынести очистку данных в фильтры модели
 */
class QInstrument extends QActivity
{
    /**
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
            array('instrument', 'length', 'max' => 255 ),
            array('instrument', 'required'),
            // алиас для поля "инструмент"
            array('name', 'length', 'max' => 255),
            array('name', 'filter', 'filter' => 'trim'),
            
            // поле "уровень владения (професионал/непрофессионал)"
            array('level', 'in', 'range' => array('amateur', 'professional'), 'allowEmpty' => false),
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
        $newBehaviors    = array(
            'QSaveSkillLevelBehavior' => array(
                'class' => 'questionary.extensions.behaviors.QSaveSkillLevelBehavior'
            ),
        );
        // добавляем функции для работы с уровнем навыка к родительскому набору поведений
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
        // если инструмент введен вручную - то попробуем сначала найти его у нас в списке стандартных по названию
        // (на случай если инструмент введен руками, игнорируя все подсказки формы)
        $criteria = new CDbCriteria();
        $criteria->compare('name', 'instrument');
        $criteria->compare('translation', $name);
        
        if ( $instrument = QActivityType::model()->find($criteria) )
        {// нашлось, ставим стандартное значение (всеми силами сохраняем однородность данных)
            $this->value = $instrument->value;
            return;
        }
    
        // Введет тот инструмент, которого нет у нас в списке
        $this->value     = 'custom';
        $this->uservalue = ECPurifier::trimQuotes(ECPurifier::purify($name));
    }

    /**
     * Сохранить введенный пользователем музыкальный инструмент, которого нет в нашем списке
     * @param string $customtype - введенное пользователем значение
     * 
     * @todo выяснить, нужна ли еще эта функция и если нет - удалить (возможно требовалась только для старого элемента)
     */
    public function setCustomInstrument($customtype)
    {
        $this->value     = 'custom';
        $this->uservalue = ECPurifier::trimQuotes(ECPurifier::purify($customtype));
    }

    /**
     * Данные для создания формы одного стандартного музыкального инструмента которым владеет пользователь
     * при помощи расширения multiModelForm
     * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
     *
     * @return array
     * @deprecated удалить при рефакторинге, использовалось для старого виджета ввода
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
