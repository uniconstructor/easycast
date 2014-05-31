<?php

// подключение родительского класса
Yii::import('questionary.extensions.widgets.QGridEditBase.QGridEditBase');

/**
 * Виджет для редактирования мероприятий ведущего
 *
 * @package    easycast
 * @subpackage questionary
 */
class QEditAddChars extends QGridEditBase
{
    /**
     * @var string - url по которому происходит удаление записей
     */
    public $deleteUrl = "/questionary/qAddChar/delete";
    /**
     * @var string - url по которому происходит создание записей
     */
    public $createUrl = "/questionary/qAddChar/create";
    /**
     * @var string - url по которому происходит обновление записей
     */
    public $updateUrl = "/questionary/qAddChar/update";
    /**
     * @var string - префикс html-id для каждой строки таблицы (чтобы можно было удалять строки)
     */
    public $rowIdPrefix = 'addchar_row_';
    /**
     * @var string - пустой класс модели (для создания формы добавления объекта)
     */
    public $modelClass = 'QAddChar';
    /**
     * @var array - список редактируемых полей в том порядке, в котором они идут в таблице
     */
    public $fields = array('name', 'comment');
    /**
     * @var string - html-id формы для ввода новой записи
    */
    public $formId = 'addchar-form';
    /**
     * @var string - html-id modal-окна для ввода новой записи
     */
    public $modalId = 'addchar-modal';
    /**
     * @var string - html-id кнопки для ввода новой записи
     */
    public $addButtonId = 'add-addchar-button';
    /**
     * @var string - заголовок всплывающего окна с формой добавления новой записи
     */
    public $modalHeader = 'Добавить характеристику';
    /**
     * @var string - надпись на кнопке добавления новой записи
     */
    public $addButtonLabel = 'Добавить';
    /**
     * @var array - список текстов-заглушек, которые отображаются в случае, когда поле не заполнено
     */
    public $emptyTextVariants = array(
        'name'    => '[не указано]',
        'comment' => '[...]',
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
        $js .= "\$('#{$this->modelClass}_name').select2('val', '');\n";
        $js .= "\$('#{$this->modelClass}_comment').val('');\n";
        return $js;
    }
    
    /**
     * Получить настройки для колонок таблицы с данными
     * @return array
     */
    protected function getDataColumns()
    {
        return array(
            // навык
            array(
                'name'  => 'name',
                'value' => '$data->name;',
            ),
            // уровень владения
            $this->getTextAreaColumnOptions('comment'),
        );
    }
}