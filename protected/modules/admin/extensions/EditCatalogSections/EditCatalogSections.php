<?php
// родительский класс
Yii::import('ext.EditableGrid.EditableGrid');

/**
 * Виджет для редактирования разделов каталога, групп заявок или разделов поиска
 */
class EditCatalogSections extends EditableGrid
{
    /**
     * @var int
     */
    public $categoryId;
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
    public $gridControllerPath = '/admin/catalogSectionGrid/';
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'catalog_section_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'CatalogSection';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array(/*'parentid',*/ 'name', /*'visible'*/);
    /**
     * @var string - html-id формы для ввода новой записи
    */
    public $formId = 'catalog-section-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'catalog-section-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-catalog-section-button';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить раздел';
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Добавить';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'name' => '[не указано]',
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
        $this->model->categoryid = $this->categoryId;
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
            // название
            $this->getTextColumnOptions('name'),
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
        return $js;
    }
    
    /**
     * @see EditableGrid::getGridCriteria()
     * @todo использовать именованые группы условий
     */
    protected function getGridCriteria()
    {
        return array(
            'condition' => "`categoryid` = '{$this->categoryId}'",
        );
    }
}