<?php

/**
 * Виджет для создания и редактирования списка дополнительных полей внутри категории
 */
class EditExtraFields extends EditableGrid
{
    /**
     * @var int - id категории в которую добавляется поле
     */
    public $categoryId = 0;
    /**
     * @var string - если для всех трех действий (create, update, delete) используется один контроллер
     *               то здесь можно указать относительный путь к нему: в этом случае не нужно
     *               отдельно указывать url для каждого действия
     *               Пример значения: '/questionary/qEmcee/'
     */
    public $gridControllerPath = '/admin/extraFieldGrid/';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'ExtraField';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array(/*'categoryid',*/ 'label', 'name', 'type', 'description');
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader    = 'Создать новое поле';
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Создать поле';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'label'       => '[не указано]',
        'name'        => '[не указано]',
        'type'        => '[не указано]',
        'description' => '[не указано]',
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
        Yii::import('catalog.models.*');
        parent::init();
    }
    
    /**
     * Создать пустую модель для формы добавления объекта
     * @return void
     */
    protected function initModel()
    {
        $this->model = new $this->modelClass;
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
     * @see EditableGrid::getActionsColumn()
     */
    protected function getActionsColumn()
    {
        return array(
            'header'      => '<i class="icon icon-list"></i>&nbsp;',
            'htmlOptions' => array('nowrap' => 'nowrap', 'style' => 'text-align:center;'),
            'class'       => 'bootstrap.widgets.TbButtonColumn',
            'template'    => '-',
            'deleteConfirmation' => $this->deleteConfirmation,
            'afterDelete' => $this->createAfterDeleteJs(),
            'buttons'     => array(),
        );
    }
    
    /**
     * Получить настройки для колонок таблицы с данными
     * @return array
     */
    protected function getDataColumns()
    {
        return array(
            // название
            $this->getTextColumnOptions('label'),
            // служебное название
            $this->getTextColumnOptions('name'),
            // тип
            $this->getStaticSelect2ColumnOptions('type', $this->model->getTypeOptions(), 'typeOption'),
            // описание
            $this->getTextAreaColumnOptions('description'),
        );
    }
    
    /**
     * js для очистки полей формы после добавления новой записи
     * @return string
     */
    protected function createClearFormJs()
    {
        $js  = '';
        $js .= "\$('#{$this->modelClass}_label').val('');\n";
        $js .= "\$('#{$this->modelClass}_name').val('');\n";
        $js .= "\$('#{$this->modelClass}_type').val('textarea');\n";
        $js .= "\$('#{$this->modelClass}_description').val('');\n";
        
        return $js;
    }
    
    /**
     * Получить список возможных групп в которые можно добавить созданное поле
     * @return array
     */
    public function getCategoryOptions()
    {
        if ( $models = Category::model()->forParent(5)->findAll() )
        {
            $this->categoryId = $models[0]->id;
        }
        return CHtml::listData($models, 'id', 'name');
    }
    
    /**
     * @see EditableGrid::getGridCriteria()
     */
    protected function getGridCriteria()
    {
        return array();
    }
}