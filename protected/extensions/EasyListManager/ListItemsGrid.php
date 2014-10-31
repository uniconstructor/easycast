<?php

// родительский класс
Yii::import('ext.EditableGrid.EditableGrid');

/**
 * Виджет для редактирования элементов списка
 */
class ListItemsGrid extends EditableGrid
{
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'EasyListItem';
    /**
     * @var string - если для всех трех действий (create, update, delete) используется один контроллер
     *               то здесь можно указать относительный путь к нему: в этом случае не нужно
     *               отдельно указывать url для каждого действия
     *               Пример значения: '/questionary/qEmcee/'
     */
    public $gridControllerPath = '/admin/easyListItem/';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     *              array('name', 'value');
     *              array('name', 'value', 'description');
     *              array('name', 'objecttype', 'objectfield', 'objectid', 'description');
     */
    public $fields = array('value', 'name', 'description');
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader       = 'Добавить элемент списка';
    /**
     * @var bool - разрешить ли изменять порядок строк перетаскиванием?
     */
    //public $sortableRows      = true;
    /**
     * @var string - поле, по которому производится сортировка
     */
    public $sortableAttribute = 'sortorder';
    /**
     * @var bool - сохранять ли новый порядок строк через AJAX?
     */
    public $sortableAjaxSave  = true;
    /**
     * @var string - путь к обработчику сортировки строк
     */
    public $sortableAction;
    /**
     * @var string - статус с которым в список добавляются новые элементы
     */
    public $newItemStatus = EasyListItem::STATUS_ACTIVE;
    /**
     * @var string - id модуля, который хранит клипы с modal-формами
     */
    public $clipModule = 'admin';
    /**
     * @var EasyList - список для которого редактируются значения
     */
    public $easyList;
    
    /**
     * @see EditableGrid::init()
     */
    public function init()
    {
        if ( ! ($this->easyList instanceof EasyList) )
        {
            throw new CException('Не указан список для редактирования элементов');
        }
        // получаем условия выборки для списка
        $this->criteria = new CDbCriteria();
        $this->criteria->scopes = array(
            'forList' => array($this->easyList->id),
        );
        // получаем модель для создания элементов списка
        $this->modelClass = $this->easyList->itemtype;
        parent::init();
    }
    
    /**
     * @see EditableGrid::initModel()
     */
    protected function initModel()
    {
        parent::initModel();
        $this->model->easylistid = $this->easyList->id;
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
     * 
     * @return array
     */
    protected function getDataColumns()
    {
        $columns = array(
            // служебное название
            $this->getTextColumnOptions('value'),
            // название
            $this->getTextColumnOptions('name'),
        );
        if ( in_array('description', $this->fields) )
        {// описание
            $columns[] = $this->getTextAreaColumnOptions('description');
        }
        return $columns;
    }
    
    /**
     * Получить колонку действий с записями
     * 
     * @return array
     */
    protected function getActionsColumn()
    {
        return array(
            'header'      => '<i class="icon icon-list"></i>&nbsp;',
            'htmlOptions' => array(
                'nowrap' => 'nowrap',
                'style'  => 'text-align:center;',
            ),
            'class'       => 'bootstrap.widgets.TbButtonColumn',
            'template'    => '{view}{delete}',
            'deleteConfirmation' => $this->deleteConfirmation,
            'afterDelete' => $this->createAfterDeleteJs(),
            'buttons' => array(
                'delete' => array(
                    'label' => $this->deleteButtonLabel,
                    'url'   => 'Yii::app()->createUrl("'.$this->deleteUrl.'", array("id" => $data->id))',
                ),
                'view' => array(
                    'label' => $this->viewButtonLabel,
                    'url'   => 'Yii::app()->createUrl("'.$this->viewUrl.'", array("id" => $data->id))',
                ),
            ),
        );
    }
}