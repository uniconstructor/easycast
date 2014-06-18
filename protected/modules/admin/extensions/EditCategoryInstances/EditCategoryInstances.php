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
     * @var string - если для всех трех действий (create, update, delete) используется один контроллер
     *               то здесь можно указать относительный путь к нему: в этом случае не нужно
     *               отдельно указывать url для каждого действия
     *               Пример значения: '/questionary/qEmcee/'
     */
    public $gridControllerPath = '/admin/categoryInstanceGrid/';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'CategoryInstance';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('objecttype', 'objectid', 'categoryid');
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Задать категории';
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Задать';
    /**
     * @var array - массив настроек виджета TbButton для кнопки "добавить"
     */
    public $addButtonOptions = array(
        'type' => 'primary',
    );
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
            'condition' => "`objectid` = '{$this->objectId}' AND 
                `objecttype` = '{$this->objectType}' AND 
                category.parentid = '{$this->parentId}'",
            'with'      => array('category'),
            'together'  => true,
        );
    }
}