<?php

// подключение родительского класса
Yii::import('ext.EditableGrid.EditableGrid');

/**
 * Виджет для редактирования списка видео
 * 
 * @package easycast
 */
class ECEditVideo extends EditableGrid
{
    /**
     * @var string
     */
    public $objectType;
    /**
     * @var int
     */
    public $objectId;
    /**
     * @see EditableGrid::gridControllerPath
     */
    public $gridControllerPath = '/video/';
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить это видео?';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'Video';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields      = array('name', 'link', 'visible');
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Добавить видео';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader    = 'Добавить видео';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'name' => '[не указано]',
        'link' => '[не указана]',
    );
    /**
     * @var string - id модуля, который хранит клипы с modal-формами
     */
    public $clipModule = 'questionary';
    
    /**
     * @see QGridEditBase::initModel()
     */
    public function initModel()
    {
        $this->model = new $this->modelClass;
        $this->model->objecttype = $this->objectType;
        $this->model->objectid   = $this->objectId;
        
        return $this->model;
    }
    
    /**
     * @see QGridEditBase::getGridCriteria()
     */
    protected function getGridCriteria()
    {
        return array(
            'condition' => "`objecttype` = '{$this->objectType}' AND `objectid` = '{$this->objectId}'",
        );
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
        return array(
            // название видео
            $this->getTextColumnOptions('name'),
            // ссылка на видео
            $this->getTextColumnOptions('link'),
            // отображение
            $this->getSelectColumnOptions('visible', $this->model->visibleOptions, 'visibleOption'),
        );
    }
}