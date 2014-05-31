<?php
/**
 * Класс для работы с одним музыкальным инструментом (тип + уровень владения)
 * @todo вынести очистку данных в фильтры модели
 */
class QInstrument extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'instrument';
    
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
        $rules       = parent::rules();
        $customRules = array(
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
        $labels    = parent::attributeLabels();
        $newLabels = array(
            'instrument' => QuestionaryModule::t('instrument_label'),
            'name'       => QuestionaryModule::t('instrument_label'),
            'level'      => QuestionaryModule::t('level'),
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
            'QSaveSkillLevelBehavior' => array(
                'class' => 'questionary.extensions.behaviors.QSaveSkillLevelBehavior'
            ),
        );
        // добавляем функции для работы с уровнем навыка к родительскому набору поведений
        return CMap::mergeArray($parentBehaviors, $newBehaviors);
    }
}