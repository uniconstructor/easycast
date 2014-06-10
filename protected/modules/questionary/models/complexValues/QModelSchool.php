<?php

Yii::import('application.modules.questionary.models.QActivity');
Yii::import('application.modules.questionary.extensions.behaviors.QSaveYearBehavior');

/**
 * Класс для работы с одной модельной школой
 */
class QModelSchool extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'modelschool';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        $parentScope  = parent::defaultScope();
        $currentScope = array(
            'alias'     => "modelschools",
            'condition' => "`modelschools`.`type`='modelschool'",
            'order'     => "`modelschools`.`timeend` DESC",
        );
        return CMap::mergeArray($parentScope, $currentScope);
    }
    
    /**
     * @see parent::rules()
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        // создаем новые правила проверки для полей "год" и "событие"
        $customRules = array(
            array('year', 'numerical', 'integerOnly'=>true),
            
            array('school', 'length', 'max'=>255),
            array('school', 'filter', 'filter'=>'trim'),
            array('school', 'required'),
        );
        return CMap::mergeArray($rules, $customRules);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels    = parent::attributeLabels();
        $newLabels = array(
            'school' => QuestionaryModule::t('model_school_label'),
            'name'   => QuestionaryModule::t('model_school_label'),
            'year'   => QuestionaryModule::t('year_label'),
        );
        return CMap::mergeArray($labels, $newLabels);
    }
    
    /**
     * @see QActivity::behaviors()
     */
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        $newBehaviors    = array(
            'QSaveYearBehavior' => array(
                'class' => 'questionary.extensions.behaviors.QSaveYearBehavior',
            ),
        );
        return CMap::mergeArray($parentBehaviors, $newBehaviors);
    }

    /**
     * @return mixed
     * @deprecated
     */
    public function getschool()
    {
        return $this->uservalue;
    }

    /**
     * @param $school
     * @deprecated
     */
    public function setschool($school)
    {
        $this->uservalue = $school;
    }
}