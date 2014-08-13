<?php

// родительский класс 
Yii::import('ext.EditableGrid.EditableGrid');

/**
 * Редактируемый список дополнительных полей, которые нужно заполнить перед подачей заявки
 * 
 * @property ExtraFieldInstance $model
 * 
 * @todo добавить возможность менять выбранное поле по AJAX
 * @todo добавить разбивку по категориям в select-списке полей
 * @todo добавить настройку "взять значение из другой заявки если пользователь уже однажды заполнял это поле"
 */
class EditExtraFieldInstances extends EditableGrid
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
     * @var Category[] - список категорий из которых можно брать дополнительные поля
     */
    public $categories = array();
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить дополнительное поле?';
    /**
     * @var string - если для всех трех действий (create, update, delete) используется один контроллер
     *               то здесь можно указать относительный путь к нему: в этом случае не нужно
     *               отдельно указывать url для каждого действия
     *               Пример значения: '/questionary/qEmcee/'
     */
    public $gridControllerPath = '/admin/extraFieldInstance/';
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'extra_instance_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'ExtraFieldInstance';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields;
    /**
     * @var string - html-id формы для ввода новой записи
    */
    public $formId  = 'extra-instance-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'extra-instance-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-extra-instance-button';
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
        //'default' => '[не указано]',
    );
    /**
     * @var string - id модуля, который хранит клипы с modal-формами
     */
    public $clipModule = 'admin';
    /**
     * @var string
     */
    public $sortableAction = 'admin/extraFieldInstance/sortable';
    
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
        // загружаем объект к которому привязываются дополнительные поля
        $this->loadTargetObject();
        
        if ( $this->bindObjectType === 'wizardstepinstance' )
        {
            $this->fields = array('objectid', 'name', 'filling');
        }else
        {
            $this->fields = array('name', 'filling');
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
        $this->model->objecttype = $this->getBindObjectType();
        $this->model->objectid   = $this->objectId;
    }
    
    /**
     * Получить настройки для колонок таблицы с данными
     * @return array
     */
    protected function getDataColumns()
    {
        $columns = array();
        $columns['form'] = array(
            // @todo возможность редактировать название
            // название поля
            array(
                'name'  => 'fieldid',
                'value' => '$data->fieldObject->label;',
            ),
            // обязательное поле (да/нет)
            $this->getStaticSelect2ColumnOptions('filling', ExtraFieldInstance::model()->getFillingModes(), 'fillingMode'),
        );
        $columns['wizard'] = array(
            $this->getSelectColumnOptions('objectid', $this->getWizardStepOptions(), 'stepName'),
            // @todo возможность редактировать название
            // название поля
            array(
                'name'  => 'fieldid',
                'value' => '$data->fieldObject->label;',
            ),
            // обязательное поле (да/нет)
            $this->getStaticSelect2ColumnOptions('filling', ExtraFieldInstance::model()->getFillingModes(), 'fillingMode'),
        );
        if ( $this->objectType === 'vacancy' )
        {
            return $columns[$this->targetObject->regtype];
        }
        return array(
            // @todo возможность редактировать название
            // название поля
            array(
                'name'  => 'fieldid',
                'value' => '$data->fieldObject->label;',
            ),
            // обязательное поле (да/нет)
            $this->getStaticSelect2ColumnOptions('filling', ExtraFieldInstance::model()->getFillingModes(), 'fillingMode'),
        );
    }
    
    /**
     * js для очистки полей формы после добавления новой записи
     * @return string
     */
    protected function createClearFormJs()
    {
        $js  = '';
        $js .= "\$('#{$this->modelClass}_name').val('');\n";
        // сбрасываем кнопку "да/нет" в исходное состояние
        $js .= "\$('#{$this->modelClass}_filling').val('required');\n";
        //$js .= "\$('#{$this->id}_filling_on_button').addClass('btn-primary');\n";
        //$js .= "\$('#{$this->id}_filling_off_button').removeClass('btn-primary');\n";
    
        return $js;
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
        if ( empty($this->categories) )
        {
            $fields = ExtraField::model()->findAll();
        }else
        {
            $categoryIds = array();
            foreach ( $this->categories as $category )
            {
                $categoryIds[] = $category->id;
            }
            $fields = ExtraField::model()->forCategories($categoryIds)->orderByLabel()->findAll();
        }
        $options = array('' => Yii::t('coreMessages', 'not_set'));
        $options = CMap::mergeArray($options, CHtml::listData($fields, 'id', 'label'));
        
        /*foreach ( $this->targetObject->extraFields as $field )
        {// если к этому объекту уже привязаны какие-либо поля, то не даем привязать их второй раз
            unset($options[$field->id]);
        }*/
        return $options;
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
     * @param ExtraField $fieldInstance
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
            case 'vacancy':  $this->targetObject = EventVacancy::model()->findByPk($this->objectId); break;
            case 'category': $this->targetObject = Category::model()->findByPk($this->objectId); break;
        }
    }
}