<?php

// родительский класс
Yii::import('ext.EditableGrid.EditableGrid');

/**
 * Виджет для привязки категорий к другим объектам системы
 */
class EditCategoryInstances extends EditableGrid
{
    /**
     * @var int
     */
    public $objectId;
    /**
     * @var string
     */
    public $objectType;
    /**
     * @var int - родительская категория для составления выпадающего списка
     */
    public $parentId = 0;
    /**
     * @var string
     */
    public $categoryType = 'sections';
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
    public $gridControllerPath = '/admin/categoryInstanceGrid/';
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'category_instance_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'CategoryInstance';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('objecttype', 'objectid', 'categoryid');
    /**
     * @var string - html-id формы для ввода новой записи
    */
    public $formId = 'category-instance-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'category-instance-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-category-instance-button';
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
     * @var CActiveRecord - объект к которому привязываются дополнительные поля
     */
    protected $targetObject;
    
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
                'name'  => 'categoryid',
                'value' => '$data->category->name;',
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
        $js .= "\$('#{$this->modelClass}_categoryid').val('');\n";
        
        return $js;
    }
    
    /**
     * 
     * @return array
     */
    protected function getCategoryOptions()
    {
        $models = Category::model()->findAll("`parentid` = {$this->parentId}");
        return CHtml::listData($models, 'id', 'name');
    }
    
    /**
     * @see EditableGrid::getGridCriteria()
     */
    protected function getGridCriteria()
    {
        return array(
            'condition' => "`objectid` = '{$this->objectId}' AND `objecttype` = '{$this->objectType}'",
            //'scopes' => array('withType' => array(''),
        );
    }
}