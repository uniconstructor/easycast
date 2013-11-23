<?php

// подключение родительского класса
Yii::import('questionary.extensions.widgets.QGridEditBase.QGridEditBase');

/**
 * Виджет для редактирования фильмографии
 * 
 * @package    easycast
 * @subpackage questionary
 */
class QEditFilms extends QGridEditBase
{
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить эту запись из фильмографии?';
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl = "/questionary/qFilmInstance/delete";
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl = "/questionary/qFilmInstance/create";
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl = "/questionary/qFilmInstance/update";
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'film_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'QFilmInstance';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('name', 'role', 'year', 'director');
    /**
     * @var string - html-id формы для ввода новой записи
     */
    public $formId = 'film-instance-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'film-instance-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-film-instance-button';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить фильмографию';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'name'     => '[не указано]',
        'role'     => '[не указана]',
        'year'     => '[не указан]',
        'director' => '[не указан]',
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
     * Получить настройки для колонок таблицы с данными
     * 
     * @return array
     */
    protected function getDataColumns()
    {
        return array(
            // название фильма
            $this->getTextColumnOptions('name'),
            // роль
            $this->getTextColumnOptions('role'),
            // год
            $this->getYearColumnOptions('year'),
            // режиссер
            $this->getTextColumnOptions('director'),
        );
    }
}