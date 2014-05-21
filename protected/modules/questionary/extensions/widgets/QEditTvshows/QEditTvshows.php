<?php

// подключение родительского класса
Yii::import('questionary.extensions.widgets.QGridEditBase.QGridEditBase');

class QEditTvshows extends QGridEditBase
{
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить эту запись?';
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl = "/questionary/qTvshowInstance/delete";
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl = "/questionary/qTvshowInstance/create";
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl = "/questionary/qTvshowInstance/update";
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'tvshow_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'QTvshowInstance';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('channelname', 'projectname', 'startyear', 'stopyear');
    /**
     * @var string - html-id формы для ввода новой записи
    */
    public $formId = 'tvshow-instance-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'tvshow-instance-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-tvshow-instance-button';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'channelname' => '[не указан]',
        'projectname' => '[не указан]',
        'startyear'   => '[не указан]',
        'stopyear'    => '[не указан]',
    );
    
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
     * js для очистки полей формы после добавления новой записи
     * @return string
     */
    protected function createClearFormJs()
    {
        $js  = '';
        $js .= "\$('#{$this->modelClass}_channelname').val('');\n";
        $js .= "\$('#{$this->modelClass}_projectname').val('');\n";
        $js .= "\$('#{$this->modelClass}_startyear').val('');\n";
        $js .= "\$('#{$this->modelClass}_stopyear').val('');\n";
    
        return $js;
    }
    
    /**
     * Получить настройки для колонок таблицы с данными
     *
     * @return array
     */
    protected function getDataColumns()
    {
        return array(
            // название телеканала
            array(
                'name'  => 'channelname',
                'value' => '$data->channelname;',
            ),
            // название телепроекта
            array(
                'name'  => 'projectname',
                'value' => '$data->projectname;',
            ),
            // год начала
            $this->getYearColumnOptions('startyear'),
            // год окончания
            $this->getYearColumnOptions('stopyear'),
        );
    }
}