<?php

// родительский класс
Yii::import('ext.EditableGrid.EditableGrid');

/**
 * Виджет для редактирования списка шагов регистрации на роль
 * @todo использовать TbRelationalColumn для отображения списка полей в каждом шаге
 * @todo сделать проверку: хотя бы один шаг формы должен быть доступен сразу же,
 *       и при этом содержать email
 */
class EditWizardSteps extends EditableGrid
{
    /**
     * @var string - тип объекта к которому добавляется шаг формы 
     */
    public $objectType = 'wizard';
    /**
     * @var int - id объекта к которому добавляется шаг формы
     */
    public $objectId;
    /**
     * @var string - сообщение перед удалением записи
     * @todo Переносить поля в предыдущий/следующий шаг если этот был удален
    */
    public $deleteConfirmation = 'Удалить шаг формы?
        Все поля формы внутри него будут удалены из этой роли.';
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
    public $sortableAction = 'admin/wizardStep/sortable';
    
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
            // собственный текст на кнопке "назад"
            $this->getTextColumnOptions('prevlabel'),
            // собственный текст на кнопке "далее"
            $this->getTextColumnOptions('nextlabel'),
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