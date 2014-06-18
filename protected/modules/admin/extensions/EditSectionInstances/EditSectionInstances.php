<?php

// родительский класс
Yii::import('ext.EditableGrid.EditableGrid');

/**
 * Виджет для привязки разделов каталога к другим объектам системы
 */
class EditSectionInstances extends EditableGrid
{
    /**
     * @var int - id объекта к которому привязывается раздел анкеты
     */
    public $objectId;
    /**
     * @var string - тип объекта к которому привязывается раздел анкеты
     */
    public $objectType;
    /**
     * @var Category[] - список категорий из которых можно брать разделы
     */
    public $categories = array();
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить запись?';
    /**
     * @var string - если для всех трех действий (create, update, delete) используется один контроллер
     *               то здесь можно указать относительный путь к нему: в этом случае не нужно
     *               отдельно указывать url для каждого действия
     *               Пример значения: '/questionary/qEmcee/'
     */
    public $gridControllerPath = '/admin/sectionInstanceGrid/';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'CatalogSectionInstance';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields  = array('objecttype', 'objectid', 'sectionid');
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить';
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Добавить';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'categoryid'    => '[не указано]',
    );
    /**
     * @var string - id модуля, который хранит клипы с modal-формами
     */
    public $clipModule = 'admin';
    
    /**
     * @see EditableGrid::init()
     */
    public function init()
    {
        Yii::import('application.modules.catalog.models.*');
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
            // выбранная категория
            array(
                'name'  => 'sectionid',
                'value' => '$data->section->name;',
            ),
        );
    }
    
    /**
     * js для очистки полей формы после добавления новой записи
     * @return string
     */
    protected function createClearFormJs()
    {
        $js  = '';
        $js .= "\$('#{$this->modelClass}_sectionid').val('');\n";
    
        return $js;
    }
    
    /**
     * Получить все возможные варианты разделов, которые можно прикрепить к объекту
     * @return array
     */
    protected function getSectionOptions()
    {
        $categoryIds = array();
        // определяем какие категории разделов используются
        if ( ! $this->categories )
        {
            return array('0' => 'Нет');
        }
        foreach ( $this->categories as $category )
        {
            $categoryIds[] = $category->id;
        }
        if ( ! $models = CatalogSection::model()->inCategory($categoryIds)->findAll() )
        {
            return array('0' => 'Нет');
        }
        return CHtml::listData($models, 'id', 'name');
    }
    
    /**
     * @see EditableGrid::getGridCriteria()
     */
    protected function getGridCriteria()
    {
        return array(
            'condition' => "`objectid` = '{$this->objectId}' AND `objecttype` = '{$this->objectType}'",
        );
    }
}