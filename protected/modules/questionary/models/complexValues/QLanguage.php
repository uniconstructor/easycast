<?php

Yii::import('application.modules.questionary.models.QActivity');

/**
 * Класс для работы с одним иностранным языком, которым владеет пользователь
 */
class QLanguage extends QActivity
{
    /**
     * @var тип деятельности по умолчанию, свой для каждого класса значения, наследуемого от QActivity
     */
    protected $_defaultType = 'language';
    
    /**
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
            // поле "уровень владения"
            array('level', 'filter', 'filter' => 'trim'),
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
            'language'       => QuestionaryModule::t('language_label'),
            'name'           => QuestionaryModule::t('language_label'),
            'customlanguage' => QuestionaryModule::t('customlanguage_label'),
            'level'          => QuestionaryModule::t('level'),
        );
        return CMap::mergeArray($labels, $newLabels);
    }
    
    /**
     * Получить уровень владения языком
     * @return string
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
     * Получить уровень владения языком
     * @return string
     */
    public function getLanguageLevel()
    {
        return $this->getDefaultValueForDisplay('languagelevel', $this->level);
    }
}
