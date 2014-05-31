<?php

// родительский класс сложных значений
Yii::import('questionary.models.QActivity');

/**
 * Класс для работы с одним типом танца (стиль + уровень владения)
 * Все используемые классы подключаются в родительском init()
 */
class QDanceType extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'dancetype';
    
    /**
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "dancetypes",
            'condition' => "`dancetypes`.`type` = 'dancetype'",
        );
    }
    
    /**
     * @see parent::rules()
     * @return array
     */
    public function rules()
    {
        // берем все общие проверки из базового класса
        $rules = parent::rules();
        $customRules = array(
            // поле "уровень владения (професионал/непрофессионал)"
            array('level', 'in', 'range' => array('amateur', 'professional'), 'allowEmpty' => false),
            array('level', 'required'),
        );
        // совмещаем старые правила проверки с новыми
        return CMap::mergeArray($rules, $customRules);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels    = parent::attributeLabels();
        $newLabels = array(
            'dancetype'  => QuestionaryModule::t('dancetype_label'),
            'name'       => QuestionaryModule::t('dancetype_label'),
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
                'class' => 'questionary.extensions.behaviors.QSaveSkillLevelBehavior',
            ),
        );
        // совмещаем с уже прикрепленными
        return CMap::mergeArray($parentBehaviors, $newBehaviors);
    }
}
