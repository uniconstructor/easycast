<?php

Yii::import('application.modules.questionary.models.QActivity');
Yii::import('application.modules.questionary.extensions.behaviors.QSaveYearBehavior');

/**
 * Класс для работы с информацией о мероприятиях ведущего
 */
class QEmcee extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'emcee';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "emceelist",
            'condition' => "`emceelist`.`type`='emcee'",
            'order'     => "`emceelist`.`timeend` DESC",
        );
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels    = parent::attributeLabels();
        $newLabels = array(
            'name'      => QuestionaryModule::t('emcee_event_label'),
            'event'     => QuestionaryModule::t('emcee_event_label'),
            'uservalue' => QuestionaryModule::t('emcee_event_label'),
            'year'      => QuestionaryModule::t('year_label'),
        );
        return CMap::mergeArray($labels, $newLabels);
    }
    
    /**
     * @see QActivity::behaviors()
     */
    public function behaviors()
    {
        return array(
            'QSaveYearBehavior' => array(
                'class' => 'questionary.extensions.behaviors.QSaveYearBehavior',
            ),
        );
    }
}