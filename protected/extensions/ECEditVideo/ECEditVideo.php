<?php

// подключение родительского класса
Yii::import('questionary.extensions.widgets.QGridEditBase.QGridEditBase');

/**
 * Виджет для редактирования списка видео
 * 
 * @package easycast
 */
class ECEditVideo extends QGridEditBase
{
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить это видео?';
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl = "/video/delete";
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl = "/video/create";
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl = "/video/update";
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'video_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass  = 'Video';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields  = array('name', 'link');
    /**
     * @var string - html-id формы для ввода новой записи
    */
    public $formId  = 'video-instance-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'video-instance-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-video-instance-button';
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
     * @see QGridEditBase::initModel()
     */
    public function initModel()
    {
        //$className = $this->modelClass;
        $this->model = new $this->modelClass;
        $this->model->objecttype = 'questionary';
        $this->model->objectid   = $this->questionary->id;
        
        return $this->model;
    }
    
    /**
     * @see QGridEditBase::getGridCriteria()
     */
    protected function getGridCriteria()
    {
        return array(
            'condition' => "`objecttype` = 'questionary' AND `objectid` = '{$this->questionary->id}'",
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
        );
    }
}