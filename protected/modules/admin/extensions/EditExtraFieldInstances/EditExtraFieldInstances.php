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
    public $fields = array('name', 'filling');
    /**
     * @var string - html-id формы для ввода новой записи
    */
    public $formId = 'extra-instance-form';
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
        'name'    => '[не указано]',
        'filling' => '[не указано]',
    );
    
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
     * Получить настройки для колонок таблицы с данными
     * @return array
     */
    protected function getDataColumns()
    {
        return array(
            // название мероприятия
            array(
                'name'  => 'name',
                'value' => '$data->name;',
            ),
            // обязательное поле (да/нет)
            $this->getToggleColumnOptions('filling', '//questionary/qTheatreInstance/toggle'),
        );
    }
    
    /**
     * @see EditableGrid::getGridCriteria()
     * @todo использовать именованые группы условий
     */
    protected function getGridCriteria()
    {
        return array(
            'condition' => "`objectid` = '{$this->objectId}' AND `objecttype` = '{$this->objectType}'",
        );
    }
    
    /**
     * Получить список полей, которые можно прикрепить к объекту,
     * исключив из него те поля, которые уже к нему прикреплены
     * @return array
     */
    protected function getFieldIdOptions()
    {
        $fields  = ExtraField::model()->findAll();
        $options = array('' => Yii::t('coreMessages', 'not_set'));
        $options = CMap::mergeArray($options, CHtml::listData($fields, 'id', 'label'));
        
        foreach ( $this->targetObject->extraFields as $field )
        {// ескли к этому объекту уже привязаны какие-либо поля, то не даем привязать их второй раз
            unset($options[$field->id]);
        }
        return $options;
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