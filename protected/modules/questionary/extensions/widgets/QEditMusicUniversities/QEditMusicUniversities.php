<?php

// подключение родительского класса
Yii::import('questionary.extensions.widgets.QGridEditBase.QGridEditBase');

/**
 * Виджет для списка музыкальных ВУЗов
 *
 * @package    easycast
 * @subpackage questionary
 * 
 * @todo пользователи тупят при вводе ВУЗа. Предлагать изначально 10 самых популярных,
 *       и только потом, при попытке ввести название текстом начинать предлагать autocomplete-варианты
 *       Другое возможное решение: перед select2 поместить radio со списком топ-10 ВУЗов, радио и select2
 *       взаимоисключают друг друга: при выборе значения в одном сбрасывается значение другого 
 */
class QEditMusicUniversities extends QGridEditBase
{
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить этот ВУЗ?';
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl = "/questionary/qMusicUniversity/delete";
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl = "/questionary/qMusicUniversity/create";
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl = "/questionary/qMusicUniversity/update";
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'music_university_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'QMusicUniversity';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('name', 'specialty', 'year', 'workshop');
    /**
     * @var string - html-id формы для ввода новой записи
    */
    public $formId = 'music-university-instance-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'music-university-instance-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-music-university-instance-button';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $addButtonLabel = 'Добавить ВУЗ';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить ВУЗ';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'name'      => '[не указано]',
        'specialty' => '[не указана]',
        'year'      => '[не указан]',
        'workshop'  => '[не указана]',
    );
    /**
     * @var string - адрес по которому происходит получение значений для select2
    */
    public $optionsListUrl = '/questionary/qUniversity/getUniversityList';

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
        $js .= "\$('#{$this->modelClass}_specialty').val('');\n";
        $js .= "\$('#{$this->modelClass}_year').val('');\n";
        $js .= "\$('#{$this->modelClass}_workshop').val('');\n";

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
            // название ВУЗа (нельзя менять если запись уже создана)
            array(
                'name'  => 'name',
                'value' => '$data->name;',
            ),
            // специальность
            $this->getTextColumnOptions('specialty'),
            // год окончания
            $this->getYearColumnOptions('year'),
            // мастерская
            $this->getTextColumnOptions('workshop'),
        );
    }
}