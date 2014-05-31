<?php

// подключение родительского класса
Yii::import('questionary.extensions.widgets.QGridEditBase.QGridEditBase');

/**
 * Виджет для редактирования опыта работы в театре
 * 
 * @todo включить обратно возможность указывать текущее место работы
 *       задача отложена до окончания проекта "том-модель по-пусски"
 */
class QEditTheatres extends QGridEditBase
{
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить эту запись?';
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl = "/questionary/qTheatreInstance/delete";
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl = "/questionary/qTheatreInstance/create";
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl = "/questionary/qTheatreInstance/update";
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'theatre_instance_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'QTheatreInstance';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('name', 'startyear', 'stopyear', 'director'); // 'currently'
    /**
     * @var string - html-id формы для ввода новой записи
     */
    public $formId = 'theatre-instance-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'theatre-instance-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-theatre-instance-button';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'name'      => '[не указано]',
        'startyear' => '[не указан]',
        'stopyear'  => '[не указан]',
        'director'  => '[не указан]',
    );
    /**
     * @var string - адрес по которому происходит получение значений для select2
     */
    public $optionsListUrl = '/questionary/qTheatreInstance/getTheatreList';
    
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
        $js = '';
    
        $js .= "\$('#{$this->modelClass}_name').select2('val', '');\n";
        $js .= "\$('#{$this->modelClass}_startyear').val('');\n";
        $js .= "\$('#{$this->modelClass}_stopyear').val('');\n";
        $js .= "\$('#{$this->modelClass}_director').val('');\n";
        // сбрасываем кнопку "да/нет" в исходное состояние
        //$js .= "\$('#{$this->modelClass}_currently').val('');\n";
        //$js .= "\$('#{$this->id}_currently_on_button').removeClass('btn-primary');\n";
        //$js .= "\$('#{$this->id}_currently_off_button').removeClass('btn-primary');\n";
    
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
            // название театра (нельзя менять если запись уже создана)
            array(
                'name'  => 'name',
                'value' => '$data->name;',
            ),
            // год начала
            $this->getYearColumnOptions('startyear'),
            // год окончания
            $this->getYearColumnOptions('stopyear'),
            //  текущее место работы
            // @todo колонка отлично работает включить все обратно когда появится время на доработку
            //$this->getToggleColumnOptions('currently', '//questionary/qTheatreInstance/toggle'),
            // режиссер
            $this->getTextColumnOptions('director'),
        );
    }
}