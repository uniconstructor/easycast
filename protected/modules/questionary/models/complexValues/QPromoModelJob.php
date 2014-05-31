<?php

Yii::import('application.modules.questionary.models.QActivity');
Yii::import('application.modules.questionary.extensions.behaviors.QSaveYearBehavior');

/**
 * Класс для работы с одним мероприятием на котором работала промо-модель
 */
class QPromoModelJob extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'promomodeljob';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "promomodeljobs",
            'condition' => "`promomodeljobs`.`type`='promomodeljob'",
            'order'     => "`promomodeljobs`.`timeend` DESC",
        );
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels    = parent::attributeLabels();
        $newLabels = array(
            'job'  => QuestionaryModule::t('promomodel_job_label'),
            'name' => QuestionaryModule::t('promomodel_job_label'),
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
     * Сохранить работу промо-моделью
     * @param string $event
     * @deprecated
     */
    public function setjob($event)
    {
        $this->uservalue = $event;
    }
}