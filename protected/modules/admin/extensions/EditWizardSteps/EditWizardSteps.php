<?php

// родительский класс
Yii::import('ext.EditableGrid.EditableGrid');

class EditWizardSteps extends EditableGrid
{
    /**
     * @var string - тип объекта к которому добавляется шаг регистрации 
     */
    public $objectType = 'vacancy';
    /**
     * @var int - id объекта к которому добавляется шаг регистрации
     */
    public $objectId;
    /**
     * @var string - сообщение перед удалением записи
    */
    public $deleteConfirmation = 'Удалить шаг регистрации?';
    /**
     * @var string - если для всех трех действий (create, update, delete) используется один контроллер
     *               то здесь можно указать относительный путь к нему: в этом случае не нужно
     *               отдельно указывать url для каждого действия
     *               Пример значения: '/questionary/qEmcee/'
     */
    public $gridControllerPath = '/admin/wizardStep/';
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'wizard_step_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'WizardStep';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('name', 'header', 'description');
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить шаг регистрации';
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Добавить';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'name'        => '[без названия]',
        'header'      => '[не указано]',
        'description' => '[не указано]',
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
            throw new CException('Не передан тип или id объекта для привязки шага регистрации');
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
    /*protected function initModel()
    {
        $this->model = new $this->modelClass;
    }*/
    
    /**
     * Получить настройки для колонок таблицы с данными
     * @return array
     */
    protected function getDataColumns()
    {
        return array(
            // название шага
            $this->getTextColumnOptions('name'),
            // заголовок
            $this->getTextColumnOptions('header'),
            // описание
            $this->getTextAreaColumnOptions('description'),
            // список полей (анкета)
            /*array(
                'name'     => 'userfields',
                'class'    => 'bootstrap.widgets.TbEditableColumn',
                //'value'    => '$data->getFieldList();',
                'editable' => array(
                    'type'      => 'select',
                    'title'     => 'Список полей',
                    'url'       => $this->gridControllerPath.'setUserFields',
                    'emptytext' => '[пусто]',
                    'source'    => $this->getAvailableFields('user'),
                    'params' => array(
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                    ),
                ),
            ),*/
            /*// список полей (заявка)
            array(
                'name'     => 'extrafields',
                'class'    => 'bootstrap.widgets.TbEditableColumn',
                'value'    => '"[...]"',
                'editable' => array(
                    'type'      => 'checklist',
                    'title'     => 'Список полей',
                    'url'       => $this->gridControllerPath.'setExtraFields',
                    'emptytext' => '[пусто]',
                    'source'    => $this->getAvailableFields('extra'),
                    'params' => array(
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                    ),
                ),
            ),*/
        );
    }
    
    /**
     * @see EditableGrid::getGridCriteria()
     * @todo использовать именованые группы условий
     */
    protected function getGridCriteria()
    {
        return array(
            //'condition' => "`objectid` = '{$this->objectId}' AND `objecttype` = '{$this->objectType}'",
            'scopes' => array(
                'forObject' => array($this->objectType, $this->objectId),
            ),
        );
    }
    
    /**
     * Получить список полей, которые можно прикрепить к объекту,
     * исключив из него те поля, которые уже к нему прикреплены
     * @return array
     * 
     * @todo исключить все поля которые уже привязаны
     */
    protected function getAvailableFields($type)
    {
        if ( $type === 'user' )
        {
            $fields = QUserField::model()->forObject($this->objectType, $this->objectId)->findAll();
        }else
        {
            $fields = ExtraField::model()->forObject($this->objectType, $this->objectId)->findAll();
        }
        $options = CHtml::listData($fields, 'id', 'label');
    
        foreach ( $fields as $field )
        {
            unset($options[$field->id]);
        }
        return $options;
    }
}