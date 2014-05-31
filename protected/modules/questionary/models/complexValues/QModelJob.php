<?php

Yii::import('application.modules.questionary.models.QActivity');
Yii::import('application.modules.questionary.extensions.behaviors.QSaveYearBehavior');

/**
 * Класс для работы с одним показом модели
 */
class QModelJob extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'modeljob';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "modeljobs",
            'condition' => "`modeljobs`.`type`='modeljob'",
            'order'     => "`modeljobs`.`timeend` DESC",
        );
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
            array('job', 'length', 'max' => 255 ),
            array('job', 'filter', 'filter' => 'trim'),
            array('job', 'required'),
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
            'job'  => QuestionaryModule::t('model_job_label'),
            'name' => QuestionaryModule::t('model_job_label'),
            'year' => QuestionaryModule::t('year_label'),
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
                'class' => 'questionary.extensions.behaviors.QSaveYearBehavior'
            ),
        );
        return CMap::mergeArray($parentBehaviors, $newBehaviors);
    }

    /**
     * Получить мероприятие ведущего
     * @return mixed
     */
    public function getjob()
    {
        return $this->uservalue;
    }

    /**
     * Установить мероприятие ведущего
     * @param $event
     */
    public function setjob($event)
    {
        $this->uservalue = $event;
    }
}