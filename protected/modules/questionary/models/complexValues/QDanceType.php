<?php

Yii::import('application.modules.questionary.models.QActivity');
Yii::import('application.modules.questionary.extensions.behaviors.QSaveSkillLevelBehavior');

/**
 * Класс для работы с одним типом танца (стиль + уровень владения)
 */
class QDanceType extends QActivity
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "dancetypes",
            'condition' => "`dancetypes`.`type`='dancetype'",
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

        // создаем новые правила проверки для полей "тип танца" и "уровень владения"
        $customRules = array(
            // поле "тип танца" ()
            array('dancetype', 'length', 'max'=>255),
            array('dancetype', 'filter', 'filter'=>'trim'),
            
            // поле "тип танца" (если введен свой вариант)
            array('name', 'length', 'max'=>255),
            array('name', 'filter', 'filter'=>'trim'),
            
            // если указан свой вариант - он не должен быть пустым
            array('dancetype', 'ext.YiiConditionalValidator',
                'if' => array(
                    array('name', 'compare', 'compareValue'=>""),
                ),
                'then' => array(
                    array('dancetype', 'required'),
                ),
            ),
            
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
        {
            $this->type = 'dancetype';
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
        return CMap::mergeArray(parent::behaviors(),
                         array('QSaveSkillLevelBehavior',
                             array('class' => 'application.modules.questionary.extensions.behaviors.QSaveSkillLevelBehavior'),
        ));
    }

    /**
     * Сеттер для поля "тип танца" - используется, если нужно сохранить стандартный тип танца
     * @param string $dancetype - предустановленный тип танца (короткое название латинскими буквами)
     */
    public function setdancetype($dancetype)
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
     * (неважно, стандартного или добавленного пользователем)
     * @return string
     */
    public function getdancetype()
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
     * Получить название танца
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
            $variants = QActivityType::model()->activityVariants('dancetype');
            return $variants[$this->value];
        }
    }
    
    /**
     * Сохранить название стиля танца
     * @param string $name
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
        
        // Введет тот стиль танца, которого нет у нас в списке
        $this->value     = 'custom';
        $this->uservalue = strip_tags($name);
    }

    /**
     * Данные для создания формы одного стандартного типа танца которым владеет пользователь
     * при помощи расширения multiModelForm
     * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
     *
     * @return array
     */
    public function formConfig()
    {
        return array(
            'elements'=>array(
                // тип танца
                'dancetype'=>array(
                    'type'    => 'ext.combobox.EJuiComboBox',
                    'data'    => QActivityType::model()->activityVariants('dancetype'),
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
                    'visible' => true,
                ),
            ));
    }
}
