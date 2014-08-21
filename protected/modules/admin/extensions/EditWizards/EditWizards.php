<?php

// родительский класс
Yii::import('ext.EditableGrid.EditableGrid');

/**
 * Виджет для редактирования списка пошаговых форм 
 * (например форма регистрации на роль или форма указания дополнительной информации после регистрации)
 * @todo сделать так чтобы последнюю форму регистрации нельзя было удалить
 */
class EditWizards extends EditableGrid
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
    public $deleteConfirmation = 'Удалить пошаговую форму со всем содержимым?
        ВНИМАНИЕ: вся связанные данные, такие как ответы участников также будут удалены.';
    /**
     * @var string - если для всех трех действий (create, update, delete) используется один контроллер
     *               то здесь можно указать относительный путь к нему: в этом случае не нужно
     *               отдельно указывать url для каждого действия
     *               Пример значения: '/questionary/qEmcee/'
     */
    public $gridControllerPath = '/admin/wizardGrid/';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'Wizard';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('name', 'description');
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
    */
    public $modalHeader = 'Создать новую форму';
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Создать новую форму';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'name'        => '[без названия]',
        'description' => '[не указано]',
    );
    /**
     * @var string - id модуля, который хранит клипы с modal-формами
    */
    public $clipModule = 'admin';
    /**
     * @var string
     */
    public $sortableAction = 'admin/wizardGrid/sortable';
    
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
     * Создать пустую модель для формы добавления объекта
     * @return void
     */
    protected function initModel()
    {
        parent::initModel();
        $this->model->objecttype = $this->objectType;
        $this->model->objectid   = $this->objectId;
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
     * Получить настройки для колонок таблицы с данными
     * @return array
     */
    protected function getDataColumns()
    {
        return array(
            // название шага
            $this->getTextColumnOptions('name'),
            // описание
            $this->getTextAreaColumnOptions('description'),
            // @todo общий список обязательных и дополнительных полей (связанная таблица)
        );
    }
    
    /**
     * @see EditableGrid::getGridCriteria()
     * @todo использовать именованые группы условий
     */
    protected function getGridCriteria()
    {
        return array(
            'scopes' => array(
                'forObject' => array($this->objectType, $this->objectId),
            ),
        );
    }
}