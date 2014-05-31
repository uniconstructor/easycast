<?php

// подключение родительского класса
Yii::import('questionary.extensions.widgets.QGridEditBase.QGridEditBase');

/**
 * Виджет для редактирования показов модели
 *
 * @package    easycast
 * @subpackage questionary
 */
class QEditPhotoModelJobs extends QGridEditBase
{
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить эту запись?';
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl = "/questionary/qPhotoModelJob/delete";
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl = "/questionary/qPhotoModelJob/create";
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl = "/questionary/qPhotoModelJob/update";
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'photomodeljob_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'QPhotoModelJob';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('name', 'year');
    /**
     * @var string - html-id формы для ввода новой записи
     */
    public $formId = 'photomodeljob-instance-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'photomodeljob-instance-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-photomodeljob-instance-button';
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
        'name' => '[не указано]',
        'year' => '[не указан]',
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
            // название мероприятия
            $this->getTextColumnOptions('name'),
            // год
            $this->getYearColumnOptions('year'),
        );
    }
}