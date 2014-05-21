<?php

// подключение родительского класса
Yii::import('questionary.extensions.widgets.QGridEditBase.QGridEditBase');

/**
 * Виджет для редактирования опыта работы промо-моделью
 *
 * @package    easycast
 * @subpackage questionary
 */
class QEditPromoModelJobs extends QGridEditBase
{
    /**
     * @var string - сообщение перед удалением записи
     */
    public $deleteConfirmation = 'Удалить эту запись?';
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl = "/questionary/qPromoModelJob/delete";
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl = "/questionary/qPromoModelJob/create";
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl = "/questionary/qPromoModelJob/update";
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'promomodeljob_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'QPromoModelJob';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('job', 'year');
    /**
     * @var string - html-id формы для ввода новой записи
     */
    public $formId = 'promomodeljob-instance-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'promomodeljob-instance-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-promomodeljob-instance-button';
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
        'job'  => '[не указано]',
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
            $this->getTextColumnOptions('job'),
            // год
            $this->getYearColumnOptions('year'),
        );
    }
}