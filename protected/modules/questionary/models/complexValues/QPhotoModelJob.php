<?php

Yii::import('application.modules.questionary.models.QActivity');
Yii::import('application.modules.questionary.extensions.behaviors.QSaveYearBehavior');

/**
 * Класс для работы с одним эпизодом работы фотомоделью
 */
class QPhotoModelJob extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'photomodeljob';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        $parentScope  = parent::defaultScope();
        $currentScope = array(
            'alias'     => "photomodeljobs",
            'condition' => "`photomodeljobs`.`type`='photomodeljob'",
            'order'     => "`photomodeljobs`.`timeend` DESC",
        );
        return CMap::mergeArray($parentScope, $currentScope);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels    = parent::attributeLabels();
        $newLabels = array(
            'job'  => QuestionaryModule::t('photomodel_job_label'),
            'name' => QuestionaryModule::t('photomodel_job_label'),
            'year' => QuestionaryModule::t('year_label'),
        );
        return CMap::mergeArray($labels, $newLabels);
    }

    /**
     * @see parent::behaviors()
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
    public function getjob()
    {
        return $this->uservalue;
    }

    /**
     * @param $event
     * @deprecated
     */
    public function setjob($event)
    {
        $this->uservalue = $event;
    }
}