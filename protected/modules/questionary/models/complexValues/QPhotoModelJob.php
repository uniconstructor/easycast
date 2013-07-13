<?php

Yii::import('application.modules.questionary.models.QActivity');
Yii::import('application.modules.questionary.extensions.behaviors.QSaveYearBehavior');

/**
 * Класс для работы с одним эпизодом работы фотомоделью
 */
class QPhotoModelJob extends QActivity
{
    /**
     * (non-PHPdoc)
     * @see CActiveRecord::defaultScope()
     */
    public function defaultScope()
    {
        return array(
            'alias'     => "photomodeljobs",
            'condition' => "`photomodeljobs`.`type`='photomodeljob'",
            'order'     => "`photomodeljobs`.`timeend` DESC",
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
            array('year', 'numerical', 'integerOnly'=>true),
            array('job', 'length', 'max'=>255 ),
            array('job', 'required'),
        );
        return CMap::mergeArray($rules, $customRules);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'job'    => QuestionaryModule::t('photomodel_job_label'),
            'year'   => QuestionaryModule::t('year_label'),
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
            $this->type        = 'photomodeljob';
            $this->value       = null;
            $this->level       = null;
            $this->timestart   = null;
        }

        return parent::beforeSave();
    }

    /**
     * @see parent::behaviors()
     */
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

    /**
     * Данные для создания формы одного экземпляра события при помощи расширения multiModelForm
     * Подробнее см. http://www.yiiframework.com/doc/guide/1.1/en/form.table
     * @return array
     */
    public function formConfig()
    {
        return array(
            'elements'=>array(

                'job'=>array(
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