<?php

Yii::import('application.modules.questionary.models.QActivity');
/**
 * Класс для работы с одним иностранным языком, которым владеет пользователь
 */
class QLanguage extends QActivity
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "languages",
            'condition' => "`languages`.`type`='language'",
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

        // создаем новые правила проверки для полей "язык" и "уровень владения"
        $customRules = array(
            // поле "язык"
            //array('language, name', 'length', 'max'=>255),
            //
            // поле "уровень владения"
            //array('level', 'length', 'max'=>255),
            //array('level', 'required'),
            
            array('language', 'length', 'max'=>255),
            //array('language, name', 'filter', 'filter'=>'trim'),
            array('language', 'required'),
            
            array('name', 'length', 'max'=>255),
            array('name', 'filter', 'filter'=>'trim'),
            
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
            'language'         => QuestionaryModule::t('language_label'),
            'customlanguage'   => QuestionaryModule::t('customlanguage_label'),
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
            $this->type      = 'language';
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
     * Сеттер для поля "язык" - используется, если нужно сохранить стандартное значение из списка
     * @param string $language - значение из выпадающего списка (короткое название латинскими буквами)
     */
    public function setlanguage($language)
    {
        if ( ! trim($language) OR $language == 'custom' )
        {// Указан собственный язык
            $this->value = 'custom';
            return;
        }
        if ( QActivityType::model()->isStandardComplexValue('language', $language) )
        {// язык выбран из списка
            $this->value = $language;
            return;
        }
    
        $this->value = 'custom';
    }
    
    /**
     * Геттер для поля "язык" - используется для получения значения иностранного языка
     * (неважно, стандартного или добавленного пользователем)
     * @return string
     */
    public function getLanguage()
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
     * Получить уровень владения языком
     * 
     * @return null
     */
    public function getLevel()
    {
        if ( $this->scenario == 'view' )
        {
            return $this->getDefaultValueForDisplay('languagelevel', $this->level);
        }
        
        return $this->level;
    }

    /**
     * Получить название языка
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
            $variants = QActivityType::model()->activityVariants('language');
            return $variants[$this->value];
        }
    }
    
    /**
     * Сеттер для поля "иностранный язык" - используется, если нужно сохранить стандартное значение из списка
     * @param string $language - значение из выпадающего списка (короткое название латинскими буквами)
     */
    public function setName($language)
    {
        if ( QActivityType::model()->isStandardComplexValue('language', $language) )
        {// язык выбран из списка
            $this->uservalue = null;
            return;
        }
        
        // если инструмент введен вручную - то попробуем сначала найти его у нас по названию
        $condition = ' name = "language" AND translation = :name ';
        $params    = array(':name' => $language );
        if ( $langType = QActivityType::model()->find($condition, $params) )
        {
            $this->value = $langType->value;
            return;
        }
        
        // Введет тот инструмент, которого нет у нас в списке
        $this->value     = 'custom';
        $this->uservalue = strip_tags($language);
    }

    /**
     * Сохранить введенный пользователем иностранный язык
     * @param string $customtype - введенное пользователем значение
     */
    public function setcustomlanguage($customtype)
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
                // иностранный язык
                'language'=>array(
                    'type'          => 'ext.combobox.EJuiComboBox',
                    'data'          => $this->getFieldVariants('language', false),
                    //'data'          => QActivityType::model()->activityVariants('language'),
                    'textFieldName' => 'name',
                    'textFieldAttribute' => 'name',
                    'assoc'     => true,
                    'visible'   => true, // multimodel form hack
                ),
                // уровень владения
                'level'=>array(
                    'type'    =>'dropdownlist',
                    'items'   => $this->getFieldVariants('languagelevel', false),
                    'prompt'  => Yii::t('coreMessages','choose'),
                    'visible' => true, // multimodel form hack
                ),
            ));
    }
}
