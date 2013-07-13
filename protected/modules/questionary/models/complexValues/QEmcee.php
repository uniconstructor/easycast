<?php

Yii::import('application.modules.questionary.models.QActivity');
Yii::import('application.modules.questionary.extensions.behaviors.QSaveYearBehavior');

/**
 * Класс для работы с информацией о мероприятиях ведущего
 */
class QEmcee extends QActivity
{
    /**
     * (non-PHPdoc)
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
     * @todo добавить дополнительную проверку для поля "событие"
     * @see parent::rules()
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        // создаем новые правила проверки для полей "год" и "событие"
        $customRules = array(
            array('year', 'numerical', 'integerOnly'=>true),
            array('event', 'length', 'max'=>255 ),
            array('event', 'required'),
        );
        return CMap::mergeArray($rules, $customRules);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'event' => QuestionaryModule::t('emcee_event_label'),
            'year' => QuestionaryModule::t('year_label'),
            );

    }

    /**
     * @see parenr::beforeSave()
     * @return bool
     */
    protected function beforeSave()
    {
        if ( $this->isNewRecord )
        {
            $this->type        = 'emcee';
            $this->value       = null;
            $this->level       = null;
            $this->timestart   = null;
        }

        return parent::beforeSave();
    }

    public function behaviors()
    {
        return array('QSaveYearBehavior',
                     array('class' => 'application.modules.questionary.extensions.behaviors.QSaveYearBehavior'),
        );
    }

    /**
     * Получить мероприятие ведущего
     * @return mixed
     */
    public function getevent()
    {
        return $this->uservalue;
    }

    /**
     * Установить мероприятие ведущего
     * @param $event
     */
    public function setevent($event)
    {
        $this->uservalue = $event;
    }

    /**
     * Данные для создания формы одного экземпляра события при помощи расширения multiModelForm
     * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
     * @return array
     */
    public function formConfig()
    {
        return array(
            'elements'=>array(

                'event'=>array(
                    'type'      => 'text',
                    'maxlength' => 255,
                    'visible'   => true,
                ),

                'year'=>array(
                    'type'    =>'dropdownlist',
                    //it is important to add an empty item because of new records
                    'items'   => $this->yearList(),
                    'visible' => true,
                ),
            ));
    }
}