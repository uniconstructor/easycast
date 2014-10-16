<?php

// родительский класс
Yii::import('ext.EditableGrid.EditableGrid');

/**
 * Виджет для редактирования списка обязательных полей анкеты
 * Эти поля будет предложено ввести участнику при подаче заявки, если они не заполнены
 * 
 * @todo добавить возможность менять выбранное поле по AJAX
 * @todo добавить разбивку по категориям в select-списке полей
 * @todo добавить возможность переопределять название и описание поля
 */
class EditRequiredFields extends EditableGrid
{
    /**
     * @var string - тип объекта к которому добавляется ввода (как правило это роль)
     */
    public $objectType;
    /**
     * @var int - id объекта к которому добавляется ввода
     */
    public $objectId;
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить требование к полю анкеты?';
    /**
     * @var string - если для всех трех действий (create, update, delete) используется один контроллер
     *               то здесь можно указать относительный путь к нему: в этом случае не нужно
     *               отдельно указывать url для каждого действия
     *               Пример значения: '/questionary/qEmcee/'
     */
    public $gridControllerPath = '/admin/qFieldInstance/';
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'qfield_instance_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'QFieldInstance';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields;
    /**
     * @var string - html-id формы для ввода новой записи
    */
    public $formId  = 'qfield-instance-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'qfield-instance-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-qfield-instance-button';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить поле';
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Добавить';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'fieldid' => '[не указано]',
        'filling' => '[не указано]',
    );
    /**
     * @var string - id модуля, который хранит клипы с modal-формами
    */
    public $clipModule = 'admin';
    /**
     * @var string - адрес по которому происходит изменение порядка сортировки элементов по AJAX
     */
    public $sortableAction = 'admin/qFieldInstance/sortable';
    
    /**
     * @var CActiveRecord - объект к которому привязываются дополнительные поля
     */
    protected $targetObject;
    
    /**
     * @see EditableGrid::init()
     */
    public function init()
    {
        if ( ! $this->objectType OR ! $this->objectId )
        {
            throw new CException('Не передан тип или id объекта для привязки поля');
        }
        Yii::import('application.modules.questionary.models.QUserField');
        Yii::import('application.modules.questionary.models.QFieldInstance');
        // загружаем объект к которому привязываются дополнительные поля
        $this->loadTargetObject();
        
        if ( $this->bindObjectType === 'wizardstepinstance' )
        {
            $this->fields = array('objectid', 'name', 'filling', 'data');
        }else
        {
            $this->fields = array('name', 'filling', 'data');
        }
        parent::init();
    }
    
    /**
     * Отобразить поля формы создания новой записи
     *
     * @param TbActiveForm $form
     * @return void
     */
    protected function renderFormFields($form)
    {
        $this->render('_fields', array('model' => $this->model, 'form' => $form));
    }
    
    /**
     * Создать пустую модель для формы добавления объекта
     * @return void
     */
    protected function initModel()
    {
        $this->model = new $this->modelClass;
        $this->model->filling    = 'required';
        $this->model->objecttype = $this->objectType;
        $this->model->objectid   = $this->objectId;
    }
    
    /**
     * js для очистки полей формы после добавления новой записи
     * @return string
     */
    protected function createClearFormJs()
    {
        $js  = '';
        $js .= "\$('#{$this->modelClass}_name').val('');\n";
        $js .= "\$('#{$this->modelClass}_filling').val('required');\n";
        $js .= "\$('#{$this->modelClass}_data').val('');\n";
        
        return $js;
    }
    
    /**
     * Получить настройки для колонок таблицы с данными
     * @return array
     */
    protected function getDataColumns()
    {
        $columns = array();
        $columns['form'] = array(
            // название поля
            // @todo возможность редактировать название
            array(
                'name'  => 'fieldid',
                'value' => '$data->name;',
            ),
            // обязательное поле (да/нет/задано автоматически)
            $this->getStaticSelect2ColumnOptions('filling', QFieldInstance::model()->getFillingModes(), 'fillingMode'),
            // автоматическое значение (если есть)
            $this->getTextColumnOptions('data'),
        );
        $columns['wizard'] = array(
            // шаг регистрации
            $this->getSelectColumnOptions('objectid', $this->getWizardStepOptions(), 'stepName'),
            // @todo возможность редактировать название
            // название поля
            array(
                'name'  => 'fieldid',
                'value' => '$data->name;',
            ),
            // обязательное поле (да/нет/задано автоматически)
            $this->getStaticSelect2ColumnOptions('filling', QFieldInstance::model()->getFillingModes(), 'fillingMode'),
            // автоматическое значение (если есть)
            $this->getTextColumnOptions('data'),
        );
        if ( $this->objectType === 'vacancy' )
        {
            return $columns[$this->targetObject->regtype];
        }
        return array(
            // название поля
            // @todo возможность редактировать название
            array(
                'name'  => 'fieldid',
                'value' => '$data->name;',
            ),
            // обязательное поле (да/нет/задано автоматически)
            $this->getStaticSelect2ColumnOptions('filling', QFieldInstance::model()->getFillingModes(), 'fillingMode'),
            // автоматическое значение (если есть)
            $this->getTextColumnOptions('data'),
        );
    }
    
    /**
     * @see EditableGrid::getGridCriteria()
     * @todo использовать именованые группы условий
     */
    protected function getGridCriteria()
    {
        if ( $this->bindObjectType === 'wizardstepinstance' )
        {// поля привязываются к разделам формы регистрации
            $ids = array_keys($this->getWizardStepOptions());
            return array(
                'scopes' => array(
                    'forObjects' => array('wizardstepinstance', $ids),
                ),
                'order' => "`sortorder` ASC",
            );
        }
        return array(
            'condition' => "`objectid` = '{$this->objectId}' AND `objecttype` = '{$this->objectType}'",
            'order'     => "`sortorder` ASC",
        );
    }
    
    /**
     * Получить список полей, которые можно прикрепить к объекту,
     * исключив из него те поля, которые уже к нему прикреплены
     * @return array
     */
    protected function getFieldIdOptions()
    {
        $fields  = QUserField::model()->findAll();
        $options = CHtml::listData($fields, 'id', 'label');
    
        /*foreach ( $this->targetObject->userFields as $field )
        {// ескли к этому объекту уже привязаны какие-либо поля, то не даем привязать их второй раз
            unset($options[$field->id]);
        }*/
        asort($options);
        
        return CMap::mergeArray(array('' => Yii::t('coreMessages', 'not_set')), $options);
    }
    
    /**
     * 
     * @return string
     */
    protected function getBindObjectType()
    {
        if ( $this->objectType === 'vacancy' AND $this->targetObject->regtype === 'wizard' )
        {
            return 'wizardstepinstance';
        }
        return $this->objectType;
    }
    
    /**
     * Получить список шагов в форме регистрации на эту роль, к которым можно привязать поле
     * @return void
     */
    protected function getWizardStepOptions()
    {
        $steps   = WizardStepInstance::model()->forObject($this->objectType, $this->objectId)->findAll();
        $options = CHtml::listData($steps, 'id', 'step.name');
        
        return $options;
    }
    
    /**
     * Получить id этапа регистрации (WizardStepInstance), к которому прикреплено это поле
     * @param QUserField $fieldInstance
     * @return int
     */
    protected function getFieldStep($fieldInstance)
    {
        if ( $stepInstance = $fieldInstance->getLinkedStepInstance() )
        {
            return $stepInstance->id;
        }
    }
    
    /**
     * Получить модель, к которой будут прикреплены критерии поиска
     * @return void
     */
    protected function loadTargetObject()
    {
        switch ( $this->objectType )
        {
            case 'vacancy': $this->targetObject = EventVacancy::model()->findByPk($this->objectId); break;
        }
    }
}